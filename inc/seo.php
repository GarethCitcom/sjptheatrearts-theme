<?php
/**
 * Structured data.
 *
 * Hand-rolled JSON-LD, per the brief. Titles, descriptions, canonicals, Open
 * Graph and the sitemap belong to SiteSEO Pro; **the theme must not emit any of
 * them**, or the two fight and the page ships duplicates. Only schema is ours,
 * and only schema that the data actually supports.
 *
 * The rule throughout: a property whose value nobody has confirmed is left out
 * of the graph entirely. An empty `streetAddress`, or a `startDate` invented to
 * satisfy a validator, is worse than an absent property; search engines surface
 * this to real people looking for a real building on a real evening.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plain text for the graph.
 *
 * JSON-LD is data, not markup, so an HTML entity in it is simply wrong: WordPress
 * runs titles through wptexturize() and "Acro & Cheer" arrives as
 * "Acro &#038; Cheer", which is what a search engine would then print.
 *
 * @param string $text Raw text, possibly entity-encoded.
 *
 * @return string
 */
function sjpta_schema_text( string $text ): string {
	return trim( html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' ) );
}

/**
 * The school itself, on every page.
 *
 * `@id` is stable so the class and breadcrumb graphs can point at it rather than
 * repeating the organisation on every page.
 *
 * @return array<string,mixed>
 */
function sjpta_schema_organisation(): array {
	$locality = sjpta_setting( 'locality', '' );
	$street   = sjpta_setting( 'full_address', '' );
	$email    = sjpta_setting( 'contact_email', '' );

	$node = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/#organisation' ),
		'name'  => sjpta_schema_text( (string) get_bloginfo( 'name' ) ),
		'url'   => home_url( '/' ),
	);

	$logo = SJPTA_URI . '/assets/images/logo.svg';

	if ( file_exists( SJPTA_DIR . '/assets/images/logo.svg' ) ) {
		$node['logo'] = $logo;
	}

	if ( '' !== $email ) {
		$node['email'] = $email;
	}

	/*
	 * Only the accounts that exist. `sameAs` is how a search engine confirms this
	 * is the same organisation it sees elsewhere, and a guessed profile URL links
	 * the school to somebody else's account.
	 */
	$social = array_filter(
		array(
			sjpta_setting( 'instagram_url', '' ),
			sjpta_setting( 'facebook_url', '' ),
			sjpta_setting( 'tiktok_url', '' ),
		)
	);

	if ( ! empty( $social ) ) {
		$node['sameAs'] = array_values( $social );
	}

	/*
	 * The brief asks for LocalBusiness. That type is a promise that this is a
	 * place you can go to, and a place needs an address: the street is still to
	 * be confirmed, so until it is this stays an Organization with the area it
	 * serves. Publishing a LocalBusiness with no street would put the school in
	 * local results with nowhere to send anybody.
	 */
	if ( '' !== $street ) {
		$node['@type']   = array( 'LocalBusiness', 'EducationalOrganization' );
		$node['address'] = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $street,
				'addressLocality' => $locality,
				'addressCountry'  => 'GB',
			)
		);
	} elseif ( '' !== $locality ) {
		$node['areaServed'] = $locality;
	}

	return $node;
}

/**
 * The visible breadcrumb, as a graph node.
 *
 * Mirrors what is on the screen. A BreadcrumbList that disagrees with the
 * breadcrumb a visitor can see is the kind of mismatch that gets a site's rich
 * results dropped.
 *
 * @param array<int,array{0:string,1:string}> $trail Label and URL pairs, last is the current page.
 *
 * @return array<string,mixed>
 */
function sjpta_schema_breadcrumbs( array $trail ): array {
	$items = array();

	foreach ( $trail as $i => $step ) {
		$item = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => sjpta_schema_text( $step[0] ),
		);

		// The current page carries no item URL, per schema.org's own guidance.
		if ( '' !== $step[1] ) {
			$item['item'] = $step[1];
		}

		$items[] = $item;
	}

	return array(
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);
}

/**
 * A class page, as a Course.
 *
 * The schedule comes from the live timetable rather than the class's own fields,
 * because that is where real times exist: fourteen of the fifteen classes have
 * no time typed into them. Where the portal has nothing either, the Course is
 * emitted without `hasCourseInstance` rather than with an empty one.
 *
 * @param int $class_id Class post id.
 *
 * @return array<string,mixed>
 */
function sjpta_schema_course( int $class_id ): array {
	$node = array(
		'@type'    => 'Course',
		'name'     => sjpta_schema_text( (string) get_the_title( $class_id ) ),
		'url'      => (string) get_permalink( $class_id ),
		'provider' => array( '@id' => home_url( '/#organisation' ) ),
	);

	$blurb = trim( (string) sjpta_get_field( 'blurb', $class_id ) );

	if ( '' !== $blurb ) {
		$node['description'] = sjpta_schema_text( $blurb );
	}

	$image = get_the_post_thumbnail_url( $class_id, 'large' );

	if ( $image ) {
		$node['image'] = $image;
	}

	/*
	 * One instance per timetable session that maps to this class, so a class
	 * taught twice a week says so. `courseMode` is onsite because every one of
	 * these happens in a studio.
	 */
	$instances = array();
	$timetable = sjpta_timetable();
	$days      = array(
		1 => 'Monday',
		2 => 'Tuesday',
		3 => 'Wednesday',
		4 => 'Thursday',
		5 => 'Friday',
		6 => 'Saturday',
		7 => 'Sunday',
	);

	foreach ( $timetable['sessions'] as $session ) {
		if ( sjpta_timetable_class_id( $session['name'] ) !== $class_id ) {
			continue;
		}

		$instance = array(
			'@type'      => 'CourseInstance',
			'courseMode' => 'onsite',
			'name'       => sjpta_schema_text( $session['name'] ),
		);

		if ( '' !== $session['start'] && isset( $days[ $session['day'] ] ) ) {
			$instance['courseSchedule'] = array_filter(
				array(
					'@type'            => 'Schedule',
					'byDay'            => 'https://schema.org/' . $days[ $session['day'] ],
					'startTime'        => $session['start'],
					'endTime'          => $session['end'] ?? '',
					'repeatFrequency'  => 'P1W',
					'scheduleTimezone' => $timetable['timezone'],
				)
			);
		}

		if ( ! empty( $session['teachers'] ) ) {
			$instance['instructor'] = array_map(
				static function ( string $name ): array {
					return array(
						'@type' => 'Person',
						'name'  => $name,
					);
				},
				$session['teachers']
			);
		}

		$instances[] = $instance;
	}

	if ( ! empty( $instances ) ) {
		$node['hasCourseInstance'] = $instances;
	}

	/*
	 * Google requires an offers price on a Course. Fees are unconfirmed for every
	 * class, so the property is omitted: the alternative is publishing a price
	 * this school has not set.
	 */
	return $node;
}

/**
 * Print the graph for whatever page is being rendered.
 *
 * One `@graph` per page, so the organisation is stated once and everything else
 * refers to it.
 *
 * @return void
 */
function sjpta_schema(): void {
	if ( is_404() || is_search() ) {
		return;
	}

	$graph = array( sjpta_schema_organisation() );
	$home  = home_url( '/' );

	if ( is_singular( SJPTA_CLASS_POST_TYPE ) ) {
		$id      = (int) get_the_ID();
		$classes = get_page_by_path( 'classes' );

		$graph[] = sjpta_schema_course( $id );
		$graph[] = sjpta_schema_breadcrumbs(
			array(
				array( __( 'Home', 'sjptheatrearts' ), $home ),
				array( __( 'Classes', 'sjptheatrearts' ), $classes ? (string) get_permalink( $classes ) : $home . 'classes/' ),
				array( (string) get_the_title( $id ), '' ),
			)
		);
	} elseif ( is_page() && ! is_front_page() ) {
		$graph[] = sjpta_schema_breadcrumbs(
			array(
				array( __( 'Home', 'sjptheatrearts' ), $home ),
				array( (string) get_the_title(), '' ),
			)
		);
	}

	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);

	if ( false === $json ) {
		return;
	}

	/*
	 * Printed raw rather than escaped: this is a script element, not HTML, and
	 * running it through esc_html() would put &quot; in the JSON and break it.
	 * wp_json_encode() has already escaped the only character that could close
	 * the tag early.
	 */
	printf(
		"<script type=\"application/ld+json\">%s</script>\n",
		$json // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD, encoded above.
	);
}
add_action( 'wp_head', 'sjpta_schema', 20 );
