<?php
/**
 * Seed the Join page.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-join.php
 *
 * Development tooling, not shipped behaviour. Copy is taken verbatim from
 * design-reference/SJP Join Alt.dc.html.
 *
 * The class menu is built from the published classes rather than the design's
 * hard-coded list, which named "Adult Ballet" (now unpublished) and predates the
 * finalised fifteen. It follows the data from here on.
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

$sjpta_page = get_page_by_path( 'join' );

if ( ! $sjpta_page ) {
	WP_CLI::error( 'No page with the slug "join".' );
}

$sjpta_contact = get_page_by_path( 'contact' );
$sjpta_contact = $sjpta_contact ? get_permalink( $sjpta_contact ) : home_url( '/contact/' );

$sjpta_sections = array();

// ---------- Hero ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/page-hero',
	array(
		'layout'            => array( 'centred', 'field_sjpta_ph_layout' ),
		'breadcrumb'        => array( 'Join SJP', 'field_sjpta_ph_breadcrumb' ),
		'heading'           => array( 'Joining is three steps.', 'field_sjpta_ph_heading' ),
		'heading_highlight' => array( 'three steps.', 'field_sjpta_ph_highlight' ),
		'intro'             => array( 'Tell us about your child, we suggest a class, and you enrol through the member portal. Simple, secure and open all year.', 'field_sjpta_ph_intro' ),
		'cta_label'         => array( 'Start my enquiry', 'field_sjpta_ph_cta_label' ),
		'cta_url'           => array( '#enquire', 'field_sjpta_ph_cta_url' ),
		'alt_label'         => array( 'Read the FAQs', 'field_sjpta_ph_alt_label' ),
		'alt_url'           => array( '#faqs', 'field_sjpta_ph_alt_url' ),
	)
);

// ---------- The three steps ----------
$sjpta_steps = array(
	array(
		'Tell us about your child',
		'Their age, what they are drawn to and whether they have danced before. If you have no idea which class to pick, say so; that is the most common answer we get.',
		'orange',
		'Takes about two minutes.',
	),
	array(
		'We reply with options',
		'Lottie reads every enquiry and comes back with two or three classes that would suit, the times they run, the fee and the next available start date.',
		'magenta',
		'A real person, not an automated reply.',
	),
	array(
		'Enrol online',
		'Enrol through the member portal, where consents and payment are handled securely. If the class is not right, tell us and we will suggest something else.',
		'green',
		'Done in a few minutes, all online.',
	),
);

$sjpta_step_rows = array();

foreach ( $sjpta_steps as $sjpta_step ) {
	$sjpta_step_rows[] = array(
		'title'  => array( $sjpta_step[0], 'field_sjpta_sg_step_title' ),
		'text'   => array( $sjpta_step[1], 'field_sjpta_sg_step_text' ),
		'invert' => array( 0, 'field_sjpta_sg_step_invert' ),
		'tone'   => array( $sjpta_step[2], 'field_sjpta_sg_step_tone' ),
		'note'   => array( $sjpta_step[3], 'field_sjpta_sg_step_note' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/step-grid',
	array_merge(
		array(
			'heading'  => 'How joining works',
			'_heading' => 'field_sjpta_sg_heading',
		),
		sjpta_seed_repeater( 'steps', 'field_sjpta_sg_steps', $sjpta_step_rows )
	)
);

// ---------- The first class ----------
$sjpta_cards = array(
	array( 'Wear', 'What to wear', 'Anything they can move in, hair tied back, bare feet or trainers. No uniform for the first class.', 'orange' ),
	array( 'Bring', 'What to bring', 'A named water bottle. That is the whole list.', 'purple' ),
	array( 'Nerves', 'If they are nervous', 'Tell us beforehand. We will pair them with a student who has been there a while.', 'magenta' ),
	array( 'After', 'Afterwards', 'The teacher will find you for two minutes and tell you honestly how it went.', 'green' ),
);

$sjpta_card_rows = array();

foreach ( $sjpta_cards as $sjpta_card ) {
	$sjpta_card_rows[] = array(
		'badge' => array( $sjpta_card[0], 'field_sjpta_fc_card_badge' ),
		'title' => array( $sjpta_card[1], 'field_sjpta_fc_card_title' ),
		'text'  => array( $sjpta_card[2], 'field_sjpta_fc_card_text' ),
		'tone'  => array( $sjpta_card[3], 'field_sjpta_fc_card_tone' ),
		'icon'  => array( '', 'field_sjpta_fc_card_icon' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/feature-cards',
	array_merge(
		array(
			'layout'   => 'bar',
			'_layout'  => 'field_sjpta_fc_layout',
			'heading'  => 'The first class',
			'_heading' => 'field_sjpta_fc_heading',
			'note'     => 'What actually happens on the day',
			'_note'    => 'field_sjpta_fc_note',
		),
		sjpta_seed_repeater( 'cards', 'field_sjpta_fc_cards', $sjpta_card_rows )
	)
);

// ---------- The enrolment form ----------
$sjpta_points = array(
	'Your place is confirmed as soon as you enrol.',
	'Your details go to Lottie only. We do not pass them to anyone else.',
	'Students can start mid-term. You do not have to wait for September.',
);

$sjpta_point_rows = array();

foreach ( $sjpta_points as $sjpta_i => $sjpta_point ) {
	$sjpta_point_rows[] = array(
		'text' => array( $sjpta_point, 'field_sjpta_ep_point_text' ),
		'tone' => array( 'green', 'field_sjpta_ep_point_tone' ),
	);
}

/*
 * The class menu, from the published classes. "Please recommend a class" comes
 * first, because it is the honest answer for most people filling this in.
 */
$sjpta_options = array( 'Please recommend a class' );

foreach ( get_posts(
	array(
		'post_type'      => SJPTA_CLASS_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
) as $sjpta_class ) {
	$sjpta_options[] = $sjpta_class->post_title;
}

$sjpta_option_rows = array();

foreach ( $sjpta_options as $sjpta_option ) {
	$sjpta_option_rows[] = array( 'text' => array( $sjpta_option, 'field_sjpta_ep_class_text' ) );
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/enquiry-panel',
	array_merge(
		array(
			'variant'       => 'join',
			'_variant'      => 'field_sjpta_ep_variant',
			'heading'       => 'Send us the basics',
			'_heading'      => 'field_sjpta_ep_heading',
			'intro'         => 'We only ask for what we need to suggest a suitable class. Everything else is sorted once you decide to join.',
			'_intro'        => 'field_sjpta_ep_intro',
			'recipient'     => 'lottie',
			'_recipient'    => 'field_sjpta_ep_recipient',
			'form_heading'  => 'Enrol now',
			'_form_heading' => 'field_sjpta_ep_form_heading',
			'form_text'     => 'Fields marked optional can be left blank.',
			'_form_text'    => 'field_sjpta_ep_form_text',
			'submit_label'  => 'Send my enquiry',
			'_submit_label' => 'field_sjpta_ep_submit_label',
			'consent'       => 'Please do not include medical or safeguarding details here. We collect those securely at registration.',
			'_consent'      => 'field_sjpta_ep_consent',
		),
		sjpta_seed_repeater( 'points', 'field_sjpta_ep_points', $sjpta_point_rows ),
		sjpta_seed_repeater( 'class_options', 'field_sjpta_ep_class_options', $sjpta_option_rows )
	),
	array( 'anchor' => 'enquire' )
);

// ---------- FAQ ----------
$sjpta_faq = array(
	array(
		'My child has never danced. Will they be behind?',
		'No. Classes are grouped by age and stage, and beginners join a class where other people are also learning it for the first time. If a class has moved on too far, we will put your child in a different one rather than let them struggle.',
	),
	array(
		'Is the atmosphere competitive?',
		'Not between students. We enter examinations and perform at events, but the comparison we care about is with where that child was last term.',
	),
	array(
		'Can I stay and watch?',
		'For the first class, yes, and for the youngest ages always. Once a child has settled, most parents find their child concentrates better without an audience.',
	),
	array(
		'My child has additional needs. What are the options?',
		'Group classes, one-to-one lessons, or a mix. Tell us what helps and what does not before you book anything, and we will suggest the honest option rather than the easy one.',
	),
	array(
		'What happens after I send the form?',
		'Lottie reads it and replies with two or three suitable classes, the times, the fee and the next start date. A person, not an automated email.',
	),
	array(
		'Do you take teenagers who are starting late?',
		'Yes, and more than you would think. Several of our troupe members started at thirteen or fourteen.',
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
			'heading'  => 'Questions we get asked most',
			'_heading' => 'field_sjpta_fq_heading',
			'intro'    => 'New families',
			'_intro'   => 'field_sjpta_fq_intro',
		),
		sjpta_seed_repeater( 'items', 'field_sjpta_fq_items', $sjpta_faq_rows )
	),
	array( 'anchor' => 'faqs' )
);

// ---------- Closing band ----------
$sjpta_sections[] = sprintf(
	'<!-- wp:sjptheatrearts/cta-band %s /-->',
	wp_json_encode(
		array(
			'heading'        => 'Still deciding?',
			'text'           => 'Ring the question past us before you fill anything in. We would rather answer it than have you guess.',
			'primaryLabel'   => 'Contact us',
			'primaryUrl'     => $sjpta_contact,
			'secondaryLabel' => 'Browse classes',
			'secondaryUrl'   => home_url( '/classes/' ),
			'roomy'          => true,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	)
);

sjpta_seed_write( 'join', $sjpta_sections );

WP_CLI::log( get_permalink( $sjpta_page->ID ) );
