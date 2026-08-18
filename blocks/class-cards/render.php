<?php
/**
 * Class cards.
 *
 * Looks like the homepage age cards, but the card is not itself a link: it
 * contains one. Nesting a button inside a card-wide anchor is invalid HTML and
 * gives a keyboard user two tab stops that do the same thing, so the whole-card
 * link is deliberately not repeated here.
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
$sjpta_highlight = sjpta_field( 'heading_highlight', '', $sjpta_ctx );
$sjpta_note      = sjpta_field( 'note', '', $sjpta_ctx );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_cards = $sjpta_has_acf ? get_field( 'cards', $sjpta_ctx ) : array();
$sjpta_cards = is_array( $sjpta_cards ) ? $sjpta_cards : array();
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-classcards alignfull">
	<div class="sjpta-inner sjpta-classcards__inner">
		<div class="sjpta-classcards__head">
			<div>
				<?php if ( '' !== $sjpta_eyebrow ) : ?>
					<span class="sjpta-eyebrow sjpta-classcards__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="sjpta-classcards__heading">
					<?php echo wp_kses_post( sjpta_highlight_words( $sjpta_heading, $sjpta_highlight ) ); ?>
				</h2>
			</div>

			<?php if ( '' !== $sjpta_note ) : ?>
				<p class="sjpta-classcards__note"><?php echo esc_html( $sjpta_note ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $sjpta_cards ) ) : ?>
			<div class="sjpta-classcards__grid" data-reveal>
				<div class="sjpta-classcards__cards" data-stagger>
					<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
						<?php
						$sjpta_title = isset( $sjpta_card['title'] ) ? (string) $sjpta_card['title'] : '';

						if ( '' === $sjpta_title ) {
							continue;
						}

						$sjpta_image = isset( $sjpta_card['image'] ) ? (int) $sjpta_card['image'] : 0;
						$sjpta_badge = isset( $sjpta_card['badge'] ) ? (string) $sjpta_card['badge'] : '';
						$sjpta_btone = isset( $sjpta_card['badge_tone'] ) ? (string) $sjpta_card['badge_tone'] : 'accent';
						$sjpta_text  = isset( $sjpta_card['text'] ) ? (string) $sjpta_card['text'] : '';
						$sjpta_meta  = ( isset( $sjpta_card['meta'] ) && is_array( $sjpta_card['meta'] ) ) ? $sjpta_card['meta'] : array();
						$sjpta_label = isset( $sjpta_card['cta_label'] ) ? (string) $sjpta_card['cta_label'] : '';
						$sjpta_url   = isset( $sjpta_card['cta_url'] ) ? (string) $sjpta_card['cta_url'] : '';
						$sjpta_ctone = ( isset( $sjpta_card['cta_tone'] ) && 'plum' === $sjpta_card['cta_tone'] ) ? 'sjpta-btn--plum' : 'sjpta-btn--primary';
						?>
						<article class="sjpta-classcard">
							<?php if ( $sjpta_image ) : ?>
								<div class="sjpta-classcard__media">
									<?php
									echo wp_get_attachment_image(
										$sjpta_image,
										'large',
										false,
										array(
											'loading' => 'lazy',
											'sizes'   => '(max-width: 782px) 100vw, 630px',
										)
									);
									?>

									<?php if ( '' !== $sjpta_badge ) : ?>
										<span class="sjpta-classcard__badge sjpta-classcard__badge--<?php echo esc_attr( $sjpta_btone ); ?>"><?php echo esc_html( $sjpta_badge ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<div class="sjpta-classcard__body">
								<h3 class="sjpta-classcard__title"><?php echo esc_html( $sjpta_title ); ?></h3>

								<?php if ( '' !== $sjpta_text ) : ?>
									<p class="sjpta-classcard__text"><?php echo esc_html( $sjpta_text ); ?></p>
								<?php endif; ?>

								<?php if ( ! empty( $sjpta_meta ) ) : ?>
									<ul class="sjpta-classcard__meta">
										<?php foreach ( $sjpta_meta as $sjpta_item ) : ?>
											<?php
											$sjpta_pill = isset( $sjpta_item['text'] ) ? (string) $sjpta_item['text'] : '';

											if ( '' === $sjpta_pill ) {
												continue;
											}
											?>
											<li><?php echo esc_html( $sjpta_pill ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>

								<?php if ( '' !== $sjpta_label ) : ?>
									<a class="sjpta-btn <?php echo esc_attr( $sjpta_ctone ); ?> sjpta-classcard__cta" href="<?php echo esc_url( '' !== $sjpta_url ? $sjpta_url : '#enquire' ); ?>">
										<?php echo esc_html( $sjpta_label ); ?>
									</a>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
