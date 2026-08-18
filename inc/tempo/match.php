<?php
/**
 * Matching timetable sessions to class pages.
 *
 * The member portal names sessions by level or year group, because that is what
 * a booking needs to distinguish: "Grade 3 Ballet", "Jazz (Yr4-Yr6)", "Senior
 * Acro Cheer". This site's class pages are the discipline: "Ballet", "Jazz &
 * Commercial", "Acro & Cheer". Of twenty sessions in the Autumn 2026 term,
 * exactly one matched a page by name.
 *
 * So matching is many-to-one and mostly explicit: each class page lists the
 * names it goes by on the portal. Guessing from keywords was considered and
 * rejected, because two of the real sessions name two classes at once
 * ("Rosette Tap & First Steps") and a keyword rule sends those to the wrong
 * page silently, which is worse than not linking them at all.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Portal name to class page, for every published class.
 *
 * Built once per request and cached in an object-cache group, because the
 * timetable calls it for each of twenty rows and the site runs Redis in
 * production.
 *
 * @return array<string,int> Normalised name to post id.
 */
function sjpta_timetable_name_map(): array {
	$cached = wp_cache_get( 'sjpta_timetable_names', 'sjptheatrearts' );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$map = array();

	$classes = get_posts(
		array(
			'post_type'              => SJPTA_CLASS_POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $classes as $class ) {
		// The title always counts, so an exact match needs no configuration.
		$map[ sjpta_timetable_key( (string) $class->post_title ) ] = (int) $class->ID;

		foreach ( (array) sjpta_get_field( 'timetable_names', $class->ID ) as $row ) {
			$name = sjpta_timetable_key( (string) ( $row['name'] ?? '' ) );

			/*
			 * First writer wins. If two classes claim the same portal name, the
			 * earlier one keeps it rather than the later one silently stealing
			 * every row; sjpta_timetable_conflicts() reports the clash.
			 */
			if ( '' !== $name && ! isset( $map[ $name ] ) ) {
				$map[ $name ] = (int) $class->ID;
			}
		}
	}

	wp_cache_set( 'sjpta_timetable_names', $map, 'sjptheatrearts', HOUR_IN_SECONDS );

	return $map;
}

/**
 * Normalise a name so trivial differences do not stop a match.
 *
 * Case, punctuation and "and" versus "&" are not meaningful differences between
 * "Acro & Cheer" and "Acro and Cheer". Anything beyond that is left alone: this
 * tidies a name, it does not guess at one.
 *
 * @param string $name Raw name.
 *
 * @return string
 */
function sjpta_timetable_key( string $name ): string {
	$name = strtolower( trim( $name ) );
	$name = str_replace( '&', ' and ', $name );
	$name = preg_replace( '/[^a-z0-9]+/', ' ', $name );

	return trim( (string) $name );
}

/**
 * The class page a session belongs to, if there is one.
 *
 * @param string $name Session name from the feed.
 *
 * @return int Post id, or 0 when nothing is mapped.
 */
function sjpta_timetable_class_id( string $name ): int {
	$map = sjpta_timetable_name_map();
	$key = sjpta_timetable_key( $name );

	return $map[ $key ] ?? 0;
}

/**
 * Forget the map after a class is saved.
 *
 * Without this, adding a timetable name leaves the row unlinked until the object
 * cache expires, which reads as the field not working.
 *
 * @param int $post_id Post being saved.
 *
 * @return void
 */
function sjpta_timetable_forget_names( $post_id ): void {
	if ( SJPTA_CLASS_POST_TYPE === get_post_type( (int) $post_id ) ) {
		wp_cache_delete( 'sjpta_timetable_names', 'sjptheatrearts' );
	}
}
add_action( 'save_post', 'sjpta_timetable_forget_names' );
add_action( 'deleted_post', 'sjpta_timetable_forget_names' );

/**
 * Sessions in the current feed that link nowhere, and names claimed twice.
 *
 * Not used on the front end. This exists so the state of the mapping can be
 * seen from WP-CLI, because an unlinked row is invisible by design: it simply
 * renders without a "Details" link, which looks intentional whether it is or
 * not.
 *
 * @return array<string,array<int,string>>
 */
function sjpta_timetable_conflicts(): array {
	$timetable = sjpta_timetable();
	$unmatched = array();
	$claimed   = array();
	$twice     = array();

	foreach ( $timetable['sessions'] as $session ) {
		if ( 0 === sjpta_timetable_class_id( $session['name'] ) ) {
			$unmatched[ $session['name'] ] = $session['name'];
		}
	}

	$classes = get_posts(
		array(
			'post_type'      => SJPTA_CLASS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		)
	);

	foreach ( $classes as $class ) {
		foreach ( (array) sjpta_get_field( 'timetable_names', $class->ID ) as $row ) {
			$key = sjpta_timetable_key( (string) ( $row['name'] ?? '' ) );

			if ( '' === $key ) {
				continue;
			}

			if ( isset( $claimed[ $key ] ) ) {
				$twice[] = sprintf( '%s: %s and %s', $row['name'], $claimed[ $key ], $class->post_title );
				continue;
			}

			$claimed[ $key ] = $class->post_title;
		}
	}

	return array(
		'unmatched' => array_values( $unmatched ),
		'claimed'   => $twice,
	);
}
