<?php
/**
 * Closing call-to-action band.
 *
 * Appears just above the footer on every page. The heading and body differ per
 * page ("Not sure where to begin?" on the homepage, "Ready to join?" on
 * Classes, and so on), so they are block attributes the client edits in place
 * rather than anything hard-coded.
 *
 * @package SJPTheatreArts
 *
 * @var array $attributes Block attributes.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The band ships as a template part so every page closes on one without having
 * to remember it. A page that needs its own wording — the homepage's "Not sure
 * where to begin?", say — places the block in its own content instead, where it
 * can also sit in the right order relative to other sections.
 *
 * Page content renders before the template part, so the first band on the page
 * wins and the part quietly stands down. Without this the homepage would close
 * on two call-to-action panels.
 */
if ( ! empty( $GLOBALS['sjpta_cta_band_rendered'] ) ) {
	return;
}

$GLOBALS['sjpta_cta_band_rendered'] = true;

$sjpta_heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$sjpta_text    = isset( $attributes['text'] ) ? (string) $attributes['text'] : '';
$sjpta_roomy   = ! empty( $attributes['roomy'] );

$sjpta_primary_label   = isset( $attributes['primaryLabel'] ) ? (string) $attributes['primaryLabel'] : '';
$sjpta_secondary_label = isset( $attributes['secondaryLabel'] ) ? (string) $attributes['secondaryLabel'] : '';

// Empty URLs resolve to the pages the design points at, so the band is never
// a dead end even before the client sets anything.
$sjpta_join      = get_page_by_path( 'join' );
$sjpta_timetable = get_page_by_path( 'timetable-and-fees' );

$sjpta_primary_url = isset( $attributes['primaryUrl'] ) ? trim( (string) $attributes['primaryUrl'] ) : '';
if ( '' === $sjpta_primary_url ) {
	$sjpta_primary_url = $sjpta_join ? (string) get_permalink( $sjpta_join ) : home_url( '/join/' );
}

$sjpta_secondary_url = isset( $attributes['secondaryUrl'] ) ? trim( (string) $attributes['secondaryUrl'] ) : '';
if ( '' === $sjpta_secondary_url ) {
	$sjpta_secondary_url = $sjpta_timetable ? (string) get_permalink( $sjpta_timetable ) : home_url( '/timetable-and-fees/' );
}

if ( '' === $sjpta_heading && '' === $sjpta_text ) {
	return;
}

$sjpta_panel_class = 'sjpta-cta__panel' . ( $sjpta_roomy ? ' sjpta-cta__panel--roomy' : '' );
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'sjpta-cta alignfull' ) ) ); ?>>
	<div class="sjpta-cta__inner">
		<div class="<?php echo esc_attr( $sjpta_panel_class ); ?>">
			<span class="sjpta-cta__blob sjpta-cta__blob--orange" aria-hidden="true"></span>
			<span class="sjpta-cta__blob sjpta-cta__blob--magenta" aria-hidden="true"></span>

			<div class="sjpta-cta__content">
				<?php if ( '' !== $sjpta_heading ) : ?>
					<h2 class="sjpta-cta__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $sjpta_text ) : ?>
					<p class="sjpta-cta__text"><?php echo esc_html( $sjpta_text ); ?></p>
				<?php endif; ?>

				<div class="sjpta-cta__actions">
					<?php if ( '' !== $sjpta_primary_label ) : ?>
						<a class="sjpta-btn sjpta-btn--primary sjpta-cta__btn" href="<?php echo esc_url( $sjpta_primary_url ); ?>">
							<?php echo esc_html( $sjpta_primary_label ); ?>
						</a>
					<?php endif; ?>

					<?php if ( '' !== $sjpta_secondary_label ) : ?>
						<a class="sjpta-btn sjpta-btn--ghost sjpta-cta__btn" href="<?php echo esc_url( $sjpta_secondary_url ); ?>">
							<?php echo esc_html( $sjpta_secondary_label ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
