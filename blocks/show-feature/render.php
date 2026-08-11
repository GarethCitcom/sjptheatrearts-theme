<?php
/**
 * The next thing on stage.
 *
 * A dark panel naming one event, with the three facts a parent asks about it
 * first: when, where, and who is in it. Dates and venue are exactly the sort of
 * thing that gets confirmed late, so each fact renders its own "to confirm"
 * state rather than the whole panel waiting on the slowest of them.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx     = sjpta_block_context( $block ?? null );
$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_eyebrow = sjpta_field( 'eyebrow', '', $sjpta_ctx );
$sjpta_heading = sjpta_field( 'heading', '', $sjpta_ctx );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_body = $sjpta_has_acf ? get_field( 'body', $sjpta_ctx ) : '';
$sjpta_body = is_string( $sjpta_body ) ? $sjpta_body : '';

$sjpta_facts = $sjpta_has_acf ? get_field( 'facts', $sjpta_ctx ) : array();
$sjpta_facts = is_array( $sjpta_facts ) ? $sjpta_facts : array();

$sjpta_photo = $sjpta_has_acf ? (int) get_field( 'photo', $sjpta_ctx ) : 0;

$sjpta_cta_label = sjpta_field( 'cta_label', '', $sjpta_ctx );
$sjpta_cta_url   = sjpta_field( 'cta_url', '', $sjpta_ctx );
$sjpta_alt_label = sjpta_field( 'alt_label', '', $sjpta_ctx );
$sjpta_alt_url   = sjpta_field( 'alt_url', '', $sjpta_ctx );
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-show alignfull">
	<div class="sjpta-inner">
		<div class="sjpta-show__panel" data-reveal>
			<div class="sjpta-show__copy">
				<?php if ( '' !== $sjpta_eyebrow ) : ?>
					<span class="sjpta-show__eyebrow">
						<span class="sjpta-show__dot" aria-hidden="true"></span>
						<?php echo esc_html( $sjpta_eyebrow ); ?>
					</span>
				<?php endif; ?>

				<h2 class="sjpta-show__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>

				<?php if ( '' !== $sjpta_body ) : ?>
					<p class="sjpta-show__body"><?php echo esc_html( $sjpta_body ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $sjpta_facts ) ) : ?>
					<dl class="sjpta-show__facts">
						<?php foreach ( $sjpta_facts as $sjpta_fact ) : ?>
							<?php
							$sjpta_label = trim( (string) ( $sjpta_fact['label'] ?? '' ) );

							if ( '' === $sjpta_label ) {
								continue;
							}

							$sjpta_value = trim( (string) ( $sjpta_fact['value'] ?? '' ) );
							?>
							<div class="sjpta-show__fact">
								<dt><?php echo esc_html( $sjpta_label ); ?></dt>
								<dd class="<?php echo '' === $sjpta_value ? 'is-toconfirm' : ''; ?>">
									<?php echo esc_html( '' === $sjpta_value ? __( 'To confirm', 'sjptheatrearts' ) : $sjpta_value ); ?>
								</dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>

				<?php if ( '' !== $sjpta_cta_label || '' !== $sjpta_alt_label ) : ?>
					<div class="sjpta-show__actions">
						<?php if ( '' !== $sjpta_cta_label ) : ?>
							<a class="sjpta-btn sjpta-btn--primary" href="<?php echo esc_url( $sjpta_cta_url ); ?>">
								<?php echo esc_html( $sjpta_cta_label ); ?>
							</a>
						<?php endif; ?>

						<?php if ( '' !== $sjpta_alt_label ) : ?>
							<a class="sjpta-btn sjpta-btn--ghost" href="<?php echo esc_url( $sjpta_alt_url ); ?>">
								<?php echo esc_html( $sjpta_alt_label ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $sjpta_photo ) : ?>
				<div class="sjpta-show__media">
					<?php
					echo wp_get_attachment_image(
						$sjpta_photo,
						'large',
						false,
						array(
							'loading' => 'lazy',
							'sizes'   => '(max-width: 1023px) 100vw, 560px',
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
