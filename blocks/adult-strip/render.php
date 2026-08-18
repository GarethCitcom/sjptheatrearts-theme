<?php
/**
 * Reassurance strip.
 *
 * Media or icon, a short piece of copy and one button, in a rounded band. On the
 * homepage it is the one place that addresses the adult reader as a potential
 * student themselves; on Born To Be it carries the "you do not need to dance
 * with SJP" reassurance. Same skeleton, two tones.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_eyebrow = sjpta_field( 'eyebrow', '', $sjpta_ctx );
$sjpta_heading = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_text    = sjpta_field( 'text', '', $sjpta_ctx );
$sjpta_label   = sjpta_field( 'link_label', '', $sjpta_ctx );
$sjpta_url     = sjpta_link_or_page( sjpta_field( 'link_url', '', $sjpta_ctx ), 'classes' );
$sjpta_image   = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? (int) get_field( 'image', $sjpta_ctx ) : 0;

$sjpta_tone   = sjpta_field( 'tone', 'pastel', $sjpta_ctx );
$sjpta_icon   = sjpta_field( 'icon', '', $sjpta_ctx );
$sjpta_btn    = sjpta_field( 'button_tone', 'plum', $sjpta_ctx );
$sjpta_dark   = 'purple' === $sjpta_tone;
$sjpta_btncls = 'white' === $sjpta_btn ? 'sjpta-btn--white' : 'sjpta-btn--plum';

// The arrow is only ever drawn on the plum button; the white one is text alone.
$sjpta_arrow = 'white' === $sjpta_btn ? '' : sjpta_icon( 'arrow-right', 15, '#FFFFFF' );

if ( '' === $sjpta_heading ) {
	return;
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-adults alignfull<?php echo $sjpta_dark ? ' sjpta-adults--purple' : ''; ?>">
	<div class="sjpta-inner sjpta-adults__inner">
		<div class="sjpta-adults__panel">
			<?php if ( '' !== $sjpta_icon ) : ?>
				<span class="sjpta-adults__icon">
					<?php
					echo sjpta_icon( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon().
						$sjpta_icon,
						24,
						$sjpta_dark ? '#FFFFFF' : '#381064',
						'var(--sjpta-accent)'
					);
					?>
				</span>
			<?php elseif ( $sjpta_image ) : ?>
				<span class="sjpta-adults__media">
					<?php
					echo wp_get_attachment_image(
						$sjpta_image,
						'medium',
						false,
						array(
							'loading' => 'lazy',
							'sizes'   => '120px',
						)
					);
					?>
				</span>
			<?php endif; ?>

			<div class="sjpta-adults__copy">
				<?php if ( '' !== $sjpta_eyebrow ) : ?>
					<span class="sjpta-badge sjpta-badge--magenta sjpta-adults__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="sjpta-adults__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>

				<?php if ( '' !== $sjpta_text ) : ?>
					<p class="sjpta-adults__text"><?php echo esc_html( $sjpta_text ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $sjpta_label ) : ?>
				<a class="sjpta-btn <?php echo esc_attr( $sjpta_btncls ); ?> sjpta-adults__cta" href="<?php echo esc_url( $sjpta_url ); ?>">
					<?php echo esc_html( $sjpta_label ); ?>
					<?php echo $sjpta_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
