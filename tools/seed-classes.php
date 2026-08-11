<?php
/**
 * Seed the Classes page.
 *
 * Run with:
 *   wp eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-classes.php
 *
 * Development tooling. The class cards themselves come from the sjp-class post
 * type, so this only seeds the page's own copy: the hero, the two section bars
 * and the help card.
 *
 * @package SJPTheatreArts
 */

// No declare(strict_types=1) here: `wp eval-file` eval()s this file.

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

require_once SJPTA_DIR . '/tools/seed-lib.php';

/*
 * The design says "Thirteen class styles" against its own thirteen-class set.
 * The count is read from the data instead, so the sentence stays true when the
 * client adds or drops one.
 */
$sjpta_total = sjpta_class_count();
$sjpta_intro = sprintf(
	/* translators: %s: number of classes, spelled out. */
	'%s class styles across four age routes. Start with your child\'s age, then narrow by what they are drawn to.',
	sjpta_spell_number( $sjpta_total )
);

$sjpta_sections = array();

// ---------- Hero ----------
$sjpta_sections[] = sjpta_seed_raw(
	'sjptheatrearts/page-hero',
	array(
		'layout'             => 'centred',
		'_layout'            => 'field_sjpta_ph_layout',
		'breadcrumb'         => 'Classes',
		'_breadcrumb'        => 'field_sjpta_ph_breadcrumb',
		'eyebrow'            => '',
		'_eyebrow'           => 'field_sjpta_ph_eyebrow',
		'heading'            => 'Find the right class.',
		'_heading'           => 'field_sjpta_ph_heading',
		'heading_highlight'  => 'class.',
		'_heading_highlight' => 'field_sjpta_ph_highlight',
		'intro'              => $sjpta_intro,
		'_intro'             => 'field_sjpta_ph_intro',
		'cta_label'          => '',
		'_cta_label'         => 'field_sjpta_ph_cta_label',
		'alt_label'          => '',
		'_alt_label'         => 'field_sjpta_ph_alt_label',
	),
	array( 'anchor' => 'top' )
);

// ---------- Age routes ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/class-routes',
	array(
		'label' => array( 'Start with an age route', 'field_sjpta_cr_label' ),
		'note'  => array( 'Pick the route that fits and we will show every class that suits', 'field_sjpta_cr_note' ),
	)
);

// ---------- Filter bar ----------
$sjpta_sections[] = sjpta_seed_attrs( 'sjptheatrearts/class-filters', array( 'anchor' => 'filters' ) );

// ---------- All classes ----------
$sjpta_sections[] = sjpta_seed_block(
	'sjptheatrearts/class-grid',
	array(
		'label'      => array( 'All classes', 'field_sjpta_cg_label' ),
		'cta_label'  => array( 'Ask us to recommend', 'field_sjpta_cg_cta_label' ),
		'cta_url'    => array( '', 'field_sjpta_cg_cta_url' ),
		'help_title' => array( 'Still not sure?', 'field_sjpta_cg_help_title' ),
		'help_text'  => array( 'Tell us your child\'s age and what they enjoy. We will come back with two or three classes that would suit, and when they run.', 'field_sjpta_cg_help_text' ),
	),
	array( 'anchor' => 'all' )
);

sjpta_seed_write( 'classes', $sjpta_sections );
