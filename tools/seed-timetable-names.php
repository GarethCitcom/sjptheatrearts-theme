<?php
/**
 * Seed the portal-name mappings for the timetable.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-timetable-names.php
 *
 * The member portal names sessions by level or year group and this site's pages
 * are the discipline, so almost nothing matches by name. These are the mappings
 * for the Autumn 2026 term, taken from the live feed on 2026-08-10.
 *
 * Create-only: a class that already has timetable names is left alone, so
 * re-running this after the client has edited anything cannot overwrite them.
 * Pass --update to replace them anyway.
 *
 * Two sessions name two different classes at once, so they were left out until
 * SJ said which page a parent should land on. Her answer, 2026-08-10, is that
 * both belong to the steps class rather than the tap grade, and they are mapped
 * below on that basis. Guessing would have sent a parent to the wrong class.
 *
 * @package SJPTheatreArts
 */

// No declare(strict_types=1) here: `wp eval-file` eval()s this file, and a
// declare must be the first statement in a script.

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Portal session names, by the slug of the class page they belong to.
 *
 * @var array<string,array<int,string>> $sjpta_map
 */
$sjpta_map = array(
	'baby-yoga'       => array( 'Baby Steps Yoga' ),
	'jazz-commercial' => array(
		'Jazz Class 8-13+ Years',
		'Jazz (Yr2-Yr3)',
		'Jazz (Yr4-Yr6)',
		'Jazz (Yr7-Yr8)',
		'Jazz (Yr9+)',
	),
	'acro-cheer'      => array( 'Senior Acro Cheer', 'Intermediate & Junior Acro' ),
	'ballet'          => array( 'Grade 1 Ballet', 'Grade 3 Ballet', 'Prep & Primary Ballet' ),
	'tap'             => array( 'Grade 1 & 2 Tap', 'Grade 3 Tap' ),
	'musical-theatre' => array( 'Junior Musical Theatre', 'Senior Musical Theatre' ),
	'troupes'         => array( 'Notorious Troupe', 'Shockwave Troupe' ),

	/*
	 * The two that name a tap grade and a steps class in the same breath. SJ
	 * confirmed on 2026-08-10 that a parent reading either row wants the steps
	 * class, not the tap page, so these are her answer rather than a guess.
	 */
	'first-steps'     => array( 'Rosette Tap & First Steps' ),
	'second-steps'    => array( 'Prep & Primary Tap and Second Steps' ),
);

$sjpta_update  = in_array( '--update', (array) ( $args ?? array() ), true );
$sjpta_written = 0;
$sjpta_skipped = 0;

foreach ( $sjpta_map as $sjpta_slug => $sjpta_names ) {
	$sjpta_post = get_page_by_path( $sjpta_slug, OBJECT, SJPTA_CLASS_POST_TYPE );

	if ( ! $sjpta_post ) {
		WP_CLI::warning( 'No class page found for "' . $sjpta_slug . '", skipped.' );
		continue;
	}

	$sjpta_existing = (array) get_field( 'timetable_names', $sjpta_post->ID );

	if ( ! empty( $sjpta_existing ) && ! $sjpta_update ) {
		WP_CLI::log( sprintf( '  %-18s already set (%d), left alone', $sjpta_slug, count( $sjpta_existing ) ) );
		++$sjpta_skipped;
		continue;
	}

	$sjpta_rows = array();

	foreach ( $sjpta_names as $sjpta_name ) {
		$sjpta_rows[] = array( 'field_sjpta_cl_timetable_name' => $sjpta_name );
	}

	update_field( 'field_sjpta_cl_timetable_names', $sjpta_rows, $sjpta_post->ID );

	WP_CLI::log( sprintf( '  %-18s %d name(s)', $sjpta_slug, count( $sjpta_rows ) ) );
	++$sjpta_written;
}

wp_cache_delete( 'sjpta_timetable_names', 'sjptheatrearts' );

WP_CLI::success( sprintf( '%d class(es) written, %d left alone.', $sjpta_written, $sjpta_skipped ) );
WP_CLI::log( 'Run `wp sjp timetable` to see what still links nowhere.' );
