<?php
/**
 * A row of photo cards.
 *
 * Photograph, a badge over it, then a heading and a line of text. Used for the
 * things that happen through the year beyond the December show; built generic
 * because it is a shape any page with three things to show can use.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx     = sjpta_block_context( $block ?? null );
$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_label = sjpta_field( 'label', '', $sjpta_ctx );
$sjpta_note  = sjpta_field( 'note', '', $sjpta_ctx );

$sjpta_cards = $sjpta_has_acf ? get_field( 'cards', $sjpta_ctx ) : array();
$sjpta_cards = is_array( $sjpta_cards ) ? $sjpta_cards : array();

if ( empty( $sjpta_cards ) ) {
	return;
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-photocards alignfull">
	<div class="sjpta-inner">
		<?php if ( '' !== $sjpta_label ) : ?>
			<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_label ); ?></h2>
			<?php sjpta_section_bar( $sjpta_label, $sjpta_note, '', '' ); ?>
		<?php endif; ?>

		<div class="sjpta-photocards__grid" data-reveal data-stagger>
			<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
				<?php
				$sjpta_title = trim( (string) ( $sjpta_card['title'] ?? '' ) );

				if ( '' === $sjpta_title ) {
					continue;
				}

				$sjpta_text  = trim( (string) ( $sjpta_card['text'] ?? '' ) );
				$sjpta_badge = trim( (string) ( $sjpta_card['badge'] ?? '' ) );
				$sjpta_tone  = (string) ( $sjpta_card['tone'] ?? 'orange' );
				$sjpta_photo = (int) ( $sjpta_card['photo'] ?? 0 );
				?>
				<article class="sjpta-photocard">
					<div class="sjpta-photocard__media">
						<?php if ( $sjpta_photo ) : ?>
							<?php
							echo wp_get_attachment_image(
								$sjpta_photo,
								'sjpta-640',
								false,
								array(
									'loading' => 'lazy',
									'sizes'   => '(max-width: 1023px) 100vw, 400px',
								)
							);
							?>
						<?php endif; ?>

						<?php if ( '' !== $sjpta_badge ) : ?>
							<span class="sjpta-badge sjpta-badge--<?php echo esc_attr( $sjpta_tone ); ?> sjpta-photocard__badge">
								<?php echo esc_html( $sjpta_badge ); ?>
							</span>
						<?php endif; ?>
					</div>

					<div class="sjpta-photocard__body">
						<h3 class="sjpta-photocard__title"><?php echo esc_html( $sjpta_title ); ?></h3>
						<?php if ( '' !== $sjpta_text ) : ?>
							<p class="sjpta-photocard__text"><?php echo esc_html( $sjpta_text ); ?></p>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
