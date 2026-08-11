<?php
/**
 * Seed the Contact page.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-contact.php
 *
 * Development tooling, not shipped behaviour. Copy is taken verbatim from
 * design-reference/SJP Contact Alt.dc.html.
 *
 * The four studio facts are not seeded here. Address, parking, step-free access
 * and waiting area live in SJP settings and have been outstanding since phase 1,
 * so the panel shows its "to confirm" state for each until somebody fills them
 * in. Guessing at step-free access is the kind of guess that gets a wheelchair
 * user to a door with a step in front of it.
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

$sjpta_page = get_page_by_path( 'contact' );

if ( ! $sjpta_page ) {
	WP_CLI::error( 'No page with the slug "contact".' );
}

$sjpta_join      = get_page_by_path( 'join' );
$sjpta_timetable = get_page_by_path( 'timetable-and-fees' );
$sjpta_join      = $sjpta_join ? get_permalink( $sjpta_join ) : home_url( '/join/' );
$sjpta_timetable = $sjpta_timetable ? get_permalink( $sjpta_timetable ) : home_url( '/timetable-and-fees/' );

$sjpta_email     = sjpta_setting( 'contact_email', 'sjptheatrearts@yahoo.com' );
$sjpta_instagram = sjpta_setting( 'instagram_url', '' );
$sjpta_portal    = sjpta_setting( 'portal_url', 'https://book.sjptheatrearts.co.uk/' );

$sjpta_sections = array();

// ---------- Hero ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/page-hero',
	array(
		'layout'            => array( 'centred', 'field_sjpta_ph_layout' ),
		'breadcrumb'        => array( 'Contact & visit', 'field_sjpta_ph_breadcrumb' ),
		'heading'           => array( 'Come and find us.', 'field_sjpta_ph_heading' ),
		'heading_highlight' => array( 'find us.', 'field_sjpta_ph_highlight' ),
		'intro'             => array( 'Ask a question, arrange a visit, or just check we teach the thing your child keeps asking about.', 'field_sjpta_ph_intro' ),
	)
);

// ---------- Three ways in ----------
$sjpta_ways = array(
	array( 'mail', 'accent', 'Email us', 'The quickest way to reach the school. Class enquiries go to Lottie.', 'mailto:' . $sjpta_email, $sjpta_email ),
	array( 'instagram', 'magenta', 'Social', 'Class clips, show news and last-minute notices go out here first.', $sjpta_instagram, '@sjptheatrearts' ),
	array( 'lock', 'plum', 'Already with us', 'Registers, invoices and bookings live in the member portal.', $sjpta_portal, 'Go to member login' ),
);

$sjpta_way_rows = array();

foreach ( $sjpta_ways as $sjpta_way ) {
	$sjpta_way_rows[] = array(
		'icon'       => array( $sjpta_way[0], 'field_sjpta_lc_card_icon' ),
		'tone'       => array( $sjpta_way[1], 'field_sjpta_lc_card_tone' ),
		'title'      => array( $sjpta_way[2], 'field_sjpta_lc_card_title' ),
		'text'       => array( $sjpta_way[3], 'field_sjpta_lc_card_text' ),
		'url'        => array( $sjpta_way[4], 'field_sjpta_lc_card_url' ),
		'link_label' => array( $sjpta_way[5], 'field_sjpta_lc_card_link_label' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/link-cards',
	sjpta_seed_repeater( 'cards', 'field_sjpta_lc_cards', $sjpta_way_rows )
);

// ---------- The studio, and a message form ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/studio-panel',
	array(
		'photo'          => array( sjpta_seed_image( 'web-pic' ), 'field_sjpta_sp_photo' ),
		'name'           => array( 'SJP Theatre Arts', 'field_sjpta_sp_name' ),
		'maps_label'     => array( 'Open in maps', 'field_sjpta_sp_maps_label' ),
		'maps_url'       => array( '', 'field_sjpta_sp_maps_url' ),
		'note'           => array( 'Studio details, parking and access information are being confirmed with the venue. Email us in the meantime and we will send directions for the class you are coming to.', 'field_sjpta_sp_note' ),
		'form_heading'   => array( 'Send us a message', 'field_sjpta_sp_form_heading' ),
		'form_text'      => array( 'For anything that is not an enrolment: private lessons, parties, wedding dance, partnerships or a question about the show.', 'field_sjpta_sp_form_text' ),
		'submit_label'   => array( 'Send message', 'field_sjpta_sp_submit' ),
		'footnote'       => array( 'Ready to enrol?', 'field_sjpta_sp_footnote' ),
		'footnote_label' => array( 'Use the enrolment form, it asks the right questions.', 'field_sjpta_sp_footnote_label' ),
		'footnote_url'   => array( $sjpta_join, 'field_sjpta_sp_footnote_url' ),
	),
	array( 'anchor' => 'contact' )
);

// ---------- Three things worth knowing ----------
$sjpta_notes = array(
	array( 'clock', 'accent', 'When we reply', 'Enquiries are answered between classes rather than during them, so a reply usually comes within a few days. If it is urgent, say so in the subject line.' ),
	array( 'calendar', 'magenta', 'When we are teaching', 'Classes run Monday, Tuesday, Wednesday, Thursday and Saturday. Exact studio hours are being confirmed for the new term.' ),
	array( 'shield-tick', 'green', 'Safeguarding contact', 'Concerns about a child should go directly to SJ rather than through the general inbox. Our policy explains the process and is available on request.' ),
);

$sjpta_note_rows = array();

foreach ( $sjpta_notes as $sjpta_note ) {
	$sjpta_note_rows[] = array(
		'icon'       => array( $sjpta_note[0], 'field_sjpta_lc_card_icon' ),
		'tone'       => array( $sjpta_note[1], 'field_sjpta_lc_card_tone' ),
		'title'      => array( $sjpta_note[2], 'field_sjpta_lc_card_title' ),
		'text'       => array( $sjpta_note[3], 'field_sjpta_lc_card_text' ),
		'url'        => array( '', 'field_sjpta_lc_card_url' ),
		'link_label' => array( '', 'field_sjpta_lc_card_link_label' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/link-cards',
	sjpta_seed_repeater( 'cards', 'field_sjpta_lc_cards', $sjpta_note_rows )
);

// ---------- Closing band ----------
$sjpta_sections[] = sprintf(
	'<!-- wp:sjptheatrearts/cta-band %s /-->',
	wp_json_encode(
		array(
			'heading'        => 'Ready when you are',
			'text'           => 'Enrol now and we will confirm your class time, teacher and what to bring.',
			'primaryLabel'   => 'Enrol now',
			'primaryUrl'     => $sjpta_join,
			'secondaryLabel' => 'Timetable & fees',
			'secondaryUrl'   => $sjpta_timetable,
			'roomy'          => true,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	)
);

sjpta_seed_write( 'contact', $sjpta_sections );

WP_CLI::log( get_permalink( $sjpta_page->ID ) );
