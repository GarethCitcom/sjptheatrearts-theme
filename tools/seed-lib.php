<?php
/**
 * Shared helpers for the seed scripts.
 *
 * Development tooling, not shipped behaviour. Loaded by tools/seed-home.php and
 * tools/seed-born-to-be.php with:
 *
 *   require_once SJPTA_DIR . '/tools/seed-lib.php';
 *
 * SJPTA_DIR rather than __DIR__ because WP-CLI's `eval-file` evaluates the
 * calling script, and __DIR__ inside eval()'d code is not the script's own
 * directory.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'sjpta_seed_raw' ) ) {

	/**
	 * Build an ACF block comment from an already-flattened data array.
	 *
	 * @param string              $block Block name.
	 * @param array<string,mixed> $data  Flattened ACF data.
	 * @param array<string,mixed> $attrs Extra top-level block attributes, such as
	 *                                   `anchor`.
	 *
	 * @return string
	 */
	function sjpta_seed_raw( string $block, array $data, array $attrs = array() ): string {
		return sprintf(
			'<!-- wp:%s %s /-->',
			$block,
			wp_json_encode(
				array_merge(
					array(
						'name' => $block,
						'data' => $data,
						'mode' => 'preview',
					),
					$attrs
				),
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
			)
		);
	}

	/**
	 * Build an ACF block comment with its field data.
	 *
	 * ACF stores each field twice: the value under its name, and the field key
	 * under an underscore-prefixed name. Both are required for the editor UI to
	 * bind the value back to its field.
	 *
	 * @param string                                $block  Block name.
	 * @param array<string,array{0:mixed,1:string}> $fields Map of field name => [ value, field key ].
	 * @param array<string,mixed>                   $attrs  Extra top-level attributes.
	 *
	 * @return string
	 */
	function sjpta_seed_block( string $block, array $fields, array $attrs = array() ): string {
		$data = array();

		foreach ( $fields as $name => $pair ) {
			list( $value, $key ) = $pair;

			$data[ $name ]       = $value;
			$data[ '_' . $name ] = $key;
		}

		return sjpta_seed_raw( $block, $data, $attrs );
	}

	/**
	 * Flatten a repeater into ACF's stored shape.
	 *
	 * ACF stores a repeater as a count under the field name, then one entry per
	 * row/sub-field as `<name>_<index>_<subname>`, each shadowed by its field key.
	 *
	 * A sub-field whose value is `array( '__rep' => array( $key, $rows ) )` is
	 * treated as a nested repeater and flattened under the row's own prefix, which
	 * is how the comparison cards carry their list of facts.
	 *
	 * @param string                         $name   Repeater field name.
	 * @param string                         $key    Repeater field key.
	 * @param array<int,array<string,mixed>> $rows   Rows of subname => [ value, key ].
	 * @param string                         $prefix Key prefix, set when nesting.
	 *
	 * @return array<string,mixed>
	 */
	function sjpta_seed_repeater( string $name, string $key, array $rows, string $prefix = '' ): array {
		$out                          = array( $prefix . $name => count( $rows ) );
		$out[ '_' . $prefix . $name ] = $key;

		foreach ( $rows as $i => $row ) {
			foreach ( $row as $sub => $pair ) {
				if ( is_array( $pair ) && isset( $pair['__rep'] ) ) {
					list( $nested_key, $nested_rows ) = $pair['__rep'];

					$out = array_merge(
						$out,
						sjpta_seed_repeater( $sub, $nested_key, $nested_rows, "{$prefix}{$name}_{$i}_" )
					);

					continue;
				}

				list( $value, $sub_key ) = $pair;

				$out[ "{$prefix}{$name}_{$i}_{$sub}" ]  = $value;
				$out[ "_{$prefix}{$name}_{$i}_{$sub}" ] = $sub_key;
			}
		}

		return $out;
	}

	/**
	 * Look up an attachment id by its slug, so a seed does not hard-code ids.
	 *
	 * @param string $slug Attachment slug.
	 *
	 * @return int
	 */
	function sjpta_seed_image( string $slug ): int {
		$found = get_posts(
			array(
				'post_type'        => 'attachment',
				'name'             => $slug,
				'post_status'      => 'inherit',
				'numberposts'      => 1,
				'fields'           => 'ids',
				'suppress_filters' => false,
			)
		);

		if ( empty( $found ) ) {
			WP_CLI::warning( sprintf( 'No attachment found for "%s" — run the media import first.', $slug ) );
			return 0;
		}

		return (int) $found[0];
	}

	/**
	 * Build a block comment for a block that uses plain block attributes.
	 *
	 * The CTA band is not an ACF block: its copy lives in block attributes, so its
	 * values sit at the top level of the comment rather than inside "data".
	 *
	 * @param string              $block Block name.
	 * @param array<string,mixed> $attrs Block attributes.
	 *
	 * @return string
	 */
	function sjpta_seed_attrs( string $block, array $attrs ): string {
		return sprintf(
			'<!-- wp:%s %s /-->',
			$block,
			wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		);
	}

	/**
	 * Write assembled sections into a page, by slug.
	 *
	 * @param string            $slug     Page slug.
	 * @param array<int,string> $sections Block comments, in order.
	 *
	 * @return void
	 */
	function sjpta_seed_write( string $slug, array $sections ): void {
		$page = get_page_by_path( $slug );

		if ( ! $page ) {
			WP_CLI::error( sprintf( 'No page with the slug "%s" was found.', $slug ) );
		}

		/*
		 * wp_update_post() expects slashed data and calls wp_unslash() on the way
		 * in. Without wp_slash() here every backslash is eaten, which silently
		 * turns the JSON escape "\n" inside a block comment into a literal "n".
		 */
		$result = wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => wp_slash( implode( "\n\n", $sections ) ),
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}

		WP_CLI::success( sprintf( 'Seeded "%s" with %d section(s).', $slug, count( $sections ) ) );
	}
}
