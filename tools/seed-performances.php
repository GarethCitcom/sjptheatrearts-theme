<?php
/**
 * Seed the Performances page.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-performances.php
 *
 * Development tooling, not shipped behaviour. Copy is taken verbatim from
 * design-reference/SJP Performances Alt.dc.html, with two exceptions:
 *
 * - The troupes card named "Notorious, Blaze and Shockwave". Blaze has gone
 *   (Gaz, phase 6), so it names the two that remain.
 * - The show's dates and venue are left empty, so they render "To confirm"
 *   rather than a guess. That is what the design shows too.
 *
 * The video slot is deliberately empty: the brief leaves it open, and the block
 * shows the poster with an honest note rather than a play button that does
 * nothing.
 *
 * Re-running is safe: it replaces the page content outright.
 *
 * @package SJPTheatreArts
 */

// No declare(strict_types=1) here: `wp eval-file` eval()s this file, and a
// declare must be the first statement in a script.

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

require_once SJPTA_DIR . '/tools/seed-lib.php';

$sjpta_page = get_page_by_path( 'performances' );

if ( ! $sjpta_page ) {
	WP_CLI::error( 'No page with the slug "performances".' );
}

$sjpta_join    = get_page_by_path( 'join' );
$sjpta_contact = get_page_by_path( 'contact' );
$sjpta_classes = get_page_by_path( 'classes' );
$sjpta_join    = $sjpta_join ? get_permalink( $sjpta_join ) : home_url( '/join/' );
$sjpta_contact = $sjpta_contact ? get_permalink( $sjpta_contact ) : home_url( '/contact/' );
$sjpta_classes = $sjpta_classes ? get_permalink( $sjpta_classes ) : home_url( '/classes/' );

$sjpta_sections = array();

// ---------- Hero ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/page-hero',
	array(
		'layout'            => array( 'centred', 'field_sjpta_ph_layout' ),
		'breadcrumb'        => array( 'Performances', 'field_sjpta_ph_breadcrumb' ),
		'heading'           => array( 'Nobody sits out the show.', 'field_sjpta_ph_heading' ),
		'heading_highlight' => array( 'the show.', 'field_sjpta_ph_highlight' ),
		'intro'             => array( 'Every student who wants to perform gets a place. Here is what a year at SJP actually looks like on stage.', 'field_sjpta_ph_intro' ),
	)
);

// ---------- The annual show ----------
$sjpta_facts = array(
	array( 'Dates', '' ),
	array( 'Venue', '' ),
	array( 'Who performs', 'Every class' ),
);

$sjpta_fact_rows = array();

foreach ( $sjpta_facts as $sjpta_fact ) {
	$sjpta_fact_rows[] = array(
		'label' => array( $sjpta_fact[0], 'field_sjpta_sf_fact_label' ),
		'value' => array( $sjpta_fact[1], 'field_sjpta_sf_fact_value' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/show-feature',
	array_merge(
		array(
			'eyebrow'    => 'Next on stage',
			'_eyebrow'   => 'field_sjpta_sf_eyebrow',
			'heading'    => 'The annual Christmas show',
			'_heading'   => 'field_sjpta_sf_heading',
			'body'       => 'Two nights, a full theatre and every class in the school on stage. Rehearsals run inside normal lesson time from the autumn term, so no extra evenings are needed.',
			'_body'      => 'field_sjpta_sf_body',
			'cta_label'  => 'Ask about tickets',
			'_cta_label' => 'field_sjpta_sf_cta_label',
			'cta_url'    => $sjpta_contact,
			'_cta_url'   => 'field_sjpta_sf_cta_url',
			'alt_label'  => 'Join in time for it',
			'_alt_label' => 'field_sjpta_sf_alt_label',
			'alt_url'    => $sjpta_join,
			'_alt_url'   => 'field_sjpta_sf_alt_url',
			'photo'      => sjpta_seed_image( 'img_0316' ),
			'_photo'     => 'field_sjpta_sf_photo',
		),
		sjpta_seed_repeater( 'facts', 'field_sjpta_sf_facts', $sjpta_fact_rows )
	)
);

// ---------- Through the year ----------
$sjpta_cards = array(
	array(
		'img_8884',
		'Troupes',
		'purple',
		'Performing troupes',
		'Notorious and Shockwave perform at events and venues outside the school. Past bookings have included Alton Towers Scarefest and Worcester Wolves basketball events.',
	),
	array(
		'img_8010',
		'Holidays',
		'orange',
		'Summer and Easter schools',
		'Multi-day workshops with guest choreographers, open to students from other schools as well as our own. Dates are announced each term.',
	),
	array(
		'xmas-2',
		'Assessments',
		'green',
		'Medal and exam days',
		'A visiting examiner comes to the studio. For many younger students this is the first time they dance for someone who is not their teacher.',
	),
);

$sjpta_card_rows = array();

foreach ( $sjpta_cards as $sjpta_card ) {
	$sjpta_card_rows[] = array(
		'photo' => array( sjpta_seed_image( $sjpta_card[0] ), 'field_sjpta_pc_card_photo' ),
		'badge' => array( $sjpta_card[1], 'field_sjpta_pc_card_badge' ),
		'tone'  => array( $sjpta_card[2], 'field_sjpta_pc_card_tone' ),
		'title' => array( $sjpta_card[3], 'field_sjpta_pc_card_title' ),
		'text'  => array( $sjpta_card[4], 'field_sjpta_pc_card_text' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/photo-cards',
	array_merge(
		array(
			'label'  => 'Through the year',
			'_label' => 'field_sjpta_pc_label',
			'note'   => 'More than one night in December',
			'_note'  => 'field_sjpta_pc_note',
		),
		sjpta_seed_repeater( 'cards', 'field_sjpta_pc_cards', $sjpta_card_rows )
	)
);

// ---------- The film ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/video-feature',
	array(
		'poster' => array( sjpta_seed_image( 'xmas-3' ), 'field_sjpta_vf_poster' ),
		'video'  => array( '', 'field_sjpta_vf_video' ),
		'title'  => array( 'Show night, from the wings', 'field_sjpta_vf_title' ),
		'text'   => array( 'Three minutes of the annual show: the queue at the side of the stage, the count-in, and 40 students hitting the first eight at once.', 'field_sjpta_vf_text' ),
	)
);

// ---------- Gallery ----------
$sjpta_filters     = array( 'On stage', 'In class', 'Troupes' );
$sjpta_filter_rows = array();

foreach ( $sjpta_filters as $sjpta_filter ) {
	$sjpta_filter_rows[] = array( 'label' => array( $sjpta_filter, 'field_sjpta_gm_filter_label' ) );
}

$sjpta_photos = array(
	array( 'img_0300', 'large', 'on-stage' ),
	array( 'img_0318', 'normal', 'on-stage' ),
	array( 'img_8003', 'normal', 'on-stage' ),
	array( 'full-cast', 'wide', 'on-stage' ),
	array( 'img_0305', 'normal', 'on-stage' ),
	array( 'img_8006', 'tall', 'troupes' ),
	array( 'ballet-feet', 'normal', 'in-class' ),
	array( 'singing', 'normal', 'in-class' ),
	array( 'xmas-2', 'normal', 'troupes' ),
	array( 'img_8011', 'wide', 'troupes' ),
);

$sjpta_photo_rows = array();

foreach ( $sjpta_photos as $sjpta_photo ) {
	$sjpta_photo_rows[] = array(
		'photo' => array( sjpta_seed_image( $sjpta_photo[0] ), 'field_sjpta_gm_photo' ),
		'span'  => array( $sjpta_photo[1], 'field_sjpta_gm_span' ),
		'tag'   => array( $sjpta_photo[2], 'field_sjpta_gm_photo_tag' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/gallery-mosaic',
	array_merge(
		array(
			'layout'   => 'bar',
			'_layout'  => 'field_sjpta_gm_layout',
			'eyebrow'  => 'Real students, real nights',
			'_eyebrow' => 'field_sjpta_gm_eyebrow',
			'heading'  => 'Gallery',
			'_heading' => 'field_sjpta_gm_heading',
			'note'     => 'Photographs are published only where a family has given consent. If you would like an image of your child removed, email us and we will take it down.',
			'_note'    => 'field_sjpta_gm_note',
		),
		sjpta_seed_repeater( 'filters', 'field_sjpta_gm_filters', $sjpta_filter_rows ),
		sjpta_seed_repeater( 'photos', 'field_sjpta_gm_photos', $sjpta_photo_rows )
	),
	array( 'anchor' => 'gallery' )
);

// ---------- Closing band ----------
$sjpta_sections[] = sprintf(
	'<!-- wp:sjptheatrearts/cta-band %s /-->',
	wp_json_encode(
		array(
			'heading'        => 'Be in the next one',
			'text'           => 'Students who join in the autumn term are usually on stage by December. Enrol now and be part of it.',
			'primaryLabel'   => 'Enrol now',
			'primaryUrl'     => $sjpta_join,
			'secondaryLabel' => 'Browse classes',
			'secondaryUrl'   => $sjpta_classes,
			'roomy'          => true,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	)
);

sjpta_seed_write( 'performances', $sjpta_sections );

WP_CLI::log( get_permalink( $sjpta_page->ID ) );
