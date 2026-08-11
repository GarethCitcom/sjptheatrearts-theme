<?php
/**
 * Seed the Timetable & fees page.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-timetable.php
 *
 * Development tooling, not shipped behaviour. Copy is taken verbatim from
 * design-reference/SJP Timetable and Fees Alt.dc.html; nothing here is invented.
 *
 * The one deliberate departure is the fee amounts. The design shows "Ask us",
 * "Discount available" and "Reduced rate" as stand-ins, and they stay stand-ins:
 * a fee is exactly the kind of fact that must never be guessed, and the feed
 * carries no pricing at all. The client types the real figures into the fields
 * and the placeholders disappear.
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

$sjpta_page = get_page_by_path( 'timetable-and-fees' );

if ( ! $sjpta_page ) {
	WP_CLI::error( 'No page with the slug "timetable-and-fees".' );
}

$sjpta_join    = get_page_by_path( 'join' );
$sjpta_contact = get_page_by_path( 'contact' );
$sjpta_join    = $sjpta_join ? get_permalink( $sjpta_join ) : home_url( '/join/' );
$sjpta_contact = $sjpta_contact ? get_permalink( $sjpta_contact ) : home_url( '/contact/' );

$sjpta_sections = array();

// ---------- Hero ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/page-hero',
	array(
		'layout'            => array( 'centred', 'field_sjpta_ph_layout' ),
		'breadcrumb'        => array( 'Timetable & fees', 'field_sjpta_ph_breadcrumb' ),
		'heading'           => array( 'Timetable & fees.', 'field_sjpta_ph_heading' ),
		'heading_highlight' => array( 'fees.', 'field_sjpta_ph_highlight' ),
		'intro'             => array( 'The weekly schedule and what it costs, in one place. Anything still being confirmed for the coming term is marked as such rather than guessed.', 'field_sjpta_ph_intro' ),
	)
);

// ---------- Term status ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/term-status',
	array(
		'new_students' => array( 'Can start mid-term', 'field_sjpta_ts_new_students' ),
		'cta_label'    => array( 'Ask for the current sheet', 'field_sjpta_ts_cta_label' ),
		'cta_url'      => array( $sjpta_join, 'field_sjpta_ts_cta_url' ),
	)
);

// ---------- Weekly timetable ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/timetable',
	array(
		'label'       => array( 'Weekly schedule', 'field_sjpta_tt_label' ),
		'note'        => array( 'What runs, and when', 'field_sjpta_tt_note' ),
		'aside_title' => array( 'Private lessons are arranged individually.', 'field_sjpta_tt_aside_title' ),
		'aside_text'  => array( 'Drama, Singing, Ballroom & Latin and one-to-one lessons for students with additional needs are booked directly with the teacher, so they do not appear on the weekly grid.', 'field_sjpta_tt_aside_text' ),
	),
	array( 'anchor' => 'timetable' )
);

// ---------- Fees ----------
$sjpta_cards = array(
	array(
		'title' => array( 'Per class', 'field_sjpta_fe_card_title' ),
		'value' => array( '', 'field_sjpta_fe_card_value' ),
		'text'  => array( 'Charged per term rather than per week, so the amount depends on how many weeks the term runs.', 'field_sjpta_fe_card_text' ),
		'tone'  => array( 'orange', 'field_sjpta_fe_card_tone' ),
	),
	array(
		'title' => array( 'Siblings', 'field_sjpta_fe_card_title' ),
		'value' => array( 'Discount available', 'field_sjpta_fe_card_value' ),
		'text'  => array( 'Families with more than one child at the school pay less. The rate is confirmed when you register.', 'field_sjpta_fe_card_text' ),
		'tone'  => array( 'magenta', 'field_sjpta_fe_card_tone' ),
	),
	array(
		'title' => array( 'Multiple classes', 'field_sjpta_fe_card_title' ),
		'value' => array( 'Reduced rate', 'field_sjpta_fe_card_value' ),
		'text'  => array( 'Students taking three or more classes a week pay a lower rate per class. Ask for the current banding.', 'field_sjpta_fe_card_text' ),
		'tone'  => array( 'purple', 'field_sjpta_fe_card_tone' ),
	),
	array(
		'title' => array( 'Enrolment', 'field_sjpta_fe_card_title' ),
		'value' => array( 'Online', 'field_sjpta_fe_card_value' ),
		'text'  => array( 'Enrol through the member portal, where consents and payment are handled securely. Your place is confirmed straight away.', 'field_sjpta_fe_card_text' ),
		'tone'  => array( 'green', 'field_sjpta_fe_card_tone' ),
	),
);

$sjpta_included = array(
	'Weekly teaching for the whole term',
	'Choreography and rehearsal for the annual show',
	'Class preparation for medals and examinations',
	'Public liability and teaching insurance',
);

$sjpta_extras = array(
	array( 'Uniform.', 'Needed once your child has settled, not for the first class.' ),
	array( 'Shoes.', 'Style depends on the class; we will tell you exactly what to buy.' ),
	array( 'Examination entry.', 'Only if your child chooses to be entered, and always optional.' ),
	array( 'Show costume and tickets.', 'Once a year, announced well in advance.' ),
);

$sjpta_included_rows = array();

foreach ( $sjpta_included as $sjpta_item ) {
	$sjpta_included_rows[] = array( 'text' => array( $sjpta_item, 'field_sjpta_fe_included_text' ) );
}

$sjpta_extra_rows = array();

foreach ( $sjpta_extras as $sjpta_item ) {
	$sjpta_extra_rows[] = array(
		'title' => array( $sjpta_item[0], 'field_sjpta_fe_extra_title' ),
		'text'  => array( $sjpta_item[1], 'field_sjpta_fe_extra_text' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/fees',
	array_merge(
		array(
			'label'           => 'What it costs',
			'_label'          => 'field_sjpta_fe_label',
			'note'            => 'Fees are being reviewed for the coming term; ask and we will send the current sheet the same week',
			'_note'           => 'field_sjpta_fe_note',
			'included_title'  => 'Included in the fee',
			'_included_title' => 'field_sjpta_fe_included_title',
			'extras_title'    => 'Costs on top, and when',
			'_extras_title'   => 'field_sjpta_fe_extras_title',
		),
		sjpta_seed_repeater( 'cards', 'field_sjpta_fe_cards', $sjpta_cards ),
		sjpta_seed_repeater( 'included', 'field_sjpta_fe_included', $sjpta_included_rows ),
		sjpta_seed_repeater( 'extras', 'field_sjpta_fe_extras', $sjpta_extra_rows )
	),
	array( 'anchor' => 'fees' )
);

// ---------- FAQ ----------
$sjpta_faq = array(
	array(
		'Do I pay for the whole term up front?',
		'Fees are invoiced per term. If paying in one go is difficult, tell us. We would rather set up an instalment plan than lose a student over timing.',
	),
	array(
		'What if my child misses a week?',
		'Term fees cover the place rather than individual attendance, so a missed week is not refunded. Where there is a suitable alternative class in the same week, we will usually offer it.',
	),
	array(
		'Can my child try a second style before choosing?',
		'Yes. Plenty of children try two before settling, and a fair number end up doing both. Ask and we will arrange it.',
	),
	array(
		'Do we have to buy uniform straight away?',
		'No. Come in comfortable clothes for the first few weeks. We will point you to the uniform list once you know your child is staying.',
	),
	array(
		'Are examinations compulsory?',
		'Never. Some students love the structure of grades and medals; others come purely to dance. Both are treated the same in class.',
	),
);

$sjpta_faq_rows = array();

foreach ( $sjpta_faq as $sjpta_item ) {
	$sjpta_faq_rows[] = array(
		'question' => array( $sjpta_item[0], 'field_sjpta_fq_question' ),
		'answer'   => array( $sjpta_item[1], 'field_sjpta_fq_answer' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/faq',
	array_merge(
		array(
			'layout'   => 'bar',
			'_layout'  => 'field_sjpta_fq_layout',
			'eyebrow'  => '',
			'_eyebrow' => 'field_sjpta_fq_eyebrow',
			'heading'  => 'Questions parents ask about money',
			'_heading' => 'field_sjpta_fq_heading',
			'intro'    => '',
			'_intro'   => 'field_sjpta_fq_intro',
		),
		sjpta_seed_repeater( 'items', 'field_sjpta_fq_items', $sjpta_faq_rows )
	)
);

// ---------- Closing band ----------
$sjpta_sections[] = sprintf(
	'<!-- wp:sjptheatrearts/cta-band %s /-->',
	wp_json_encode(
		array(
			'heading'        => "Ask for this term's sheet",
			'text'           => "Send us your child's age and we will reply with the times, the fee and how to enrol.",
			'primaryLabel'   => 'Enrol now',
			'primaryUrl'     => $sjpta_join,
			'secondaryLabel' => 'Contact us',
			'secondaryUrl'   => $sjpta_contact,
			'roomy'          => true,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	)
);

/*
 * sjpta_seed_write() rather than wp_update_post(), which unslashes and would
 * turn any JSON "\n" escape in a block comment into a literal "n". Nothing here
 * carries a newline today, but the next line of copy that does would break
 * silently.
 */
sjpta_seed_write( 'timetable-and-fees', $sjpta_sections );

WP_CLI::log( get_permalink( $sjpta_page->ID ) );
