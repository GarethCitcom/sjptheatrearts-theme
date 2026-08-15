<?php
/**
 * The 404 page.
 *
 * Playful rather than apologetic: three big numerals that bounce onto the stage,
 * a spotlight ring on the zero, and a few slow sparkles. Underneath the fun it is
 * a recovery page, so it carries a real search form, the two main actions and a
 * row of routes into the parts of the site a lost visitor was most likely after.
 *
 * Nothing here needs JavaScript. The bounce, the ring and the sparkles are CSS
 * keyframes and all stop under prefers-reduced-motion; the search form is a plain
 * GET to the homepage; every route is a real link. Routes only render when their
 * target exists, because a 404 page that links to a second 404 is a poor joke.
 *
 * @package SJPTheatreArts
 *
 * @var array $attributes Block attributes.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_eyebrow = isset( $attributes['eyebrow'] ) ? trim( (string) $attributes['eyebrow'] ) : '';
$sjpta_heading = isset( $attributes['heading'] ) ? trim( (string) $attributes['heading'] ) : '';
$sjpta_text    = isset( $attributes['text'] ) ? trim( (string) $attributes['text'] ) : '';
$sjpta_aside   = isset( $attributes['aside'] ) ? trim( (string) $attributes['aside'] ) : '';

if ( '' === $sjpta_heading ) {
	$sjpta_heading = __( 'This page missed its cue', 'sjptheatrearts' );
}

/*
 * Destinations. The classes page falls back to its designed path, as the CTA
 * band does, because "See all classes" is the one route worth showing even
 * before the page is published. The pills below are stricter.
 */
$sjpta_classes_page = get_page_by_path( 'classes' );
$sjpta_classes_url  = $sjpta_classes_page ? (string) get_permalink( $sjpta_classes_page ) : home_url( '/classes/' );

$sjpta_routes = array();

foreach ( sjpta_age_routes() as $sjpta_slug => $sjpta_route ) {
	if ( 0 === sjpta_class_count( 'age-group', $sjpta_slug ) ) {
		continue;
	}

	$sjpta_routes[] = array(
		'label' => $sjpta_route['name'],
		'url'   => add_query_arg( 'age', $sjpta_slug, $sjpta_classes_url ) . '#all',
		'tone'  => $sjpta_route['tone'],
		'icon'  => $sjpta_route['icon'],
	);
}

$sjpta_pages = array(
	'timetable-and-fees' => array( __( 'Timetable & fees', 'sjptheatrearts' ), 'calendar' ),
	'join'               => array( __( 'Enrol now', 'sjptheatrearts' ), 'arrow-right' ),
	'about'              => array( __( 'About us', 'sjptheatrearts' ), 'heart' ),
	'contact'            => array( __( 'Contact', 'sjptheatrearts' ), 'mail' ),
);

foreach ( $sjpta_pages as $sjpta_slug => $sjpta_page ) {
	$sjpta_found = get_page_by_path( $sjpta_slug );

	if ( ! $sjpta_found instanceof WP_Post || 'publish' !== $sjpta_found->post_status ) {
		continue;
	}

	$sjpta_routes[] = array(
		'label' => $sjpta_page[0],
		'url'   => (string) get_permalink( $sjpta_found ),
		'tone'  => 'plain',
		'icon'  => $sjpta_page[1],
	);
}

$sjpta_search_id = 'sjpta-404-search';
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'sjpta-404 sjpta-underlap alignfull' ) ) ); ?>>
	<div class="sjpta-404__decor" aria-hidden="true">
		<span class="sjpta-404__spark sjpta-404__spark--1"></span>
		<span class="sjpta-404__spark sjpta-404__spark--2"></span>
		<span class="sjpta-404__spark sjpta-404__spark--3"></span>
		<span class="sjpta-404__spark sjpta-404__spark--4"></span>
		<span class="sjpta-404__spark sjpta-404__spark--5"></span>
		<span class="sjpta-404__spark sjpta-404__spark--6"></span>
	</div>

	<div class="sjpta-inner sjpta-404__inner">
		<div class="sjpta-404__figure" aria-hidden="true">
			<span class="sjpta-404__digit sjpta-404__digit--left">4</span>
			<span class="sjpta-404__digit sjpta-404__digit--zero">
				<span class="sjpta-404__ring"></span>
				<span class="sjpta-404__glyph">0</span>
				<span class="sjpta-404__star"></span>
			</span>
			<span class="sjpta-404__digit sjpta-404__digit--right">4</span>
		</div>

		<div class="sjpta-404__copy">
			<?php if ( '' !== $sjpta_eyebrow ) : ?>
				<span class="sjpta-eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
			<?php endif; ?>

			<h1 class="sjpta-404__heading"><?php echo esc_html( $sjpta_heading ); ?></h1>

			<?php if ( '' !== $sjpta_text ) : ?>
				<p class="sjpta-404__text"><?php echo esc_html( $sjpta_text ); ?></p>
			<?php endif; ?>

			<div class="sjpta-404__actions">
				<a class="sjpta-btn sjpta-btn--primary sjpta-404__btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Back to the homepage', 'sjptheatrearts' ); ?>
				</a>
				<a class="sjpta-btn sjpta-btn--outline sjpta-404__btn" href="<?php echo esc_url( $sjpta_classes_url ); ?>">
					<?php esc_html_e( 'See all classes', 'sjptheatrearts' ); ?>
					<?php echo sjpta_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
				</a>
			</div>

			<form class="sjpta-404__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="<?php echo esc_attr( $sjpta_search_id ); ?>">
					<?php esc_html_e( 'Search the site', 'sjptheatrearts' ); ?>
				</label>
				<input
					class="sjpta-404__input"
					id="<?php echo esc_attr( $sjpta_search_id ); ?>"
					type="search"
					name="s"
					placeholder="<?php esc_attr_e( 'Try searching, for example ballet or fees', 'sjptheatrearts' ); ?>"
					autocomplete="off"
				>
				<button class="sjpta-btn sjpta-btn--plum sjpta-404__submit" type="submit">
					<?php esc_html_e( 'Search', 'sjptheatrearts' ); ?>
				</button>
			</form>

			<?php if ( '' !== $sjpta_aside ) : ?>
				<p class="sjpta-404__aside"><?php echo esc_html( $sjpta_aside ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $sjpta_routes ) ) : ?>
			<nav class="sjpta-404__routes" aria-label="<?php esc_attr_e( 'Popular places to go next', 'sjptheatrearts' ); ?>">
				<span class="sjpta-404__routes-label"><?php esc_html_e( 'Or head straight to', 'sjptheatrearts' ); ?></span>
				<ul class="sjpta-404__pills">
					<?php foreach ( $sjpta_routes as $sjpta_route ) : ?>
						<li>
							<a class="sjpta-404__pill sjpta-404__pill--<?php echo esc_attr( $sjpta_route['tone'] ); ?>" href="<?php echo esc_url( $sjpta_route['url'] ); ?>">
								<span class="sjpta-404__pill-icon">
									<?php echo sjpta_icon( $sjpta_route['icon'], 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								</span>
								<?php echo esc_html( $sjpta_route['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
</section>
