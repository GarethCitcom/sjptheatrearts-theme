<?php
/**
 * Bio feature.
 *
 * A photo collage beside one person's story. The collage is flipped relative to
 * the hero's, which is why sjpta_photo_collage() takes a $flip argument rather
 * than existing twice.
 *
 * Two arrangements. Born To Be overlaps the two photographs; About stacks them
 * inside a bordered panel beside a bordered card, and closes with a byline
 * rather than a row of pills. The same fields feed both, so a bio moved between
 * the two pages keeps everything it had.
 *
 * Names and histories are facts about real people: every word here comes from a
 * field, and an empty field renders nothing rather than a plausible guess.
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

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_body = $sjpta_has_acf ? get_field( 'body', $sjpta_ctx ) : '';
$sjpta_body = is_string( $sjpta_body ) ? $sjpta_body : '';

$sjpta_main  = $sjpta_has_acf ? (int) get_field( 'photo_main', $sjpta_ctx ) : 0;
$sjpta_inset = $sjpta_has_acf ? (int) get_field( 'photo_inset', $sjpta_ctx ) : 0;

$sjpta_pills = $sjpta_has_acf ? get_field( 'pills', $sjpta_ctx ) : array();
$sjpta_pills = is_array( $sjpta_pills ) ? $sjpta_pills : array();

$sjpta_layout  = sjpta_field( 'layout', 'collage', $sjpta_ctx );
$sjpta_stacked = ( 'stacked' === $sjpta_layout );

$sjpta_caption = sjpta_field( 'photo_caption', '', $sjpta_ctx );
$sjpta_by_name = sjpta_field( 'byline_name', '', $sjpta_ctx );
$sjpta_by_role = sjpta_field( 'byline_role', '', $sjpta_ctx );
$sjpta_by_init = sjpta_field( 'byline_initials', '', $sjpta_ctx );
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-bio alignfull<?php echo $sjpta_stacked ? ' sjpta-bio--stacked' : ''; ?>">
	<div class="sjpta-inner sjpta-bio__inner" data-reveal>
		<div class="sjpta-bio__media">
			<?php if ( $sjpta_stacked ) : ?>
				<?php
				/*
				 * Two photographs stacked inside one bordered panel: the portrait
				 * given the room, a wide strip beneath it. The caption names who
				 * is in the portrait, which matters when the page is about them.
				 */
				?>
				<?php if ( $sjpta_main ) : ?>
					<div class="sjpta-bio__portrait">
						<?php
						echo wp_get_attachment_image(
							$sjpta_main,
							'large',
							false,
							array(
								'loading' => 'lazy',
								'sizes'   => '(max-width: 1023px) 100vw, 500px',
							)
						);
						?>
						<?php if ( '' !== $sjpta_caption ) : ?>
							<span class="sjpta-bio__caption"><?php echo esc_html( $sjpta_caption ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $sjpta_inset ) : ?>
					<div class="sjpta-bio__strip">
						<?php
						echo wp_get_attachment_image(
							$sjpta_inset,
							'large',
							false,
							array(
								'loading' => 'lazy',
								'sizes'   => '(max-width: 1023px) 100vw, 500px',
							)
						);
						?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php sjpta_photo_collage( $sjpta_main, $sjpta_inset, true ); ?>
			<?php endif; ?>
		</div>

		<div class="sjpta-bio__copy">
			<?php if ( '' !== $sjpta_eyebrow ) : ?>
				<span class="sjpta-eyebrow sjpta-bio__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="sjpta-bio__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>

			<?php if ( '' !== $sjpta_body ) : ?>
				<div class="sjpta-bio__body"><?php echo wp_kses_post( $sjpta_body ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $sjpta_pills ) ) : ?>
				<ul class="sjpta-bio__pills">
					<?php foreach ( $sjpta_pills as $sjpta_pill ) : ?>
						<?php
						$sjpta_label = isset( $sjpta_pill['text'] ) ? (string) $sjpta_pill['text'] : '';

						if ( '' === $sjpta_label ) {
							continue;
						}
						?>
						<li><?php echo esc_html( $sjpta_label ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_by_name ) : ?>
				<div class="sjpta-bio__byline">
					<?php if ( '' !== $sjpta_by_init ) : ?>
						<span class="sjpta-bio__initials" aria-hidden="true"><?php echo esc_html( $sjpta_by_init ); ?></span>
					<?php endif; ?>
					<span class="sjpta-bio__bytext">
						<span class="sjpta-bio__byname"><?php echo esc_html( $sjpta_by_name ); ?></span>
						<?php if ( '' !== $sjpta_by_role ) : ?>
							<span class="sjpta-bio__byrole"><?php echo esc_html( $sjpta_by_role ); ?></span>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
