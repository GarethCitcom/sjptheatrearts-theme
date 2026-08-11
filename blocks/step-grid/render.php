<?php
/**
 * Step grid.
 *
 * A numbered grid of what happens in a session, inside a white panel. One card
 * can be inverted, which is how the design marks the step everything else builds
 * towards.
 *
 * An ordered list, not a stack of divs: the numbers carry meaning, so the order
 * has to survive being read aloud. The visible numerals are decorative on top of
 * that, hence aria-hidden.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_eyebrow   = sjpta_field( 'eyebrow', '', $sjpta_ctx );
$sjpta_heading   = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_note      = sjpta_field( 'note', '', $sjpta_ctx );
$sjpta_foot_head = sjpta_field( 'footer_heading', '', $sjpta_ctx );
$sjpta_foot_text = sjpta_field( 'footer_text', '', $sjpta_ctx );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_steps = $sjpta_has_acf ? get_field( 'steps', $sjpta_ctx ) : array();
$sjpta_steps = is_array( $sjpta_steps ) ? $sjpta_steps : array();

$sjpta_pills = $sjpta_has_acf ? get_field( 'pills', $sjpta_ctx ) : array();
$sjpta_pills = is_array( $sjpta_pills ) ? $sjpta_pills : array();
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-steps alignfull">
	<div class="sjpta-inner sjpta-steps__inner">
		<div class="sjpta-steps__panel" data-reveal>
			<div class="sjpta-steps__head">
				<div>
					<?php if ( '' !== $sjpta_eyebrow ) : ?>
						<span class="sjpta-eyebrow sjpta-steps__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
					<?php endif; ?>

					<h2 class="sjpta-steps__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>
				</div>

				<?php if ( '' !== $sjpta_note ) : ?>
					<span class="sjpta-steps__note"><?php echo esc_html( $sjpta_note ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $sjpta_steps ) ) : ?>
				<?php
				/*
				 * Columns follow the number of steps. Born To Be has six and wants
				 * three across; the About page's four examination routes want four,
				 * and in a three-column grid the fourth sat alone on a second row
				 * looking like an afterthought.
				 */
				$sjpta_columns = ( 0 === count( $sjpta_steps ) % 4 ) ? 4 : 3;
				?>
				<ol class="sjpta-steps__grid sjpta-steps__grid--<?php echo esc_attr( (string) $sjpta_columns ); ?>" data-stagger>
					<?php foreach ( $sjpta_steps as $sjpta_i => $sjpta_step ) : ?>
						<?php
						$sjpta_title = isset( $sjpta_step['title'] ) ? (string) $sjpta_step['title'] : '';

						if ( '' === $sjpta_title ) {
							continue;
						}

						$sjpta_text   = isset( $sjpta_step['text'] ) ? (string) $sjpta_step['text'] : '';
						$sjpta_invert = ! empty( $sjpta_step['invert'] );
						$sjpta_tone   = (string) ( $sjpta_step['tone'] ?? '' );

						/*
						 * Not $sjpta_note: that is the section's own note, set well
						 * above and already rendered. Reusing the name worked only
						 * by luck of ordering.
						 */
						$sjpta_line = trim( (string) ( $sjpta_step['note'] ?? '' ) );
						?>
						<li class="sjpta-steps__step<?php echo $sjpta_invert ? ' is-inverted' : ''; ?><?php echo '' !== $sjpta_tone ? ' sjpta-tone--' . esc_attr( $sjpta_tone ) : ''; ?>" data-lift>
							<span class="sjpta-steps__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $sjpta_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<h3 class="sjpta-steps__steptitle"><?php echo esc_html( $sjpta_title ); ?></h3>

							<?php if ( '' !== $sjpta_text ) : ?>
								<p class="sjpta-steps__steptext"><?php echo esc_html( $sjpta_text ); ?></p>
							<?php endif; ?>

							<?php if ( '' !== $sjpta_line ) : ?>
								<p class="sjpta-steps__stepnote"><?php echo esc_html( $sjpta_line ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_foot_head || ! empty( $sjpta_pills ) ) : ?>
				<div class="sjpta-steps__foot">
					<div class="sjpta-steps__footcopy">
						<?php if ( '' !== $sjpta_foot_head ) : ?>
							<h3 class="sjpta-steps__foothead"><?php echo esc_html( $sjpta_foot_head ); ?></h3>
						<?php endif; ?>

						<?php if ( '' !== $sjpta_foot_text ) : ?>
							<p class="sjpta-steps__foottext"><?php echo esc_html( $sjpta_foot_text ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $sjpta_pills ) ) : ?>
						<ul class="sjpta-steps__pills">
							<?php foreach ( $sjpta_pills as $sjpta_pill ) : ?>
								<?php
								$sjpta_label = isset( $sjpta_pill['text'] ) ? (string) $sjpta_pill['text'] : '';

								if ( '' === $sjpta_label ) {
									continue;
								}

								$sjpta_tone = isset( $sjpta_pill['tone'] ) ? (string) $sjpta_pill['tone'] : 'red';
								?>
								<li class="sjpta-steps__pill sjpta-tone--<?php echo esc_attr( $sjpta_tone ); ?>"><?php echo esc_html( $sjpta_label ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
