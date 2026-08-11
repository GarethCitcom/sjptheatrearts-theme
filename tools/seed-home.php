<?php
/**
 * Seed the homepage with its designed content.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-home.php
 *
 * Development tooling, not shipped behaviour. It writes the page content a
 * client would otherwise have to paste in by hand, so the site can be reviewed
 * against the design with real copy in real fields.
 *
 * Why the fields are populated rather than left to the render fallbacks:
 *
 *  1. An editor opening a block with empty fields sees blank inputs while the
 *     front end shows fallback copy — they cannot edit what they can see.
 *  2. ACF's `ACF_Local_Meta::is_request()` calls `key( $meta )`, which returns
 *     null for an empty array, and passes it straight to an array offset. On
 *     PHP 8.1+ that is a deprecation on every render of a data-less ACF block.
 *
 * Re-running is safe: it replaces the homepage content outright.
 *
 * @package SJPTheatreArts
 */

// No declare(strict_types=1) here: `wp eval-file` eval()s this file, and a
// declare must be the first statement in a script.

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

require_once SJPTA_DIR . '/tools/seed-lib.php';

$sjpta_sections = array();

// ---------- Hero ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/hero',
	array(
		'eyebrow'         => array( 'Bromsgrove · all ages · all abilities', 'field_sjpta_hero_eyebrow' ),
		'heading'         => array( "Find their place\nto shine.", 'field_sjpta_hero_heading' ),
		'highlight'       => array( 'shine.', 'field_sjpta_hero_highlight' ),
		'intro'           => array( 'Dance, sing and perform with welcoming, industry-trained teachers. Classes for babies, children and teens of every ability, plus adult ballet and ballroom.', 'field_sjpta_hero_intro' ),
		'primary_label'   => array( 'Enrol now', 'field_sjpta_hero_primary_label' ),
		'primary_url'     => array( '', 'field_sjpta_hero_primary_url' ),
		'secondary_label' => array( 'Find the right class', 'field_sjpta_hero_secondary_label' ),
		'secondary_url'   => array( '', 'field_sjpta_hero_secondary_url' ),
	)
);

// ---------- Age route cards ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/age-cards',
	sjpta_seed_repeater(
		'cards',
		'field_sjpta_ac_cards',
		array(
			array(
				'image'       => array( sjpta_seed_image( 'img_8003' ), 'field_sjpta_ac_image' ),
				'badge'       => array( 'Ages 2–4', 'field_sjpta_ac_badge' ),
				'badge_style' => array( 'orange', 'field_sjpta_ac_badge_style' ),
				'title'       => array( 'First steps into the studio', 'field_sjpta_ac_title' ),
				'text'        => array( 'Parent and toddler sessions, then first solo classes. Music, movement and making friends.', 'field_sjpta_ac_text' ),
				'meta'        => array( 'First Steps · with Lottie & SJ', 'field_sjpta_ac_meta' ),
				'url'         => array( '', 'field_sjpta_ac_url' ),
			),
			array(
				'image'       => array( sjpta_seed_image( 'img_0310' ), 'field_sjpta_ac_image' ),
				'badge'       => array( 'Ages 4–10', 'field_sjpta_ac_badge' ),
				'badge_style' => array( 'magenta', 'field_sjpta_ac_badge_style' ),
				'title'       => array( 'Ballet, tap, jazz & drama', 'field_sjpta_ac_title' ),
				'text'        => array( 'Graded classes with medals, exams and the Christmas show, at whatever pace suits your child.', 'field_sjpta_ac_text' ),
				'meta'        => array( 'Children · 8 class styles', 'field_sjpta_ac_meta' ),
				'url'         => array( '', 'field_sjpta_ac_url' ),
			),
			array(
				'image'       => array( sjpta_seed_image( 'img_8006' ), 'field_sjpta_ac_image' ),
				'badge'       => array( 'Ages 11–18', 'field_sjpta_ac_badge' ),
				'badge_style' => array( 'purple', 'field_sjpta_ac_badge_style' ),
				'title'       => array( 'Teens: train your way', 'field_sjpta_ac_title' ),
				'text'        => array( 'Commercial, lyrical, musical theatre and troupe. Once a week or a full training timetable.', 'field_sjpta_ac_text' ),
				'meta'        => array( 'Teens · recreational or serious', 'field_sjpta_ac_meta' ),
				'url'         => array( '', 'field_sjpta_ac_url' ),
			),
		)
	)
);

// ---------- Discipline ticker ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/ticker',
	array(
		'items' => array(
			"Ballet\nTap\nJazz & Commercial\nAcro & Cheer\nMusical Theatre\nDrama\nSinging\nLyrical\nTroupe\nLAMDA\nBallroom & Latin\nAdult Ballet\nWedding Dance",
			'field_sjpta_ticker_items',
		),
	)
);

// ---------- Accreditation ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/accreditation',
	array_merge(
		array(
			'intro_strong'  => 'Recognised, checked and insured.',
			'_intro_strong' => 'field_sjpta_acc_strong',
			'intro'         => 'Our teaching follows established awarding-body syllabuses and safeguarding practice.',
			'_intro'        => 'field_sjpta_acc_intro',
		),
		sjpta_seed_repeater(
			'logos',
			'field_sjpta_acc_logos',
			array(
				array(
					'image'  => array( sjpta_seed_image( 'idta' ), 'field_sjpta_acc_logo_image' ),
					'height' => array( 40, 'field_sjpta_acc_logo_height' ),
				),
				array(
					'image'  => array( sjpta_seed_image( 'lamda' ), 'field_sjpta_acc_logo_image' ),
					'height' => array( 34, 'field_sjpta_acc_logo_height' ),
				),
				array(
					'image'  => array( sjpta_seed_image( 'uka' ), 'field_sjpta_acc_logo_image' ),
					'height' => array( 36, 'field_sjpta_acc_logo_height' ),
				),
				array(
					'image'  => array( sjpta_seed_image( 'adfp' ), 'field_sjpta_acc_logo_image' ),
					'height' => array( 38, 'field_sjpta_acc_logo_height' ),
				),
			)
		)
	)
);

// ---------- About + stats ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/about-stats',
	array_merge(
		array(
			'label'          => 'About SJP',
			'_label'         => 'field_sjpta_ab_label',
			'label_note'     => 'Professional training, family-school warmth',
			'_label_note'    => 'field_sjpta_ab_note',
			'link_label'     => 'Our story',
			'_link_label'    => 'field_sjpta_ab_link_label',
			'link_url'       => '',
			'_link_url'      => 'field_sjpta_ab_link_url',
			'heading'        => 'A school where every child is known by name',
			'_heading'       => 'field_sjpta_ab_heading',
			'highlight'      => '',
			'_highlight'     => 'field_sjpta_ab_highlight',
			'body'           => "SJP Theatre Arts (formerly Bromsgrove School of Dance) is led by SJ, who trained at Laine Theatre Arts on a personal scholarship from Betty Laine and has performed and choreographed across the Midlands and beyond.\n\nThat professional standard sits inside a school where beginners are genuinely welcome and nobody has to fit a mould to belong here.",
			'_body'          => 'field_sjpta_ab_body',
			'image'          => sjpta_seed_image( 'web-pic' ),
			'_image'         => 'field_sjpta_ab_image',
			'image_caption'  => 'Inside the studio',
			'_image_caption' => 'field_sjpta_ab_caption',
			'byline_image'   => sjpta_seed_image( 'sj' ),
			'_byline_image'  => 'field_sjpta_ab_by_image',
			'byline_strong'  => 'SJ, founder and principal.',
			'_byline_strong' => 'field_sjpta_ab_by_strong',
			'byline_text'    => 'Laine Theatre Arts trained; teaching in Bromsgrove ever since.',
			'_byline_text'   => 'field_sjpta_ab_by_text',
		),
		sjpta_seed_repeater(
			'stats',
			'field_sjpta_ab_stats',
			array(
				array(
					'source' => array( 'classes', 'field_sjpta_ab_stat_source' ),
					'value'  => array( '', 'field_sjpta_ab_stat_value' ),
					'label'  => array( 'Class styles taught', 'field_sjpta_ab_stat_label' ),
					'tone'   => array( 'orange', 'field_sjpta_ab_stat_tone' ),
				),
				array(
					'source' => array( 'typed', 'field_sjpta_ab_stat_source' ),
					'value'  => array( '2–18+', 'field_sjpta_ab_stat_value' ),
					'label'  => array( 'Ages in the studio', 'field_sjpta_ab_stat_label' ),
					'tone'   => array( 'magenta', 'field_sjpta_ab_stat_tone' ),
				),
				array(
					'source' => array( 'logos', 'field_sjpta_ab_stat_source' ),
					'value'  => array( '', 'field_sjpta_ab_stat_value' ),
					'label'  => array( 'Awarding & safeguarding bodies', 'field_sjpta_ab_stat_label' ),
					'tone'   => array( 'purple', 'field_sjpta_ab_stat_tone' ),
				),
			)
		)
	)
);

// ---------- Popular classes ----------

/*
 * Which classes to feature is the client's call, so the relationship field is
 * seeded empty on purpose: the block falls back to the first few until someone
 * chooses, rather than this script deciding what is popular.
 */
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/popular-classes',
	array(
		'label'     => array( 'Popular classes', 'field_sjpta_pc_label' ),
		'note'      => array( 'First steps, big stages and everything between', 'field_sjpta_pc_note' ),
		'cta_label' => array( 'All', 'field_sjpta_pc_cta_label' ),
		'classes'   => array( array(), 'field_sjpta_pc_classes' ),
	),
	array( 'anchor' => 'classes' )
);

// ---------- Teens band ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/teens-band',
	array_merge(
		array(
			'eyebrow'          => 'For teenagers',
			'_eyebrow'         => 'field_sjpta_tb_eyebrow',
			'heading'          => 'Train your way.',
			'_heading'         => 'field_sjpta_tb_heading',
			'highlight'        => 'way.',
			'_highlight'       => 'field_sjpta_tb_highlight',
			'text'             => 'Build confidence, sharpen your technique and perform with people who love it as much as you do. Once a week or a full timetable, both are normal here.',
			'_text'            => 'field_sjpta_tb_text',
			'background'       => sjpta_seed_image( 'xmas-3' ),
			'_background'      => 'field_sjpta_tb_bg',
			'primary_label'    => 'Enrol now',
			'_primary_label'   => 'field_sjpta_tb_primary_label',
			'primary_url'      => '',
			'_primary_url'     => 'field_sjpta_tb_primary_url',
			'secondary_label'  => 'Teen classes',
			'_secondary_label' => 'field_sjpta_tb_secondary_label',
			'secondary_url'    => '',
			'_secondary_url'   => 'field_sjpta_tb_secondary_url',
		),
		sjpta_seed_repeater(
			'routes',
			'field_sjpta_tb_routes',
			array(
				array(
					'image' => array( sjpta_seed_image( 'img_8007' ), 'field_sjpta_tb_route_image' ),
					'title' => array( 'Commercial, lyrical & jazz', 'field_sjpta_tb_route_title' ),
					'text'  => array( 'High-energy technique and performance', 'field_sjpta_tb_route_text' ),
					'url'   => array( '', 'field_sjpta_tb_route_url' ),
				),
				array(
					'image' => array( sjpta_seed_image( 'img_0300' ), 'field_sjpta_tb_route_image' ),
					'title' => array( 'Troupe: Notorious, Shockwave', 'field_sjpta_tb_route_title' ),
					'text'  => array( 'Invitation-only performance teams', 'field_sjpta_tb_route_text' ),
					'url'   => array( '', 'field_sjpta_tb_route_url' ),
				),
				array(
					'image' => array( sjpta_seed_image( 'singing' ), 'field_sjpta_tb_route_image' ),
					'title' => array( 'LAMDA, singing & acting', 'field_sjpta_tb_route_title' ),
					'text'  => array( 'Exams and one-to-one coaching', 'field_sjpta_tb_route_text' ),
					'url'   => array( '', 'field_sjpta_tb_route_url' ),
				),
				array(
					'image' => array( sjpta_seed_image( 'img_0316' ), 'field_sjpta_tb_route_image' ),
					'title' => array( 'Vocational & college routes', 'field_sjpta_tb_route_title' ),
					'text'  => array( 'For students aiming at the industry', 'field_sjpta_tb_route_text' ),
					'url'   => array( '', 'field_sjpta_tb_route_url' ),
				),
			)
		)
	)
);

// ---------- The SJP experience ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/experience',
	array_merge(
		array(
			'label'        => 'The SJP experience',
			'_label'       => 'field_sjpta_exp_label',
			'label_note'   => 'Classes, friendships and real audiences',
			'_label_note'  => 'field_sjpta_exp_note',
			'link_label'   => 'Gallery',
			'_link_label'  => 'field_sjpta_exp_link_label',
			'link_url'     => '',
			'_link_url'    => 'field_sjpta_exp_link_url',
			'poster'       => sjpta_seed_image( 'full-cast' ),
			'_poster'      => 'field_sjpta_exp_poster',
			'video_title'  => 'Watch a rehearsal',
			'_video_title' => 'field_sjpta_exp_video_title',
			'video_text'   => 'Two minutes inside a normal week at SJP: warm-up, corrections, laughing, and the bit where it clicks.',
			'_video_text'  => 'field_sjpta_exp_video_text',
			// Deliberately empty: the videos are still to be compressed.
			'video_url'    => '',
			'_video_url'   => 'field_sjpta_exp_video_url',
		),
		sjpta_seed_repeater(
			'cards',
			'field_sjpta_exp_cards',
			array(
				array(
					'eyebrow' => array( 'Every December', 'field_sjpta_exp_card_eyebrow' ),
					'tone'    => array( 'orange', 'field_sjpta_exp_card_tone' ),
					'title'   => array( 'The Christmas show', 'field_sjpta_exp_card_title' ),
					'text'    => array( 'Every student who wants a place on stage gets one, from first steps to troupe.', 'field_sjpta_exp_card_text' ),
				),
				array(
					'eyebrow' => array( 'Through the year', 'field_sjpta_exp_card_eyebrow' ),
					'tone'    => array( 'purple', 'field_sjpta_exp_card_tone' ),
					'title'   => array( 'Medals, exams & workshops', 'field_sjpta_exp_card_title' ),
					'text'    => array( 'Awarding-body examinations and workshops with guest choreographers, for students who want them.', 'field_sjpta_exp_card_text' ),
				),
			)
		)
	)
);

// ---------- The team ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/team',
	array_merge(
		array(
			'label'                => 'The team',
			'_label'               => 'field_sjpta_tm_label',
			'label_note'           => 'The people your child will actually see',
			'_label_note'          => 'field_sjpta_tm_note',
			'link_label'           => 'Meet everyone',
			'_link_label'          => 'field_sjpta_tm_link_label',
			'link_url'             => '',
			'_link_url'            => 'field_sjpta_tm_link_url',
			'safeguarding_strong'  => 'Safeguarding.',
			'_safeguarding_strong' => 'field_sjpta_tm_safe_strong',
			'safeguarding_text'    => 'Every teacher is DBS-checked and the school follows an external safeguarding framework. Our full policy is available on request.',
			'_safeguarding_text'   => 'field_sjpta_tm_safe_text',
		),
		sjpta_seed_repeater(
			'people',
			'field_sjpta_tm_people',
			array(
				array(
					'name'  => array( 'SJ', 'field_sjpta_tm_name' ),
					'role'  => array( 'Founder & principal · Acro, Ballroom', 'field_sjpta_tm_role' ),
					'photo' => array( sjpta_seed_image( 'sj' ), 'field_sjpta_tm_photo' ),
					'tone'  => array( 'orange', 'field_sjpta_tm_tone' ),
				),
				array(
					'name'  => array( 'Madi', 'field_sjpta_tm_name' ),
					'role'  => array( 'Musical Theatre, Drama, Singing', 'field_sjpta_tm_role' ),
					'photo' => array( 0, 'field_sjpta_tm_photo' ),
					'tone'  => array( 'magenta', 'field_sjpta_tm_tone' ),
				),
				array(
					'name'  => array( 'Amy', 'field_sjpta_tm_name' ),
					'role'  => array( 'Second Steps, Jazz & Commercial', 'field_sjpta_tm_role' ),
					'photo' => array( 0, 'field_sjpta_tm_photo' ),
					'tone'  => array( 'purple', 'field_sjpta_tm_tone' ),
				),
				array(
					'name'  => array( 'Lottie', 'field_sjpta_tm_name' ),
					'role'  => array( 'Parent & Toddler · first contact', 'field_sjpta_tm_role' ),
					'photo' => array( 0, 'field_sjpta_tm_photo' ),
					'tone'  => array( 'green', 'field_sjpta_tm_tone' ),
				),
			)
		)
	)
);

// ---------- Testimonials ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/testimonials',
	sjpta_seed_repeater(
		'quotes',
		'field_sjpta_q_quotes',
		array(
			array(
				'quote'   => array( 'My daughter has attended from the age of 5, now 13. She would dance here 7 days a week if she could. A unique teacher; you honestly couldn\'t wish for a better role model. Fun, exciting, relaxed, and every child shines.', 'field_sjpta_q_quote' ),
				'name'    => array( 'Natasha Archer', 'field_sjpta_q_name' ),
				'context' => array( 'Parent', 'field_sjpta_q_context' ),
			),
			array(
				'quote'   => array( 'Our daughter attends private classes for young people with additional needs. It has been amazing to see the growth in her ability and in her confidence. A safe, nurturing environment, and she is very proud of her certificates.', 'field_sjpta_q_quote' ),
				'name'    => array( 'Parent of Caitlin', 'field_sjpta_q_name' ),
				'context' => array( 'Private lessons, additional needs', 'field_sjpta_q_context' ),
			),
		)
	)
);

// ---------- Join teaser ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/join-teaser',
	array_merge(
		array(
			'eyebrow'          => 'Join SJP',
			'_eyebrow'         => 'field_sjpta_jt_eyebrow',
			'heading'          => 'Joining is three steps',
			'_heading'         => 'field_sjpta_jt_heading',
			'highlight'        => '',
			'_highlight'       => 'field_sjpta_jt_highlight',
			'intro'            => 'Not sure which class fits? Leave the class blank and we will recommend one.',
			'_intro'           => 'field_sjpta_jt_intro',
			'footnote_strong'  => 'What to wear for a first class:',
			'_footnote_strong' => 'field_sjpta_jt_foot_strong',
			'footnote_text'    => 'comfortable clothes they can move in and bare feet or trainers. No uniform needed until you have decided to join.',
			'_footnote_text'   => 'field_sjpta_jt_foot_text',
			'panel_title'      => 'Enrol now',
			'_panel_title'     => 'field_sjpta_jt_panel_title',
			'panel_text'       => 'Tell us about your child and we will reply with suitable classes and times.',
			'_panel_text'      => 'field_sjpta_jt_panel_text',
			'panel_label'      => 'Start your enquiry',
			'_panel_label'     => 'field_sjpta_jt_panel_label',
			'panel_url'        => '',
			'_panel_url'       => 'field_sjpta_jt_panel_url',
		),
		sjpta_seed_repeater(
			'steps',
			'field_sjpta_jt_steps',
			array(
				array(
					'title' => array( 'Tell us about your child', 'field_sjpta_jt_step_title' ),
					'text'  => array( 'Age, what they are interested in, and whether they have danced before.', 'field_sjpta_jt_step_text' ),
				),
				array(
					'title' => array( 'We reply with class options', 'field_sjpta_jt_step_title' ),
					'text'  => array( 'Lottie will send suitable classes, times and the next available start date.', 'field_sjpta_jt_step_text' ),
				),
				array(
					'title' => array( 'Enrol online', 'field_sjpta_jt_step_title' ),
					'text'  => array( 'Register through the member portal and your place is confirmed for the term.', 'field_sjpta_jt_step_text' ),
				),
			)
		)
	)
);

// ---------- Adult strip ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/adult-strip',
	array(
		'eyebrow'    => array( 'Also for grown-ups', 'field_sjpta_ad_eyebrow' ),
		'heading'    => array( 'Adult ballet, ballroom & wedding dance', 'field_sjpta_ad_heading' ),
		'text'       => array( 'Absolute beginners are the norm, not the exception. Come on your own or with a partner.', 'field_sjpta_ad_text' ),
		'image'      => array( sjpta_seed_image( 'sj' ), 'field_sjpta_ad_image' ),
		'link_label' => array( 'Adult classes', 'field_sjpta_ad_link_label' ),
		'link_url'   => array( '', 'field_sjpta_ad_link_url' ),
	)
);

// ---------- Closing CTA (homepage wording, before the photo strip) ----------
$sjpta_sections[] = sjpta_seed_attrs(
	'sjptheatrearts/cta-band',
	array(
		'heading'        => 'Not sure where to begin?',
		'text'           => 'Tell us a little about your child and we will recommend a class. No obligation, no pressure.',
		'primaryLabel'   => 'Enrol now',
		'secondaryLabel' => 'Timetable & fees',
		'roomy'          => true,
	)
);

// ---------- Photo strip ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/photo-strip',
	array_merge(
		array(
			'handle'  => '@sjptheatrearts',
			'_handle' => 'field_sjpta_ps_handle',
		),
		sjpta_seed_repeater(
			'photos',
			'field_sjpta_ps_photos',
			array_map(
				static function ( $slug ) {
					return array( 'photo' => array( sjpta_seed_image( $slug ), 'field_sjpta_ps_photo' ) );
				},
				array( 'img_0305', 'img_0316', 'img_0318', 'img_8011', 'xmas-2', 'img_0300', 'xmas-3' )
			)
		)
	)
);

sjpta_seed_write( 'home', $sjpta_sections );
