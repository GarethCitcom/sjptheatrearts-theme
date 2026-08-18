<?php
/**
 * Import classes from the client's information form.
 *
 * `wp sjp import-classes [--file=<path>] [--dry-run] [--update]`
 *
 * The form is still being completed, so this will be run more than once against
 * successive exports. That shapes the whole command:
 *
 *  - **Create-only by default.** An existing class is reported and left alone.
 *  - `--update` overwrites, but prints a per-field diff first, so nobody
 *    discovers after the fact that a re-import undid an editor's wording.
 *  - `--dry-run` writes nothing at all.
 *
 * Classes are matched on the form's own key, stored as `source_key`, not on the
 * title. A client who renames "Jazz & Commercial" should get an updated class,
 * not a second one.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Turn the form's key into a readable class name.
 *
 * The original export had no display names, only slugs, and the client's own
 * wording for a couple of them is not simply the slug with capitals. A class the
 * client adds later does carry a name, which wins.
 *
 * @param string              $key   Form key.
 * @param array<string,mixed> $entry The class, if the caller has it.
 *
 * @return string
 */
function sjpta_class_name_from_key( string $key, array $entry = array() ): string {
	/*
	 * A class the client added carries its own name, and the key is a timestamp.
	 * Without this, "custom-1786292058413" would import as "Custom 1786292058413".
	 */
	$named = trim( (string) ( $entry['name'] ?? '' ) );

	if ( '' !== $named ) {
		return $named;
	}

	$known = array(
		'baby-yoga'       => 'Baby Yoga',
		'parent-toddler'  => 'Parent & Toddler',
		'first-steps'     => 'First Steps',
		'second-steps'    => 'Second Steps',
		'ballet'          => 'Ballet',
		'jazz-commercial' => 'Jazz & Commercial',
		'tap'             => 'Tap',
		'acro-cheer'      => 'Acro & Cheer',
		'musical-theatre' => 'Musical Theatre',
		'drama'           => 'Drama',
		'singing'         => 'Singing',
		'troupes'         => 'Troupes',
		'ballroom-latin'  => 'Ballroom & Latin',
		'adult-ballet'    => 'Adult Ballet',
		'wedding-dance'   => 'Wedding Dance',
	);

	if ( isset( $known[ $key ] ) ) {
		return $known[ $key ];
	}

	// An unknown key means the client added a class. Title-case it and say so.
	return ucwords( str_replace( '-', ' ', $key ) );
}

/**
 * Flatten a list of strings into ACF repeater rows.
 *
 * @param array<int,mixed> $values   Values.
 * @param string           $sub_name Sub-field name.
 *
 * @return array<int,array<string,string>>
 */
function sjpta_class_rows( array $values, string $sub_name ): array {
	$rows = array();

	foreach ( $values as $value ) {
		$value = is_scalar( $value ) ? trim( (string) $value ) : '';

		if ( '' !== $value ) {
			$rows[] = array( $sub_name => $value );
		}
	}

	return $rows;
}

/**
 * Turn the form's class times into readable lines.
 *
 * The shape changes with the data: an empty array when nobody has filled it in,
 * and an object keyed by day when they have:
 *
 *   { "Tuesday": { "start": "8:00pm", "dur": "1Hour" } }
 *
 * Reading it as a flat list, as the first export allowed, silently produced
 * nothing at all, and the class page would have gone on saying "Ask us for
 * times" while the client could see they had supplied them.
 *
 * The day is always included, because the times sit in their own column away
 * from the days and "8:00pm" alone does not say which evening.
 *
 * @param mixed $times The dayTimes value.
 *
 * @return array<int,array<string,string>>
 */
function sjpta_class_time_rows( $times ): array {
	if ( ! is_array( $times ) || empty( $times ) ) {
		return array();
	}

	$rows = array();

	foreach ( $times as $day => $slot ) {
		// A list rather than a map means the client has not filled this in.
		if ( is_int( $day ) || ! is_array( $slot ) ) {
			continue;
		}

		$start = trim( (string) ( $slot['start'] ?? '' ) );
		$dur   = trim( (string) ( $slot['dur'] ?? '' ) );

		if ( '' === $start ) {
			continue;
		}

		// "1Hour" is how the form stores it; "1 hour" is how people read it.
		$dur = strtolower( trim( (string) preg_replace( '/(?<=\d)(?=[A-Za-z])/', ' ', $dur ) ) );

		$rows[] = array(
			'text' => '' === $dur
				? sprintf( '%s %s', $day, $start )
				: sprintf( '%1$s %2$s (%3$s)', $day, $start, $dur ),
		);
	}

	return $rows;
}

/**
 * Map one form entry onto the class field group.
 *
 * @param array<string,mixed> $entry One class from the export.
 *
 * @return array<string,mixed>
 */
function sjpta_class_fields_from_entry( array $entry ): array {
	$wear = is_array( $entry['wear'] ?? null ) ? $entry['wear'] : array();

	$steps = array();
	foreach ( (array) ( $entry['steps'] ?? array() ) as $step ) {
		if ( ! is_array( $step ) ) {
			continue;
		}

		$title = trim( (string) ( $step['title'] ?? '' ) );
		$text  = trim( (string) ( $step['text'] ?? '' ) );

		if ( '' !== $title || '' !== $text ) {
			$steps[] = array(
				'title' => $title,
				'text'  => $text,
			);
		}
	}

	return array(
		'blurb'      => trim( (string) ( $entry['desc'] ?? '' ) ),
		'tags'       => sjpta_class_rows( (array) ( $entry['tags'] ?? array() ), 'text' ),
		'days'       => sjpta_class_rows( (array) ( $entry['days'] ?? array() ), 'text' ),
		'times'      => sjpta_class_time_rows( $entry['dayTimes'] ?? array() ),
		'teachers'   => sjpta_class_rows( (array) ( $entry['teachers'] ?? array() ), 'name' ),
		'level'      => trim( (string) ( $entry['level'] ?? '' ) ),
		'who'        => trim( (string) ( $entry['who'] ?? '' ) ),
		'steps'      => $steps,
		'lead'       => trim( (string) ( $entry['lead'] ?? '' ) ),
		'lead_tags'  => sjpta_class_rows( (array) ( $entry['leadTags'] ?? array() ), 'text' ),
		'wear_items' => sjpta_class_rows( (array) ( $wear['items'] ?? array() ), 'text' ),
		'wear_note'  => trim( (string) ( $wear['note'] ?? '' ) ),

		/*
		 * Deliberately absent: fees. Not in the export at all, so the page shows
		 * its "ask us about fees" state until someone fills the field in.
		 */
	);
}

/**
 * Which age routes and disciplines a class's tags put it in.
 *
 * The form has no taxonomy, only the client's own labels, so the routes are
 * derived from those. Age labels are read literally: "Ages 4 and over" belongs to
 * both the children and teens routes, because it does.
 *
 * **"All ages" means four and over** (Gaz, phase 4), not literally every age.
 * Read literally it put Musical Theatre and Singing in the 2-to-4 route next to
 * Baby Yoga, and showing a parent of a two-year-old Musical Theatre undermines
 * the point of having routes at all. Worth re-checking with the client when they
 * finish the form, since it is their phrase.
 *
 * Tags that are neither age nor discipline ("Beginners welcome", "Private
 * one-to-one", "Invitation only") stay as pills on the class and are not terms:
 * they describe the class, they do not sort it.
 *
 * @param array<int,string> $tags The class's tags.
 *
 * @return array<string,array<int,string>> Taxonomy slug => term slugs.
 */
function sjpta_class_terms_from_tags( array $tags ): array {
	$ages = array(
		'Babies'               => array( 'first-steps' ),
		'Up to 4'              => array( 'first-steps' ),
		'Ages 2 to 4'          => array( 'first-steps' ),
		'Ages 4 and over'      => array( 'children', 'teens' ),
		'Ages 6 and over'      => array( 'children', 'teens' ),
		'Ages 8 and over'      => array( 'children', 'teens' ),
		'All ages'             => array( 'children', 'teens', 'adults' ),
		'Adults (18 and over)' => array( 'adults' ),
	);

	$disciplines = array(
		'Dance'            => 'dance',
		'Singing'          => 'singing',
		'Drama and acting' => 'drama',
		'Fitness'          => 'fitness',
	);

	$routes = array();
	$styles = array();

	foreach ( $tags as $tag ) {
		$tag = trim( (string) $tag );

		if ( isset( $ages[ $tag ] ) ) {
			$routes = array_merge( $routes, $ages[ $tag ] );
		}

		if ( isset( $disciplines[ $tag ] ) ) {
			$styles[] = $disciplines[ $tag ];
		}
	}

	return array(
		'age-group'  => array_values( array_unique( $routes ) ),
		'discipline' => array_values( array_unique( $styles ) ),
	);
}

/**
 * Make sure a term exists, and return its slug.
 *
 * @param string $taxonomy Taxonomy.
 * @param string $slug     Term slug.
 * @param string $name     Display name.
 *
 * @return void
 */
function sjpta_ensure_term( string $taxonomy, string $slug, string $name ): void {
	if ( term_exists( $slug, $taxonomy ) ) {
		return;
	}

	wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
}

/**
 * Create the age routes and disciplines, so neither is an empty box.
 *
 * @return void
 */
function sjpta_seed_class_terms(): void {
	foreach ( sjpta_age_routes() as $slug => $route ) {
		sjpta_ensure_term( 'age-group', $slug, $route['name'] );
	}

	foreach (
		array(
			'dance'   => __( 'Dance', 'sjptheatrearts' ),
			'singing' => __( 'Singing', 'sjptheatrearts' ),
			'drama'   => __( 'Drama and acting', 'sjptheatrearts' ),
			'fitness' => __( 'Fitness', 'sjptheatrearts' ),
		) as $slug => $name
	) {
		sjpta_ensure_term( 'discipline', $slug, $name );
	}
}

/**
 * Put a class into its routes and disciplines.
 *
 * @param int               $id   Class id.
 * @param array<int,string> $tags The class's tags.
 *
 * @return void
 */
function sjpta_assign_class_terms( int $id, array $tags ): void {
	foreach ( sjpta_class_terms_from_tags( $tags ) as $taxonomy => $terms ) {
		wp_set_object_terms( $id, $terms, $taxonomy, false );
	}
}

/**
 * Reduce a stored or incoming value to a form the two can be compared in.
 *
 * Without this the diff is useless: ACF hands back `who` with wpautop's tags
 * wrapped round it, an empty repeater as `false` rather than an empty array, and
 * repeater rows carrying keys the importer never set. Every class then reports
 * as changed on every run, and a real edit is invisible in the noise.
 *
 * @param mixed $value Stored or incoming value.
 *
 * @return mixed
 */
function sjpta_class_normalise( $value ) {
	// ACF returns false for an empty repeater; the importer sends an empty array.
	if ( false === $value || null === $value ) {
		return array();
	}

	if ( is_array( $value ) ) {
		$rows = array();

		foreach ( $value as $row ) {
			if ( ! is_array( $row ) ) {
				$rows[] = trim( (string) $row );
				continue;
			}

			$keep = array();

			foreach ( array( 'title', 'text', 'name' ) as $sub ) {
				if ( isset( $row[ $sub ] ) ) {
					$keep[ $sub ] = trim( (string) $row[ $sub ] );
				}
			}

			$rows[] = $keep;
		}

		return $rows;
	}

	// Compare the words, not the markup a formatted field is returned in.
	$text = wp_strip_all_tags( (string) $value );
	$text = preg_replace( '/[ \t]+/', ' ', $text );
	$text = preg_replace( '/\s*\n\s*/', "\n", (string) $text );

	return trim( preg_replace( '/\n{2,}/', "\n", (string) $text ) ?? '' );
}

/**
 * Describe a field value briefly, for the diff.
 *
 * @param mixed $value Value.
 *
 * @return string
 */
function sjpta_class_describe( $value ): string {
	if ( is_array( $value ) ) {
		/* translators: %d: number of rows. */
		return sprintf( _n( '%d row', '%d rows', count( $value ), 'sjptheatrearts' ), count( $value ) );
	}

	$text = trim( (string) $value );

	if ( '' === $text ) {
		return '(empty)';
	}

	return strlen( $text ) > 48 ? substr( $text, 0, 45 ) . '...' : $text;
}

/**
 * The import command.
 *
 * @param array<int,string>    $args       Positional args.
 * @param array<string,string> $assoc_args Flags.
 *
 * @return void
 */
function sjpta_cli_import_classes( $args, $assoc_args ): void {
	unset( $args );

	$file   = $assoc_args['file'] ?? SJPTA_DIR . '/data/class-info-answers.json';
	$dry    = isset( $assoc_args['dry-run'] );
	$update = isset( $assoc_args['update'] );

	if ( ! file_exists( $file ) ) {
		WP_CLI::error( sprintf( 'No such file: %s', $file ) );
	}

	$raw = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file, not a remote request.

	if ( ! is_array( $raw ) || ! isset( $raw['answers'] ) || ! is_array( $raw['answers'] ) ) {
		WP_CLI::error( 'That file does not look like a class information export (no "answers" object).' );
	}

	sjpta_seed_class_terms();

	WP_CLI::log( sprintf( 'Export updated %s, submitted: %s', $raw['updatedAt'] ?? 'unknown', empty( $raw['sent'] ) ? 'no' : 'yes' ) );

	if ( empty( $raw['sent'] ) ) {
		WP_CLI::warning( 'The client has not submitted this form yet, so the answers may still change.' );
	}

	$created = 0;
	$skipped = 0;
	$updated = 0;
	$removed = 0;

	foreach ( $raw['answers'] as $key => $entry ) {
		if ( ! is_array( $entry ) ) {
			continue;
		}

		$key  = (string) $key;
		$name = sjpta_class_name_from_key( $key, $entry );

		if ( ! empty( $entry['removed'] ) ) {
			/*
			 * Unpublished, not deleted. The client removing a class from the form
			 * is not the same as asking for its page, its photograph and its
			 * history to be destroyed, and a draft can be put back in one click.
			 * Anything linking to it 404s either way.
			 */
			$gone = get_posts(
				array(
					'post_type'        => SJPTA_CLASS_POST_TYPE,
					'post_status'      => 'publish',
					'numberposts'      => 1,
					'fields'           => 'ids',
					'meta_key'         => 'source_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- matching on the import key.
					'meta_value'       => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
					'suppress_filters' => false,
				)
			);

			if ( empty( $gone ) ) {
				WP_CLI::log( sprintf( '  %s: flagged removed, not on the site', $name ) );
			} elseif ( $dry ) {
				WP_CLI::log( sprintf( '  %s: flagged removed, would be unpublished (#%d)', $name, (int) $gone[0] ) );
			} else {
				wp_update_post(
					array(
						'ID'          => (int) $gone[0],
						'post_status' => 'draft',
					)
				);

				WP_CLI::log( sprintf( '  %s: flagged removed, unpublished (#%d)', $name, (int) $gone[0] ) );
			}

			++$removed;
			continue;
		}

		$existing = get_posts(
			array(
				'post_type'        => SJPTA_CLASS_POST_TYPE,
				'post_status'      => 'any',
				'numberposts'      => 1,
				'fields'           => 'ids',
				'meta_key'         => 'source_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- matching on the import key is the whole point.
				'meta_value'       => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
				'suppress_filters' => false,
			)
		);

		$fields = sjpta_class_fields_from_entry( $entry );

		if ( ! empty( $existing ) ) {
			$id = (int) $existing[0];

			if ( ! $update ) {
				WP_CLI::log( sprintf( '  %s: exists (#%d), left alone. Use --update to overwrite.', $name, $id ) );
				++$skipped;
				continue;
			}

			$changes = array();

			foreach ( $fields as $field => $value ) {
				$current = function_exists( 'get_field' ) ? get_field( $field, $id ) : null;

				$before = wp_json_encode( sjpta_class_normalise( $current ) );
				$after  = wp_json_encode( sjpta_class_normalise( $value ) );

				if ( $before !== $after ) {
					$changes[] = sprintf( '      %s: %s -> %s', $field, sjpta_class_describe( $current ), sjpta_class_describe( $value ) );
				}
			}

			/*
			 * Terms are re-derived even when no field changed. They come from the
			 * tags rather than being stored, so a change to the mapping has to be
			 * able to reach classes whose content is identical, and assigning the
			 * same terms twice costs nothing.
			 */
			if ( ! $dry ) {
				sjpta_assign_class_terms( $id, (array) ( $entry['tags'] ?? array() ) );
			}

			if ( empty( $changes ) ) {
				WP_CLI::log( sprintf( '  %s: no change', $name ) );
				++$skipped;
				continue;
			}

			WP_CLI::log( sprintf( '  %s (#%d): %d field(s) would change', $name, $id, count( $changes ) ) );
			foreach ( $changes as $line ) {
				WP_CLI::log( $line );
			}

			if ( $dry ) {
				++$updated;
				continue;
			}

			foreach ( $fields as $field => $value ) {
				update_field( $field, $value, $id );
			}

			sjpta_assign_class_terms( $id, (array) ( $entry['tags'] ?? array() ) );

			++$updated;
			continue;
		}

		if ( $dry ) {
			WP_CLI::log( sprintf( '  %s: would be created', $name ) );
			++$created;
			continue;
		}

		/*
		 * The form key is the slug for the fifteen classes it ships with, because
		 * those keys are already readable. A class the client adds is keyed by a
		 * timestamp, which would have published Adult Jazz Class at
		 * /classes/custom-1786292058413/. Those get their slug from their name.
		 */
		$slug = str_starts_with( $key, 'custom-' ) ? sanitize_title( $name ) : $key;

		$id = wp_insert_post(
			array(
				'post_type'    => SJPTA_CLASS_POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => wp_slash( $name ),
				'post_name'    => $slug,
				'post_excerpt' => wp_slash( trim( (string) ( $entry['desc'] ?? '' ) ) ),
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( sprintf( '  %s: %s', $name, $id->get_error_message() ) );
			continue;
		}

		update_field( 'source_key', $key, $id );

		foreach ( $fields as $field => $value ) {
			update_field( $field, $value, $id );
		}

		sjpta_assign_class_terms( $id, (array) ( $entry['tags'] ?? array() ) );

		WP_CLI::log( sprintf( '  %s: created (#%d)', $name, $id ) );
		++$created;
	}

	WP_CLI::success(
		sprintf(
			'%d created, %d updated, %d left alone, %d flagged removed.%s',
			$created,
			$updated,
			$skipped,
			$removed,
			$dry ? ' (dry run, nothing written)' : ''
		)
	);

	if ( ! $dry ) {
		WP_CLI::log( sprintf( 'Published classes now: %d', sjpta_class_count() ) );
	}
}

WP_CLI::add_command( 'sjp import-classes', 'sjpta_cli_import_classes' );
