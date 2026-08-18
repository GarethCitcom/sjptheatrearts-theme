<?php
/**
 * Inclusion and safeguarding, side by side.
 *
 * Two panels of a similar weight, deliberately together: how a student who needs
 * something different is taught, and how every student is kept safe. Separating
 * them would let a page carry one without the other.
 *
 * The safeguarding claims here are the strongest promises on the site. Each is a
 * field, and the panel renders only what has been filled in, because "every
 * teacher is DBS-checked" is not a sentence to leave a template guessing at.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx     = sjpta_block_context( $block ?? null );
$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_left_eyebrow  = sjpta_field( 'left_eyebrow', '', $sjpta_ctx );
$sjpta_left_heading  = sjpta_field( 'left_heading', '', $sjpta_ctx );
$sjpta_right_eyebrow = sjpta_field( 'right_eyebrow', '', $sjpta_ctx );
$sjpta_right_heading = sjpta_field( 'right_heading', '', $sjpta_ctx );

$sjpta_left_body = $sjpta_has_acf ? get_field( 'left_body', $sjpta_ctx ) : '';
$sjpta_left_body = is_string( $sjpta_left_body ) ? $sjpta_left_body : '';

$sjpta_quote  = sjpta_field( 'quote', '', $sjpta_ctx );
$sjpta_source = sjpta_field( 'quote_source', '', $sjpta_ctx );
$sjpta_detail = sjpta_field( 'quote_detail', '', $sjpta_ctx );

$sjpta_points = $sjpta_has_acf ? get_field( 'points', $sjpta_ctx ) : array();
$sjpta_points = is_array( $sjpta_points ) ? $sjpta_points : array();

$sjpta_mark      = $sjpta_has_acf ? (int) get_field( 'mark', $sjpta_ctx ) : 0;
$sjpta_mark_text = sjpta_field( 'mark_text', '', $sjpta_ctx );

if ( '' === $sjpta_left_heading && '' === $sjpta_right_heading ) {
	return;
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-assure alignfull">
	<div class="sjpta-inner sjpta-assure__grid" data-reveal>

		<?php if ( '' !== $sjpta_left_heading ) : ?>
			<div class="sjpta-assure__panel">
				<?php if ( '' !== $sjpta_left_eyebrow ) : ?>
					<span class="sjpta-badge sjpta-badge--magenta sjpta-assure__badge"><?php echo esc_html( $sjpta_left_eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="sjpta-assure__heading"><?php echo esc_html( $sjpta_left_heading ); ?></h2>

				<?php if ( '' !== $sjpta_left_body ) : ?>
					<div class="sjpta-assure__body"><?php echo wp_kses_post( $sjpta_left_body ); ?></div>
				<?php endif; ?>

				<?php if ( '' !== $sjpta_quote ) : ?>
					<figure class="sjpta-assure__quote">
						<span class="sjpta-assure__mark" aria-hidden="true">&ldquo;</span>
						<blockquote><?php echo esc_html( $sjpta_quote ); ?></blockquote>
						<?php if ( '' !== $sjpta_source ) : ?>
							<figcaption>
								<strong><?php echo esc_html( $sjpta_source ); ?></strong><?php echo '' !== $sjpta_detail ? esc_html( ', ' . $sjpta_detail ) : ''; ?>
							</figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $sjpta_right_heading ) : ?>
			<div class="sjpta-assure__panel">
				<?php if ( '' !== $sjpta_right_eyebrow ) : ?>
					<span class="sjpta-badge sjpta-badge--purple sjpta-assure__badge"><?php echo esc_html( $sjpta_right_eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="sjpta-assure__heading"><?php echo esc_html( $sjpta_right_heading ); ?></h2>

				<?php if ( ! empty( $sjpta_points ) ) : ?>
					<ul class="sjpta-assure__points">
						<?php foreach ( $sjpta_points as $sjpta_point ) : ?>
							<?php
							$sjpta_strong = trim( (string) ( $sjpta_point['strong'] ?? '' ) );
							$sjpta_rest   = trim( (string) ( $sjpta_point['text'] ?? '' ) );

							if ( '' === $sjpta_strong && '' === $sjpta_rest ) {
								continue;
							}
							?>
							<li>
								<span class="sjpta-assure__tick" aria-hidden="true">
									<?php echo sjpta_icon( 'check', 13, 'var(--wp--custom--color--badge-green-fg)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								</span>
								<span>
									<?php if ( '' !== $sjpta_strong ) : ?>
										<strong><?php echo esc_html( $sjpta_strong ); ?></strong>
									<?php endif; ?>
									<?php echo esc_html( $sjpta_rest ); ?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $sjpta_mark || '' !== $sjpta_mark_text ) : ?>
					<div class="sjpta-assure__policy">
						<?php if ( $sjpta_mark ) : ?>
							<span class="sjpta-assure__policymark">
								<?php
								echo wp_get_attachment_image(
									$sjpta_mark,
									'medium',
									false,
									array(
										'loading' => 'lazy',
										'sizes'   => '54px',
									)
								);
								?>
							</span>
						<?php endif; ?>

						<?php if ( '' !== $sjpta_mark_text ) : ?>
							<p><?php echo esc_html( $sjpta_mark_text ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
