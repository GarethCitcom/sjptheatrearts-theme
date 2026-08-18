<?php
/**
 * Link cards.
 *
 * Wide cards that are each a single link: icon tile, title, and a line saying
 * what is on the other end. The whole card is the anchor, which is safe here
 * because it contains nothing else clickable.
 *
 * Links open in place rather than in a new tab. A new tab has to be announced to
 * be accessible, and taking the back button away from a reader who did not ask
 * for that is not a decision worth making on their behalf.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_cards = $sjpta_has_acf ? get_field( 'cards', $sjpta_ctx ) : array();
$sjpta_cards = is_array( $sjpta_cards ) ? $sjpta_cards : array();

if ( empty( $sjpta_cards ) ) {
	return;
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-linkcards alignfull">
	<div class="sjpta-inner sjpta-linkcards__inner">
		<?php
		/*
		 * Columns follow the number of cards. Born To Be has two and Contact has
		 * three; a fixed two-column grid left Contact's third card alone on a
		 * second row looking like an afterthought.
		 */
		$sjpta_columns = min( 3, max( 1, count( $sjpta_cards ) ) );
		?>
		<div class="sjpta-linkcards__grid sjpta-linkcards__grid--<?php echo esc_attr( (string) $sjpta_columns ); ?>" data-reveal>
			<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
				<?php
				$sjpta_title = isset( $sjpta_card['title'] ) ? (string) $sjpta_card['title'] : '';
				$sjpta_url   = isset( $sjpta_card['url'] ) ? (string) $sjpta_card['url'] : '';

				if ( '' === $sjpta_title ) {
					continue;
				}

				$sjpta_text = isset( $sjpta_card['text'] ) ? (string) $sjpta_card['text'] : '';
				$sjpta_icon = isset( $sjpta_card['icon'] ) ? (string) $sjpta_card['icon'] : '';
				$sjpta_tone = isset( $sjpta_card['tone'] ) ? (string) $sjpta_card['tone'] : 'accent';
				$sjpta_link = isset( $sjpta_card['link_label'] ) ? (string) $sjpta_card['link_label'] : '';

				/*
				 * A card without a link is information, not a dead link. Contact
				 * uses three of each: "Email us" goes somewhere, "When we reply"
				 * only tells you something. Rendering the second as an anchor with
				 * nowhere to go would put it in the tab order for nothing.
				 */
				$sjpta_tag  = '' !== $sjpta_url ? 'a' : 'div';
				$sjpta_href = '' !== $sjpta_url ? ' href="' . esc_url( $sjpta_url ) . '"' . sjpta_external_attr( $sjpta_url ) : '';
				?>
				<<?php echo esc_attr( $sjpta_tag ); ?> class="sjpta-linkcard<?php echo '' === $sjpta_url ? ' is-static' : ''; ?>"<?php echo $sjpta_href; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?> data-lift>
					<?php if ( '' !== $sjpta_icon ) : ?>
						<span class="sjpta-linkcard__tile sjpta-linkcard__tile--<?php echo esc_attr( $sjpta_tone ); ?>">
							<?php echo sjpta_icon( $sjpta_icon, 26, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</span>
					<?php endif; ?>

					<span class="sjpta-linkcard__copy">
						<span class="sjpta-linkcard__title"><?php echo esc_html( $sjpta_title ); ?></span>

						<?php if ( '' !== $sjpta_text ) : ?>
							<span class="sjpta-linkcard__text"><?php echo esc_html( $sjpta_text ); ?></span>
						<?php endif; ?>

						<?php if ( '' !== $sjpta_link ) : ?>
							<span class="sjpta-linkcard__label"><?php echo esc_html( $sjpta_link ); ?></span>
						<?php endif; ?>

						<?php echo sjpta_new_tab_note( $sjpta_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
					</span>

					<?php if ( '' !== $sjpta_url ) : ?>
						<span class="sjpta-linkcard__chevron" aria-hidden="true">
							<?php echo sjpta_icon( 'chevron-right', 16, 'var(--sjpta-accent-text)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</span>
					<?php endif; ?>
				</<?php echo esc_attr( $sjpta_tag ); ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
