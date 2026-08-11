<?php
/**
 * Seed the Born To Be page with its designed content.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-born-to-be.php
 *
 * Development tooling, not shipped behaviour. Copy is taken verbatim from
 * design-reference/SJP Born To Be Alt.dc.html; nothing here is invented.
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

$sjpta_classes_page = get_page_by_path( 'classes' );
$sjpta_classes_url  = $sjpta_classes_page ? get_permalink( $sjpta_classes_page ) : home_url( '/classes/' );

$sjpta_sections = array();

// ---------- 2. Hero ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/page-hero',
	array_merge(
		array(
			'layout'             => 'split',
			'_layout'            => 'field_sjpta_ph_layout',
			'breadcrumb'         => 'Born To Be',
			'_breadcrumb'        => 'field_sjpta_ph_breadcrumb',
			'logo'               => sjpta_seed_image( 'born-to-be-logo' ),
			'_logo'              => 'field_sjpta_ph_logo',
			'eyebrow'            => '',
			'_eyebrow'           => 'field_sjpta_ph_eyebrow',
			'heading'            => 'Dance, sing and act. All in one class.',
			'_heading'           => 'field_sjpta_ph_heading',
			'heading_highlight'  => 'All in one class.',
			'_heading_highlight' => 'field_sjpta_ph_highlight',
			'intro'              => 'Born To Be is the musical theatre company behind every musical theatre class at SJP. Wednesday evenings in Bromsgrove, working towards shows, concerts, workshops and trips.',
			'_intro'             => 'field_sjpta_ph_intro',
			'cta_label'          => 'Enquire about a place',
			'_cta_label'         => 'field_sjpta_ph_cta_label',
			'cta_url'            => '#enquire',
			'_cta_url'           => 'field_sjpta_ph_cta_url',
			'alt_label'          => 'See the two classes',
			'_alt_label'         => 'field_sjpta_ph_alt_label',
			'alt_url'            => '#classes',
			'_alt_url'           => 'field_sjpta_ph_alt_url',
			'photo_main'         => sjpta_seed_image( 'born-to-be-cast-seussical' ),
			'_photo_main'        => 'field_sjpta_ph_photo_main',
			'photo_inset'        => sjpta_seed_image( 'born-to-be-costume-closeup' ),
			'_photo_inset'       => 'field_sjpta_ph_photo_inset',
			'card_heading'       => 'Wednesdays',
			'_card_heading'      => 'field_sjpta_ph_card_heading',
			'card_note'          => 'Junior 5–6pm · Senior 6–7pm',
			'_card_note'         => 'field_sjpta_ph_card_note',
		),
		sjpta_seed_repeater(
			'points',
			'field_sjpta_ph_points',
			array(
				array(
					'text' => array( 'No dance classes needed', 'field_sjpta_ph_point_text' ),
					'tone' => array( 'accent', 'field_sjpta_ph_point_tone' ),
				),
				array(
					'text' => array( 'No experience needed', 'field_sjpta_ph_point_text' ),
					'tone' => array( 'soft', 'field_sjpta_ph_point_tone' ),
				),
				array(
					'text' => array( 'Everyone gets a place on stage', 'field_sjpta_ph_point_text' ),
					'tone' => array( 'deep', 'field_sjpta_ph_point_tone' ),
				),
			)
		)
	),
	array( 'anchor' => 'top' )
);

// ---------- 3. Reassurance band ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/adult-strip',
	array(
		'tone'        => array( 'purple', 'field_sjpta_ad_tone' ),
		'eyebrow'     => array( '', 'field_sjpta_ad_eyebrow' ),
		'heading'     => array( 'You do not need to dance with SJP to join Born To Be', 'field_sjpta_ad_heading' ),
		'text'        => array( 'Plenty of our students come on a Wednesday and take no other class. Plenty of SJP students add musical theatre to the dance classes they already take. Both are completely normal. Booking is handled through the SJP member portal, so you will set up an SJP account to reserve a place, but that is admin rather than a commitment to the dance school.', 'field_sjpta_ad_text' ),
		'icon'        => array( 'shield-tick', 'field_sjpta_ad_icon' ),
		'image'       => array( '', 'field_sjpta_ad_image' ),
		'link_label'  => array( 'See the difference', 'field_sjpta_ad_link_label' ),
		'link_url'    => array( '#difference', 'field_sjpta_ad_link_url' ),
		'button_tone' => array( 'white', 'field_sjpta_ad_button_tone' ),
	)
);

// ---------- 4. What Born To Be is ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/feature-cards',
	array_merge(
		array(
			'eyebrow'            => 'What Born To Be is',
			'_eyebrow'           => 'field_sjpta_fc_eyebrow',
			'heading'            => 'Quality training in all things musical theatre.',
			'_heading'           => 'field_sjpta_fc_heading',
			'heading_highlight'  => '',
			'_heading_highlight' => 'field_sjpta_fc_highlight',
			'body'               => "Dance, singing and acting are taught together rather than as separate subjects, because that is how they work on a stage. Students learn through weekly classes, then put it all to use in shows and concerts.\n\nMusical theatre is not only about performing. It is teamwork, building a character, reading a script and knowing how a show is put together. We cover all of it, and we try to make every part of it fun.",
			'_body'              => 'field_sjpta_fc_body',
			'note'               => 'Every student is guaranteed a place on stage in our yearly shows and concerts. Nobody sits a show out because they are new, quiet or still finding their feet.',
			'_note'              => 'field_sjpta_fc_note',
		),
		sjpta_seed_repeater(
			'cards',
			'field_sjpta_fc_cards',
			array(
				array(
					'icon'  => array( 'mic', 'field_sjpta_fc_card_icon' ),
					'title' => array( 'Singing', 'field_sjpta_fc_card_title' ),
					'text'  => array( 'Solo lines, group numbers and harmony, sung as the character rather than as a performer standing still.', 'field_sjpta_fc_card_text' ),
					'tone'  => array( 'red', 'field_sjpta_fc_card_tone' ),
				),
				array(
					'icon'  => array( 'dance', 'field_sjpta_fc_card_icon' ),
					'title' => array( 'Dance', 'field_sjpta_fc_card_title' ),
					'text'  => array( 'Show choreography learned as a company, with the storytelling that goes with it. Come with dance training or without.', 'field_sjpta_fc_card_text' ),
					'tone'  => array( 'plum', 'field_sjpta_fc_card_tone' ),
				),
				array(
					'icon'  => array( 'script', 'field_sjpta_fc_card_icon' ),
					'title' => array( 'Acting', 'field_sjpta_fc_card_title' ),
					'text'  => array( 'Script work, character building and scene study, so students know who they are playing and why.', 'field_sjpta_fc_card_text' ),
					'tone'  => array( 'amber', 'field_sjpta_fc_card_tone' ),
				),
				array(
					'icon'  => array( 'backstage', 'field_sjpta_fc_card_icon' ),
					'title' => array( 'Backstage', 'field_sjpta_fc_card_title' ),
					'text'  => array( 'Costume, sound, lighting and stage crew. There is a place in a show for students who would rather be in the wings.', 'field_sjpta_fc_card_text' ),
					'tone'  => array( 'mint', 'field_sjpta_fc_card_tone' ),
				),
			)
		)
	)
);

// ---------- 5. Comparison ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/comparison',
	array_merge(
		array(
			'eyebrow'   => 'Two companies, one building',
			'_eyebrow'  => 'field_sjpta_cp_eyebrow',
			'heading'   => 'Which one is which?',
			'_heading'  => 'field_sjpta_cp_heading',
			'intro'     => 'They share a home, a stage and a lot of the same faces. Here is what each one actually does.',
			'_intro'    => 'field_sjpta_cp_intro',
			'footnote'  => 'All musical theatre classes at the studio are run by Born To Be. If your child already dances with SJP, adding musical theatre is one message to us. If they do not, that is fine too.',
			'_footnote' => 'field_sjpta_cp_footnote',
		),
		sjpta_seed_repeater(
			'cards',
			'field_sjpta_cp_cards',
			array(
				array(
					'mark'       => array( 'sjp', 'field_sjpta_cp_card_mark' ),
					'logo'       => array( '', 'field_sjpta_cp_card_logo' ),
					'title'      => array( 'A dance and performing arts school', 'field_sjpta_cp_card_title' ),
					'text'       => array( 'Weekly graded classes in a style your child chooses, taken term after term.', 'field_sjpta_cp_card_text' ),
					'link_label' => array( 'See SJP classes', 'field_sjpta_cp_card_link_label' ),
					'link_url'   => array( $sjpta_classes_url, 'field_sjpta_cp_card_link_url' ),
					'emphasis'   => array( 0, 'field_sjpta_cp_card_emphasis' ),
					'rows'       => array(
						'__rep' => array(
							'field_sjpta_cp_card_rows',
							array(
								array(
									'label' => array( 'Who for', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Babies to adults, all abilities', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'Covers', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Ballet, tap, jazz, acro, ballroom, drama, singing', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'When', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Classes across the week', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'Leads to', 'field_sjpta_cp_row_label' ),
									'value' => array( 'IDTA and LAMDA examinations, medals, troupes', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'Run by', 'field_sjpta_cp_row_label' ),
									'value' => array( 'SJ and the SJP teaching team', 'field_sjpta_cp_row_value' ),
								),
							),
						),
					),
				),
				array(
					'mark'       => array( 'image', 'field_sjpta_cp_card_mark' ),
					'logo'       => array( sjpta_seed_image( 'born-to-be-logo' ), 'field_sjpta_cp_card_logo' ),
					'title'      => array( 'A musical theatre company', 'field_sjpta_cp_card_title' ),
					'text'       => array( 'Dance, singing and acting taught together, aimed at putting a show on its feet.', 'field_sjpta_cp_card_text' ),
					'link_label' => array( 'See Born To Be classes', 'field_sjpta_cp_card_link_label' ),
					'link_url'   => array( '#classes', 'field_sjpta_cp_card_link_url' ),
					'emphasis'   => array( 1, 'field_sjpta_cp_card_emphasis' ),
					'rows'       => array(
						'__rep' => array(
							'field_sjpta_cp_card_rows',
							array(
								array(
									'label' => array( 'Who for', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Anyone who wants to perform, dance training or not', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'Covers', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Musical theatre, script work, harmony, backstage roles', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'When', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Wednesday evenings, junior and senior', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'Leads to', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Yearly shows, concerts, workshops and trips', 'field_sjpta_cp_row_value' ),
								),
								array(
									'label' => array( 'Run by', 'field_sjpta_cp_row_label' ),
									'value' => array( 'Madison Copson', 'field_sjpta_cp_row_value' ),
								),
							),
						),
					),
				),
			)
		)
	),
	array( 'anchor' => 'difference' )
);

// ---------- 6. The two classes ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/class-cards',
	array_merge(
		array(
			'eyebrow'            => 'Weekly classes',
			'_eyebrow'           => 'field_sjpta_cc_eyebrow',
			'heading'            => 'Two classes, both on a Wednesday.',
			'_heading'           => 'field_sjpta_cc_heading',
			'heading_highlight'  => '',
			'_heading_highlight' => 'field_sjpta_cc_highlight',
			'note'               => 'Not sure which group your child belongs in? Tell us their age and we will point you at the right one.',
			'_note'              => 'field_sjpta_cc_note',
		),
		sjpta_seed_repeater(
			'cards',
			'field_sjpta_cc_cards',
			array(
				array(
					'image'      => array( sjpta_seed_image( 'born-to-be-rehearsal-jump' ), 'field_sjpta_cc_card_image' ),
					'badge'      => array( 'Junior', 'field_sjpta_cc_card_badge' ),
					'badge_tone' => array( 'accent', 'field_sjpta_cc_card_badge_tone' ),
					'title'      => array( 'Junior Musical Theatre', 'field_sjpta_cc_card_title' ),
					'text'       => array( 'The first taste of musical theatre. Big group numbers, characters to play and games that quietly teach stagecraft.', 'field_sjpta_cc_card_text' ),
					'cta_label'  => array( 'Enquire about Junior', 'field_sjpta_cc_card_cta_label' ),
					'cta_url'    => array( '#enquire', 'field_sjpta_cc_card_cta_url' ),
					'cta_tone'   => array( 'accent', 'field_sjpta_cc_card_cta_tone' ),
					'meta'       => array(
						'__rep' => array(
							'field_sjpta_cc_card_meta',
							array(
								array( 'text' => array( 'Wednesday 5:00–6:00pm', 'field_sjpta_cc_meta_text' ) ),
								array( 'text' => array( 'Madison', 'field_sjpta_cc_meta_text' ) ),
							),
						),
					),
				),
				array(
					'image'      => array( sjpta_seed_image( 'born-to-be-rehearsal-alice' ), 'field_sjpta_cc_card_image' ),
					'badge'      => array( 'Senior', 'field_sjpta_cc_card_badge' ),
					'badge_tone' => array( 'deep', 'field_sjpta_cc_card_badge_tone' ),
					'title'      => array( 'Senior Musical Theatre', 'field_sjpta_cc_card_title' ),
					'text'       => array( 'Longer scenes, harder harmonies and real script work, with solo and featured roles for students who want them.', 'field_sjpta_cc_card_text' ),
					'cta_label'  => array( 'Enquire about Senior', 'field_sjpta_cc_card_cta_label' ),
					'cta_url'    => array( '#enquire', 'field_sjpta_cc_card_cta_url' ),
					'cta_tone'   => array( 'plum', 'field_sjpta_cc_card_cta_tone' ),
					'meta'       => array(
						'__rep' => array(
							'field_sjpta_cc_card_meta',
							array(
								array( 'text' => array( 'Wednesday 6:00–7:00pm', 'field_sjpta_cc_meta_text' ) ),
								array( 'text' => array( 'Madison', 'field_sjpta_cc_meta_text' ) ),
							),
						),
					),
				),
			)
		)
	),
	array( 'anchor' => 'classes' )
);

// ---------- 7. Inside a session ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/step-grid',
	array_merge(
		array(
			'eyebrow'         => 'Inside a session',
			'_eyebrow'        => 'field_sjpta_sg_eyebrow',
			'heading'         => 'What an hour actually looks like.',
			'_heading'        => 'field_sjpta_sg_heading',
			'note'            => 'Every week is a mix, never all of one thing',
			'_note'           => 'field_sjpta_sg_note',
			'footer_heading'  => 'Not everyone wants the spotlight',
			'_footer_heading' => 'field_sjpta_sg_footer_heading',
			'footer_text'     => 'Students can learn the jobs that make a show happen: costume, sound, lighting and stage crew. It is a proper route through Born To Be, not a consolation prize.',
			'_footer_text'    => 'field_sjpta_sg_footer_text',
		),
		sjpta_seed_repeater(
			'steps',
			'field_sjpta_sg_steps',
			array(
				array(
					'title'  => array( 'Warm up together', 'field_sjpta_sg_step_title' ),
					'text'   => array( 'Bodies and voices, as a company. It is also where friendships get made.', 'field_sjpta_sg_step_text' ),
					'invert' => array( 0, 'field_sjpta_sg_step_invert' ),
				),
				array(
					'title'  => array( 'Vocals and harmony', 'field_sjpta_sg_step_title' ),
					'text'   => array( 'Learning the number, then splitting into parts and putting it back together.', 'field_sjpta_sg_step_text' ),
					'invert' => array( 0, 'field_sjpta_sg_step_invert' ),
				),
				array(
					'title'  => array( 'Choreography', 'field_sjpta_sg_step_title' ),
					'text'   => array( 'Show routines taught in sections, with the story behind each move explained.', 'field_sjpta_sg_step_text' ),
					'invert' => array( 0, 'field_sjpta_sg_step_invert' ),
				),
				array(
					'title'  => array( 'Script and character', 'field_sjpta_sg_step_title' ),
					'text'   => array( 'Reading scenes, trying accents, working out who the character is and what they want.', 'field_sjpta_sg_step_text' ),
					'invert' => array( 0, 'field_sjpta_sg_step_invert' ),
				),
				array(
					'title'  => array( 'Games and teamwork', 'field_sjpta_sg_step_title' ),
					'text'   => array( 'Improvisation and ensemble games. The quickest way to get a shy student talking.', 'field_sjpta_sg_step_text' ),
					'invert' => array( 0, 'field_sjpta_sg_step_invert' ),
				),
				array(
					'title'  => array( 'Run it', 'field_sjpta_sg_step_title' ),
					'text'   => array( 'Putting the pieces together, week on week, until it is a show.', 'field_sjpta_sg_step_text' ),
					'invert' => array( 1, 'field_sjpta_sg_step_invert' ),
				),
			)
		),
		sjpta_seed_repeater(
			'pills',
			'field_sjpta_sg_pills',
			array(
				array(
					'text' => array( 'Costume', 'field_sjpta_sg_pill_text' ),
					'tone' => array( 'red', 'field_sjpta_sg_pill_tone' ),
				),
				array(
					'text' => array( 'Sound', 'field_sjpta_sg_pill_text' ),
					'tone' => array( 'plum', 'field_sjpta_sg_pill_tone' ),
				),
				array(
					'text' => array( 'Lighting', 'field_sjpta_sg_pill_text' ),
					'tone' => array( 'amber', 'field_sjpta_sg_pill_tone' ),
				),
				array(
					'text' => array( 'Stage crew', 'field_sjpta_sg_pill_text' ),
					'tone' => array( 'mint', 'field_sjpta_sg_pill_tone' ),
				),
			)
		)
	)
);

// ---------- 8. Shows and concerts ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/dark-feature',
	array_merge(
		array(
			'eyebrow'    => 'Shows and concerts',
			'_eyebrow'   => 'field_sjpta_df_eyebrow',
			'heading'    => 'Everything points at a stage.',
			'_heading'   => 'field_sjpta_df_heading',
			'text'       => 'A yearly show, concerts through the year, and the odd workshop or trip in between. Students see the whole arc: first read-through, first run, tech, then an audience.',
			'_text'      => 'field_sjpta_df_text',
			'cta_label'  => 'See past performances',
			'_cta_label' => 'field_sjpta_df_cta_label',
			'cta_url'    => '',
			'_cta_url'   => 'field_sjpta_df_cta_url',
		),
		sjpta_seed_repeater(
			'points',
			'field_sjpta_df_points',
			array(
				array( 'text' => array( 'A guaranteed part for every student, every year', 'field_sjpta_df_point_text' ) ),
				array( 'text' => array( 'Full sets, costumes and lighting on a real stage', 'field_sjpta_df_point_text' ) ),
				array( 'text' => array( 'Workshops and trips to see musical theatre done professionally', 'field_sjpta_df_point_text' ) ),
			)
		),
		sjpta_seed_repeater(
			'photos',
			'field_sjpta_df_photos',
			array_map(
				static function ( $slug ) {
					return array( 'photo' => array( sjpta_seed_image( $slug ), 'field_sjpta_df_photo' ) );
				},
				array( 'born-to-be-cast-group', 'born-to-be-seussical-scene', 'born-to-be-solo-stage' )
			)
		)
	)
);

// ---------- 9. Who runs it ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/bio-feature',
	array_merge(
		array(
			'eyebrow'      => 'Who runs it',
			'_eyebrow'     => 'field_sjpta_bf_eyebrow',
			'heading'      => 'Madison Copson',
			'_heading'     => 'field_sjpta_bf_heading',
			'body'         => "Madison has always had a passion for dancing, acting and singing, and knew from a young age that she wanted to be on stage. She was a student at SJP Theatre Arts for over thirteen years, working through different styles of dance and taking part in show after show.\n\nShe was a keen member of Notorious dance troupe as well as dance captain for junior troupe Shockwave. She now teaches musical theatre and singing at SJP, and runs Born To Be.",
			'_body'        => 'field_sjpta_bf_body',
			'photo_main'   => sjpta_seed_image( 'born-to-be-seussical-solo' ),
			'_photo_main'  => 'field_sjpta_bf_photo_main',
			'photo_inset'  => sjpta_seed_image( 'born-to-be-rehearsal-costumes' ),
			'_photo_inset' => 'field_sjpta_bf_photo_inset',
		),
		sjpta_seed_repeater(
			'pills',
			'field_sjpta_bf_pills',
			array(
				array( 'text' => array( '13 years an SJP student', 'field_sjpta_bf_pill_text' ) ),
				array( 'text' => array( 'Notorious troupe', 'field_sjpta_bf_pill_text' ) ),
				array( 'text' => array( 'Dance captain, Shockwave', 'field_sjpta_bf_pill_text' ) ),
			)
		)
	)
);

// ---------- 10. Gallery ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/gallery-mosaic',
	array_merge(
		array(
			'eyebrow'     => 'Gallery',
			'_eyebrow'    => 'field_sjpta_gm_eyebrow',
			'heading'     => 'Rehearsal room to opening night.',
			'_heading'    => 'field_sjpta_gm_heading',
			'link_label'  => 'More on Instagram',
			'_link_label' => 'field_sjpta_gm_link_label',
			'link_url'    => sjpta_setting( 'btb_instagram_url', 'https://www.instagram.com/born.to.be4/' ),
			'_link_url'   => 'field_sjpta_gm_link_url',
			'note'        => 'Photographs are published only where a family has given consent.',
			'_note'       => 'field_sjpta_gm_note',
		),
		sjpta_seed_repeater(
			'photos',
			'field_sjpta_gm_photos',
			array_map(
				static function ( $row ) {
					return array(
						'photo' => array( sjpta_seed_image( $row[0] ), 'field_sjpta_gm_photo' ),
						'span'  => array( $row[1], 'field_sjpta_gm_span' ),
					);
				},
				array(
					array( 'born-to-be-rehearsal-group', 'large' ),
					array( 'born-to-be-costume-closeup', 'normal' ),
					array( 'born-to-be-solo-stage', 'normal' ),
					array( 'born-to-be-cast-seussical', 'wide' ),
					array( 'born-to-be-rehearsal-alice', 'normal' ),
					array( 'born-to-be-seussical-scene', 'normal' ),
					array( 'born-to-be-cast-group', 'wide' ),
					array( 'born-to-be-rehearsal-jump', 'normal' ),
					array( 'born-to-be-seussical-solo', 'normal' ),
				)
			)
		)
	),
	array( 'anchor' => 'gallery' )
);

// ---------- 11. Questions ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/faq',
	array_merge(
		array(
			'eyebrow'     => 'Questions',
			'_eyebrow'    => 'field_sjpta_fq_eyebrow',
			'heading'     => 'The things parents ask first.',
			'_heading'    => 'field_sjpta_fq_heading',
			'intro'       => 'If yours is not here, send it over. We would rather answer it before your child walks in than after.',
			'_intro'      => 'field_sjpta_fq_intro',
			'link_label'  => 'Ask us something',
			'_link_label' => 'field_sjpta_fq_link_label',
			'link_url'    => '#enquire',
			'_link_url'   => 'field_sjpta_fq_link_url',
		),
		sjpta_seed_repeater(
			'items',
			'field_sjpta_fq_items',
			array(
				array(
					'question' => array( 'Does my child have to take dance classes at SJP?', 'field_sjpta_fq_question' ),
					'answer'   => array( 'No. Born To Be is its own company and takes its own students. Some children come on a Wednesday and take no other class; others dance with SJP through the week and add musical theatre. Either is fine. Places are booked through the SJP member portal, so you will set up an SJP account to reserve one, but that does not mean signing up to the dance school.', 'field_sjpta_fq_answer' ),
					'open'     => array( 1, 'field_sjpta_fq_open' ),
				),
				array(
					'question' => array( 'So what is the difference between the two?', 'field_sjpta_fq_question' ),
					'answer'   => array( 'SJP Theatre Arts is a dance and performing arts school teaching separate graded styles across the week. Born To Be is a musical theatre company that teaches dance, singing and acting together and puts on shows. Born To Be runs every musical theatre class at the studio.', 'field_sjpta_fq_answer' ),
					'open'     => array( 0, 'field_sjpta_fq_open' ),
				),
				array(
					'question' => array( 'Does my child need experience?', 'field_sjpta_fq_question' ),
					'answer'   => array( 'None at all. Some students have danced for years, some have never been in a class before. The class is built so both can stand in the same room and get something out of it.', 'field_sjpta_fq_answer' ),
					'open'     => array( 0, 'field_sjpta_fq_open' ),
				),
				array(
					'question' => array( 'Do they have to perform?', 'field_sjpta_fq_question' ),
					'answer'   => array( 'Every student is offered a place in the show, and most take it. If a child would rather be behind the scenes, there are real backstage jobs for them instead. Nobody is pushed on stage before they are ready.', 'field_sjpta_fq_answer' ),
					'open'     => array( 0, 'field_sjpta_fq_open' ),
				),
				array(
					'question' => array( 'What should they wear and bring?', 'field_sjpta_fq_question' ),
					'answer'   => array( 'Comfortable clothes they can move in, trainers, and a water bottle. No uniform is needed to start.', 'field_sjpta_fq_answer' ),
					'open'     => array( 0, 'field_sjpta_fq_open' ),
				),
				array(
					'question' => array( 'Can we come and watch first?', 'field_sjpta_fq_question' ),
					'answer'   => array( 'Send us a message and we will tell you which Wednesday to come along. It is usually easier to try a class than to watch one.', 'field_sjpta_fq_answer' ),
					'open'     => array( 0, 'field_sjpta_fq_open' ),
				),
			)
		)
	)
);

/*
 * ---------- 12. Enquiry ----------
 *
 * Routed to Madison rather than SJ. The address itself lives in SJP settings, so
 * this only records which mailbox to use.
 */
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/enquiry-panel',
	array_merge(
		array(
			'logo'          => sjpta_seed_image( 'born-to-be-logo' ),
			'_logo'         => 'field_sjpta_ep_logo',
			'heading'       => 'Come and join us on a Wednesday.',
			'_heading'      => 'field_sjpta_ep_heading',
			'intro'         => 'Tell us your child\'s age and we will tell you which class fits, what it costs and when to come along. We answer every message ourselves.',
			'_intro'        => 'field_sjpta_ep_intro',
			'recipient'     => 'madison',
			'_recipient'    => 'field_sjpta_ep_recipient',
			'form_heading'  => '',
			'_form_heading' => 'field_sjpta_ep_form_heading',
			'form_text'     => '',
			'_form_text'    => 'field_sjpta_ep_form_text',
			'submit_label'  => 'Send enquiry',
			'_submit_label' => 'field_sjpta_ep_submit_label',
			'consent'       => 'We use your details only to answer this enquiry. Nothing is shared with anyone else.',
			'_consent'      => 'field_sjpta_ep_consent',
			'sent_heading'  => 'Thank you, that is on its way.',
			'_sent_heading' => 'field_sjpta_ep_sent_heading',
			'sent_text'     => 'Madison answers every message herself, so it may take a day or two. If it is urgent, please ring us.',
			'_sent_text'    => 'field_sjpta_ep_sent_text',
		),
		sjpta_seed_repeater(
			'points',
			'field_sjpta_ep_points',
			array(
				array(
					'text' => array( 'Junior 5:00–6:00pm, Senior 6:00–7:00pm', 'field_sjpta_ep_point_text' ),
					'tone' => array( 'accent', 'field_sjpta_ep_point_tone' ),
				),
				array(
					'text' => array( 'Bromsgrove, Worcestershire', 'field_sjpta_ep_point_text' ),
					'tone' => array( 'deep', 'field_sjpta_ep_point_tone' ),
				),
				array(
					'text' => array( 'Booked through the SJP member portal', 'field_sjpta_ep_point_text' ),
					'tone' => array( 'sjp', 'field_sjpta_ep_point_tone' ),
				),
			)
		),
		sjpta_seed_repeater(
			'class_options',
			'field_sjpta_ep_class_options',
			array(
				array( 'text' => array( 'Not sure yet, please advise', 'field_sjpta_ep_class_text' ) ),
				array( 'text' => array( 'Junior, Wednesday 5:00–6:00pm', 'field_sjpta_ep_class_text' ) ),
				array( 'text' => array( 'Senior, Wednesday 6:00–7:00pm', 'field_sjpta_ep_class_text' ) ),
			)
		)
	),
	array( 'anchor' => 'enquire' )
);

// ---------- 13. Social cards ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/link-cards',
	sjpta_seed_repeater(
		'cards',
		'field_sjpta_lc_cards',
		array(
			array(
				'icon'  => array( 'instagram', 'field_sjpta_lc_card_icon' ),
				'tone'  => array( 'accent', 'field_sjpta_lc_card_tone' ),
				'title' => array( '@born.to.be4', 'field_sjpta_lc_card_title' ),
				'text'  => array( 'Rehearsal clips, show nights and cast announcements', 'field_sjpta_lc_card_text' ),
				'url'   => array( sjpta_setting( 'btb_instagram_url', 'https://www.instagram.com/born.to.be4/' ), 'field_sjpta_lc_card_url' ),
			),
			array(
				'icon'  => array( 'facebook', 'field_sjpta_lc_card_icon' ),
				'tone'  => array( 'plum', 'field_sjpta_lc_card_tone' ),
				'title' => array( 'Born To Be on Facebook', 'field_sjpta_lc_card_title' ),
				'text'  => array( 'Show dates, ticket links and news for parents', 'field_sjpta_lc_card_text' ),
				'url'   => array( sjpta_setting( 'btb_facebook_url', 'https://www.facebook.com/p/Born-To-Be-61585014057919/' ), 'field_sjpta_lc_card_url' ),
			),
		)
	)
);

sjpta_seed_write( 'born-to-be', $sjpta_sections );
