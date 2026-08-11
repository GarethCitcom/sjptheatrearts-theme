<?php
/**
 * Seed the About page.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-about.php
 *
 * Development tooling, not shipped behaviour. Copy is taken verbatim from
 * design-reference/SJP About Alt.dc.html; nothing here is invented.
 *
 * Three departures from the prototype, all agreed with Gaz:
 *
 * - "13 class styles taught" is now counted from the published classes, so it
 *   cannot go stale. It reads 15 today.
 * - "12 awarding & safeguarding bodies" is counted from the logos on this page,
 *   because the prototype put 12 directly above a row of four.
 * - "3 performing troupes" is now 2. Blaze has gone.
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

$sjpta_page = get_page_by_path( 'about' );

if ( ! $sjpta_page ) {
	WP_CLI::error( 'No page with the slug "about".' );
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
		'breadcrumb'        => array( 'About SJP', 'field_sjpta_ph_breadcrumb' ),
		'heading'           => array( 'A theatre school where every child is known by name.', 'field_sjpta_ph_heading' ),
		'heading_highlight' => array( 'known by name.', 'field_sjpta_ph_highlight' ),
		'intro'             => array( 'Professional training, taught in a room where nobody is embarrassed to be a beginner.', 'field_sjpta_ph_intro' ),
	)
);

// ---------- A welcome from SJ ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/bio-feature',
	array(
		'layout'          => array( 'stacked', 'field_sjpta_bf_layout' ),
		'eyebrow'         => array( 'Our story', 'field_sjpta_bf_eyebrow' ),
		'heading'         => array( 'A welcome from SJ', 'field_sjpta_bf_heading' ),
		'body'            => array(
			"<p>I trained at Laine Theatre Arts from the age of 18, on a personal scholarship from Betty Laine, studying dance across every genre alongside singing and acting. Since then I have appeared in television commercials and performed at Birmingham Hippodrome, Wolverhampton Grand and Cardiff New Theatre.</p>\n"
			. "<p>I have choreographed Cabaret, Sweet Charity, The Wedding Singer, Seussical the Musical and the Great British Games for Heart of Worcestershire College, plus work for the Worcester Olympic ceremony, Worcester Wolves and Worcester Warriors.</p>\n"
			. '<p>None of that matters much on a Tuesday evening. What matters is that a seven-year-old who was hiding behind their mum in September is front and centre by December. That is the part of this job I care about, and it is why SJP exists.</p>',
			'field_sjpta_bf_body',
		),
		'photo_main'      => array( sjpta_seed_image( 'sj-headshot' ), 'field_sjpta_bf_photo_main' ),
		'photo_inset'     => array( sjpta_seed_image( 'xmas-3' ), 'field_sjpta_bf_photo_inset' ),
		'photo_caption'   => array( 'SJ · founder and principal', 'field_sjpta_bf_photo_caption' ),
		'byline_initials' => array( 'SJ', 'field_sjpta_bf_byline_initials' ),
		'byline_name'     => array( 'SJ', 'field_sjpta_bf_byline_name' ),
		'byline_role'     => array( 'Founder and principal, SJP Theatre Arts', 'field_sjpta_bf_byline_role' ),
	)
);

// ---------- What we believe ----------
$sjpta_beliefs = array(
	array( 'heart', 'Belonging', 'Every student should feel wanted in the room before they are any good at the thing.', 'orange' ),
	array( 'cap', 'Real training', 'Proper technique, taught properly, whether a student is heading for a career or a hobby.', 'magenta' ),
	array( 'check', 'Openness', 'Additional needs, late starters and nervous children are planned for, not accommodated as an afterthought.', 'purple' ),
	array( 'trophy', 'A stage for everyone', 'Nobody sits out the show. Every student who wants to perform gets a place in it.', 'green' ),
);

$sjpta_belief_rows = array();

foreach ( $sjpta_beliefs as $sjpta_belief ) {
	$sjpta_belief_rows[] = array(
		'icon'  => array( $sjpta_belief[0], 'field_sjpta_fc_card_icon' ),
		'title' => array( $sjpta_belief[1], 'field_sjpta_fc_card_title' ),
		'text'  => array( $sjpta_belief[2], 'field_sjpta_fc_card_text' ),
		'tone'  => array( $sjpta_belief[3], 'field_sjpta_fc_card_tone' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/feature-cards',
	array_merge(
		array(
			'layout'   => 'bar',
			'_layout'  => 'field_sjpta_fc_layout',
			'heading'  => 'What we believe',
			'_heading' => 'field_sjpta_fc_heading',
			'note'     => 'Four things we will not compromise on',
			'_note'    => 'field_sjpta_fc_note',
		),
		sjpta_seed_repeater( 'cards', 'field_sjpta_fc_cards', $sjpta_belief_rows )
	)
);

// ---------- The numbers ----------
$sjpta_stats = array(
	array( 'classes', '', 'Class styles taught', 'orange' ),
	array( 'typed', 'Baby to adult', 'Ages in the studio', 'magenta' ),
	array( 'typed', '2', 'Performing troupes', 'purple' ),
	array( 'logos', '', 'Awarding & safeguarding bodies', 'green' ),
);

$sjpta_stat_rows = array();

foreach ( $sjpta_stats as $sjpta_stat ) {
	$sjpta_stat_rows[] = array(
		'source' => array( $sjpta_stat[0], 'field_sjpta_sr_source' ),
		'value'  => array( $sjpta_stat[1], 'field_sjpta_sr_value' ),
		'label'  => array( $sjpta_stat[2], 'field_sjpta_sr_label' ),
		'tone'   => array( $sjpta_stat[3], 'field_sjpta_sr_tone' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/stat-row',
	sjpta_seed_repeater( 'stats', 'field_sjpta_sr_stats', $sjpta_stat_rows )
);

// ---------- Meet the teachers ----------
$sjpta_teachers = array(
	array( 'SJ', 'Founder and principal. Laine Theatre Arts scholar. Acro & Cheer, Ballroom & Latin, First Steps, Drama.', 'orange' ),
	array( 'Madi', 'Musical Theatre on Wednesdays, plus Drama, Singing and Jazz & Commercial.', 'magenta' ),
	array( 'Amy', 'Second Steps and Jazz & Commercial. Often the first teacher a four-year-old works with on their own.', 'purple' ),
	array( 'Lottie', 'Parent & Toddler on Monday mornings, and the person who answers your first enquiry.', 'green' ),
);

$sjpta_teacher_rows = array();

foreach ( $sjpta_teachers as $sjpta_teacher ) {
	$sjpta_teacher_rows[] = array(
		'name' => array( $sjpta_teacher[0], 'field_sjpta_tl_name' ),
		'role' => array( $sjpta_teacher[1], 'field_sjpta_tl_role' ),
		'tone' => array( $sjpta_teacher[2], 'field_sjpta_tl_tone' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/teacher-list',
	array_merge(
		array(
			'label'          => 'Meet the teachers',
			'_label'         => 'field_sjpta_tl_label',
			'note'           => 'Every teacher is qualified, DBS-checked and has worked professionally',
			'_note'          => 'field_sjpta_tl_note',
			'link_label'     => 'Ask about a teacher',
			'_link_label'    => 'field_sjpta_tl_link_label',
			'link_url'       => $sjpta_join,
			'_link_url'      => 'field_sjpta_tl_link_url',
			'photo'          => sjpta_seed_image( 'team' ),
			'_photo'         => 'field_sjpta_tl_photo',
			'photo_caption'  => 'The teaching team',
			'_photo_caption' => 'field_sjpta_tl_photo_caption',
			'footnote'       => 'Teacher photographs and full qualifications are being updated. Ask us and we will tell you exactly who teaches the class you are considering.',
			'_footnote'      => 'field_sjpta_tl_footnote',
		),
		sjpta_seed_repeater( 'people', 'field_sjpta_tl_people', $sjpta_teacher_rows )
	),
	array( 'anchor' => 'teachers' )
);

// ---------- Inclusion and safeguarding ----------
$sjpta_points = array(
	array( 'Every teacher is DBS-checked', 'before they take a class, and checks are kept current.' ),
	array( 'We work to an external safeguarding framework', 'rather than a policy we wrote ourselves.' ),
	array( 'Photography consent is recorded per family', 'and checked before anything is published.' ),
	array( 'Medical and safeguarding details are collected securely', 'at registration, never through a website form.' ),
);

$sjpta_point_rows = array();

foreach ( $sjpta_points as $sjpta_point ) {
	$sjpta_point_rows[] = array(
		'strong' => array( $sjpta_point[0], 'field_sjpta_as_point_strong' ),
		'text'   => array( $sjpta_point[1], 'field_sjpta_as_point_text' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/assurance',
	array_merge(
		array(
			'left_eyebrow'   => 'Inclusion',
			'_left_eyebrow'  => 'field_sjpta_as_left_eyebrow',
			'left_heading'   => 'Additional needs and one-to-one teaching',
			'_left_heading'  => 'field_sjpta_as_left_heading',
			'left_body'      => "Some students thrive in a full group from the first week. Others need a quieter room, a familiar teacher and time to work at their own pace. We teach both, and we do not treat one as the lesser option.\n\nPrivate lessons for young people with additional needs run alongside the group timetable, with the same certificates and the same chance to perform. If you would like to talk it through before booking anything, we would rather have that conversation first.",
			'_left_body'     => 'field_sjpta_as_left_body',
			'quote'          => 'Private lessons offer a great way for Caitlin to learn at her pace, and the teachers are professional and experienced in a safe, nurturing environment. It is wonderful for her to experience the joy of dance and to have the chance to earn certificates she is very proud of.',
			'_quote'         => 'field_sjpta_as_quote',
			'quote_source'   => 'Parent of Caitlin',
			'_quote_source'  => 'field_sjpta_as_quote_source',
			'quote_detail'   => 'private lessons',
			'_quote_detail'  => 'field_sjpta_as_quote_detail',
			'right_eyebrow'  => 'Safeguarding',
			'_right_eyebrow' => 'field_sjpta_as_right_eyebrow',
			'right_heading'  => 'How we keep students safe',
			'_right_heading' => 'field_sjpta_as_right_heading',
			'mark'           => sjpta_seed_image( 'safeguarding-white' ),
			'_mark'          => 'field_sjpta_as_mark',
			'mark_text'      => 'Our full safeguarding policy is available on request. Ask at reception or email us and we will send a copy.',
			'_mark_text'     => 'field_sjpta_as_mark_text',
		),
		sjpta_seed_repeater( 'points', 'field_sjpta_as_points', $sjpta_point_rows )
	),
	array( 'anchor' => 'safeguarding' )
);

// ---------- Examinations ----------
$sjpta_steps = array(
	array( 'Medals', 'Short assessments for younger students. A first taste of dancing for someone other than the teacher.' ),
	array( 'Graded examinations', 'Structured syllabus work in ballet, tap and modern, assessed by a visiting examiner.' ),
	array( 'LAMDA', 'Speech, acting and musical theatre qualifications for drama and singing students.' ),
	array( 'Vocational routes', 'Audition preparation and college applications for students considering training full time.' ),
);

$sjpta_step_rows = array();

foreach ( $sjpta_steps as $sjpta_step ) {
	$sjpta_step_rows[] = array(
		'title'  => array( $sjpta_step[0], 'field_sjpta_sg_step_title' ),
		'text'   => array( $sjpta_step[1], 'field_sjpta_sg_step_text' ),
		'invert' => array( 0, 'field_sjpta_sg_step_invert' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/step-grid',
	array_merge(
		array(
			'heading'  => 'Examinations and where they lead',
			'_heading' => 'field_sjpta_sg_heading',
			'note'     => 'Grades are optional; for students who want them, they give a clear ladder',
			'_note'    => 'field_sjpta_sg_note',
		),
		sjpta_seed_repeater( 'steps', 'field_sjpta_sg_steps', $sjpta_step_rows )
	),
	array( 'anchor' => 'exams' )
);

// ---------- Awarding bodies ----------
$sjpta_logos = array( 'idta', 'lamda', 'uka', 'adfp' );
$sjpta_rows  = array();

foreach ( $sjpta_logos as $sjpta_logo ) {
	$sjpta_rows[] = array(
		'image'  => array( sjpta_seed_image( $sjpta_logo ), 'field_sjpta_acc_logo_image' ),
		'height' => array( 30, 'field_sjpta_acc_logo_height' ),
	);
}

$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/accreditation',
	array_merge(
		array(
			'panel'         => 1,
			'_panel'        => 'field_sjpta_acc_panel',
			'intro_strong'  => 'Who assesses our students.',
			'_intro_strong' => 'field_sjpta_acc_strong',
			'intro'         => 'We enter students through established awarding bodies, so the certificate means the same thing wherever they take it next.',
			'_intro'        => 'field_sjpta_acc_intro',
		),
		sjpta_seed_repeater( 'logos', 'field_sjpta_acc_logos', $sjpta_rows )
	)
);

// ---------- Closing band ----------
$sjpta_sections[] = sprintf(
	'<!-- wp:sjptheatrearts/cta-band %s /-->',
	wp_json_encode(
		array(
			'heading'        => 'Come and see the place',
			'text'           => 'The best way to judge a school is to stand in it for an hour. Come and visit, bring your questions, and enrol when you are ready.',
			'primaryLabel'   => 'Enrol now',
			'primaryUrl'     => $sjpta_join,
			'secondaryLabel' => 'Find the studio',
			'secondaryUrl'   => $sjpta_contact,
			'roomy'          => true,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	)
);

/*
 * sjpta_seed_write(), not wp_update_post(). It slashes the content first, and
 * without that wp_update_post() eats every backslash: the JSON escape "\n"
 * inside a block comment arrives as a literal "n", which is exactly how SJ's
 * biography ended up with a stray letter between each paragraph.
 */
sjpta_seed_write( 'about', $sjpta_sections );

WP_CLI::log( get_permalink( $sjpta_page->ID ) );
