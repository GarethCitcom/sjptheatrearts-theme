<?php
/**
 * Dark feature.
 *
 * A dark band: copy and a tick list on one side, a mosaic of photographs on the
 * other. The first photograph spans both columns, so the mosaic reads as one
 * picture wall rather than three unrelated tiles.
 *
 * Close in shape to the homepage teens band, but that section's second column is
 * a list of links and its first is a single photo behind a scrim. Two new modes
 * there would have cost more than this block and destabilised a signed-off
 * section.
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
$sjpta_label   = sjpta_field( 'cta_label', '', $sjpta_ctx );
$sjpta_url     = sjpta_link_or_page( sjpta_field( 'cta_url', '', $sjpta_ctx ), 'performances' );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_points = $sjpta_has_acf ? get_field( 'points', $sjpta_ctx ) : array();
$sjpta_points = is_array( $sjpta_points ) ? $sjpta_points : array();

$sjpta_photos = $sjpta_has_acf ? get_field( 'photos', $sjpta_ctx ) : array();
$sjpta_photos = is_array( $sjpta_photos ) ? $sjpta_photos : array();
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-darkfeature alignfull">
	<div class="sjpta-inner sjpta-darkfeature__inner">
		<div class="sjpta-darkfeature__panel" data-reveal>
			<span class="sjpta-darkfeature__decor" aria-hidden="true"></span>

			<div class="sjpta-darkfeature__grid">
				<div class="sjpta-darkfeature__copy">
					<?php if ( '' !== $sjpta_eyebrow ) : ?>
						<span class="sjpta-eyebrow sjpta-darkfeature__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
					<?php endif; ?>

					<h2 class="sjpta-darkfeature__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>

					<?php if ( '' !== $sjpta_text ) : ?>
						<p class="sjpta-darkfeature__text"><?php echo esc_html( $sjpta_text ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $sjpta_points ) ) : ?>
						<ul class="sjpta-darkfeature__list">
							<?php foreach ( $sjpta_points as $sjpta_point ) : ?>
								<?php
								$sjpta_line = isset( $sjpta_point['text'] ) ? (string) $sjpta_point['text'] : '';

								if ( '' === $sjpta_line ) {
									continue;
								}
								?>
								<li>
									<span class="sjpta-darkfeature__tick" aria-hidden="true">
										<?php echo sjpta_icon( 'check', 12, 'var(--sjpta-accent-light)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
									</span>
									<?php echo esc_html( $sjpta_line ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( '' !== $sjpta_label ) : ?>
						<a class="sjpta-btn sjpta-btn--primary sjpta-darkfeature__cta" href="<?php echo esc_url( $sjpta_url ); ?>">
							<?php echo esc_html( $sjpta_label ); ?>
							<?php echo sjpta_icon( 'arrow-right', 15, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $sjpta_photos ) ) : ?>
					<div class="sjpta-darkfeature__mosaic">
						<?php foreach ( $sjpta_photos as $sjpta_i => $sjpta_photo ) : ?>
							<?php
							$sjpta_id = isset( $sjpta_photo['photo'] ) ? (int) $sjpta_photo['photo'] : 0;

							if ( ! $sjpta_id ) {
								continue;
							}
							?>
							<span class="sjpta-darkfeature__tile<?php echo 0 === $sjpta_i ? ' is-wide' : ''; ?>">
								<?php
								echo wp_get_attachment_image(
									$sjpta_id,
									'sjpta-640',
									false,
									array(
										'loading' => 'lazy',
										'sizes'   => 0 === $sjpta_i
											? '(max-width: 1023px) 92vw, 640px'
											: '(max-width: 1023px) 45vw, 312px',
									)
								);
								?>
							</span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
