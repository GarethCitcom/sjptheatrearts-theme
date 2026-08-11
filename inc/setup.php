<?php
/**
 * Theme supports, and the guard that keeps the theme from fatalling without ACF Pro.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme supports.
 *
 * Block themes already declare most supports implicitly (title tag, wide align,
 * block templates). Only the extras are declared here.
 *
 * @return void
 */
function sjpta_setup(): void {
	load_theme_textdomain( 'sjptheatrearts', SJPTA_DIR . '/languages' );

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary (floating nav and mobile menu)', 'sjptheatrearts' ),
			'footer'  => __( 'Footer', 'sjptheatrearts' ),
		)
	);

	/*
	 * Two intermediate widths, purely to give srcset finer steps.
	 *
	 * Core jumps 300 -> 768 -> 1024. A photo that a phone draws at roughly 380
	 * or 520 CSS pixels therefore downloads the 768 variant either way, which on
	 * the Born To Be hero cost about 55KB above the fold and a Lighthouse point.
	 * Soft crops, so nothing is cut: they are the same picture, smaller.
	 */
	add_image_size( 'sjpta-480', 480, 0, false );
	add_image_size( 'sjpta-640', 640, 0, false );

	/*
	 * Deliberately NOT adding 'wp-block-styles'. That support loads core's
	 * opinionated default block styles site-wide, which fights theme.json and
	 * costs CSS we have budgeted at under 60KB per page.
	 */
}
add_action( 'after_setup_theme', 'sjpta_setup' );

/**
 * Register the ACF options pages.
 *
 * Site settings hold the facts that appear in the chrome on every page, so they
 * are editable rather than baked into a template part.
 *
 * @return void
 */
function sjpta_acf_options_pages(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'SJP site settings', 'sjptheatrearts' ),
			'menu_title' => __( 'SJP settings', 'sjptheatrearts' ),
			'menu_slug'  => 'sjpta-settings',
			'capability' => 'edit_theme_options',
			'icon_url'   => 'dashicons-star-filled',
			'position'   => 59,
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'sjpta_acf_options_pages' );

/**
 * Re-encode generated image sizes at a quality that matches the source.
 *
 * `npm run media` writes WebP at quality 78, and WordPress then re-encodes every
 * derivative at its own default of 82. Encoding an already-compressed image at a
 * higher quality than the source cannot restore detail; it only spends bytes
 * describing the first encoder's artefacts. Measured on the Born To Be hero: 63KB
 * for a 640px photograph, against 45KB at a quality the eye cannot separate from
 * it.
 *
 * Only WebP is changed. Anything uploaded as JPEG keeps core's default, because
 * that file may well be a camera original with real detail to preserve.
 *
 * @param int    $quality Quality, 1 to 100.
 * @param string $mime    Mime type being written.
 *
 * @return int
 */
function sjpta_webp_quality( $quality, $mime ): int {
	return 'image/webp' === $mime ? 75 : (int) $quality;
}
add_filter( 'wp_editor_set_quality', 'sjpta_webp_quality', 10, 2 );

/**
 * Read a site setting, falling back to a default.
 *
 * Every default here is a fact confirmed in the build brief, never an invented
 * one. Facts that are genuinely unconfirmed default to an empty string so the
 * template renders its "to confirm" state instead.
 *
 * @param string $key      Field name on the options page.
 * @param string $fallback Used when ACF is absent or the field is empty.
 *
 * @return string
 */
function sjpta_setting( string $key, string $fallback = '' ): string {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}

	$value = get_field( $key, 'option' );

	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return $fallback;
	}

	return trim( $value );
}

/**
 * Load core block CSS per block rather than as one bundle.
 *
 * Without this a page ships the whole of wp-block-library. With it, a page only
 * carries CSS for blocks it actually renders. Central to the CSS budget.
 *
 * @return bool
 */
function sjpta_separate_block_assets(): bool {
	return true;
}
add_filter( 'should_load_separate_core_block_assets', 'sjpta_separate_block_assets' );

/**
 * Remove WordPress's own default presets from the generated stylesheet.
 *
 * The `defaultPalette` / `defaultFontSizes` / `defaultPresets` flags in
 * theme.json only hide these in the editor picker and permit theme slugs to
 * override colliding core slugs — verified against WP_Theme_JSON::merge(), where
 * `prevent_override` is consulted only when merging theme presets, never when
 * generating CSS. Core's 19 colours, 11 gradients and 5 shadows are therefore
 * still emitted, along with a `.has-*-color` / `.has-*-background-color` utility
 * class for each. Measured at roughly 8.8KB per page, on every page.
 *
 * Emptying the preset arrays at the default origin drops them: merge() replaces
 * a preset path outright when the incoming value is set, rather than merging
 * into it.
 *
 * @param WP_Theme_JSON_Data $theme_json Default-origin theme.json data.
 *
 * @return WP_Theme_JSON_Data
 */
function sjpta_strip_core_presets( $theme_json ) {
	return $theme_json->update_with(
		array(
			'version'  => 3,
			'settings' => array(
				'color'      => array(
					'palette'   => array(),
					'gradients' => array(),
					'duotone'   => array(),
				),
				'typography' => array(
					'fontSizes' => array(),
				),
				'shadow'     => array(
					'presets' => array(),
				),
				'spacing'    => array(
					'spacingSizes' => array(),
					'spacingScale' => array( 'steps' => 0 ),
				),
			),
		)
	);
}
add_filter( 'wp_theme_json_data_default', 'sjpta_strip_core_presets' );

/**
 * Is ACF Pro available?
 *
 * Checked by capability rather than by plugin path, so it holds however ACF is
 * installed (plugin, mu-plugin or bundled).
 *
 * @return bool
 */
function sjpta_has_acf(): bool {
	return function_exists( 'acf_add_local_field_group' ) && function_exists( 'get_field' );
}

/**
 * Read a field from a post, whether or not ACF is installed.
 *
 * The blocks that take their content from block data already guard with
 * `$sjpta_has_acf`. The ones that read a *post's* fields did not, and a class
 * page fatalled on `get_field()` the moment ACF was deactivated, which broke the
 * acceptance rule that the theme must never fatal without it.
 *
 * Deliberately not a `get_field()` polyfill: defining that name ourselves would
 * shadow the real function if ACF loaded later, and quietly break every field on
 * the site.
 *
 * @param string   $name    Field name.
 * @param int|bool $post_id Post to read from, or false for the current one.
 *
 * @return mixed Null when ACF is unavailable.
 */
function sjpta_get_field( string $name, $post_id = false ) {
	if ( ! sjpta_has_acf() ) {
		return null;
	}

	return get_field( $name, $post_id );
}

/**
 * Warn in the admin when ACF Pro is missing.
 *
 * The theme is required to activate cleanly on a fresh install, so nothing here
 * fatals — the ACF-backed sections simply render their empty state. This notice
 * exists so that degradation is never silent.
 *
 * @return void
 */
function sjpta_acf_missing_notice(): void {
	if ( sjpta_has_acf() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s</p></div>',
		esc_html__( 'SJP Theatre Arts:', 'sjptheatrearts' ),
		esc_html__( 'Advanced Custom Fields Pro is not active. The theme will keep working, but class content, site settings and the timetable will render their empty states until ACF Pro is enabled.', 'sjptheatrearts' )
	);
}
add_action( 'admin_notices', 'sjpta_acf_missing_notice' );

/**
 * Tell ACF to load and save field groups as JSON inside the theme, so field
 * definitions version in git instead of living only in the database.
 *
 * @return void
 */
function sjpta_acf_json_paths(): void {
	if ( ! sjpta_has_acf() ) {
		return;
	}

	$path = SJPTA_DIR . '/acf-json';

	if ( ! is_dir( $path ) ) {
		return;
	}

	add_filter(
		'acf/settings/save_json',
		static function () use ( $path ): string {
			return $path;
		}
	);

	add_filter(
		'acf/settings/load_json',
		static function ( array $paths ) use ( $path ): array {
			$paths[] = $path;

			// ACF's own default is this same directory; de-duplicate rather
			// than replace, so a child theme's path is not discarded.
			return array_values( array_unique( $paths ) );
		}
	);
}
add_action( 'acf/init', 'sjpta_acf_json_paths' );
