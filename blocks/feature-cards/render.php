<?php
/**
 * Feature cards.
 *
 * Prose in the left column, a grid of tinted cards in the right. The cards carry
 * an icon, a title and a line of copy, and take their colours from the shared
 * tone map, so no hex is written here.
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

$sjpta_body = $sjpta_has_acf ? get_field( 'body', $sjpta_ctx ) : '';
$sjpta_body = is_string( $sjpta_body ) ? $sjpta_body : '';

$sjpta_cards = $sjpta_has_acf ? get_field( 'cards', $sjpta_ctx ) : array();
$sjpta_cards = is_array( $sjpta_cards ) ? $sjpta_cards : array();

/*
 * Born To Be sets a column of prose beside the cards; About puts the heading in
 * a section bar with the cards in one full-width row beneath. Same cards, and
 * the same fields feed the bar, so nothing has to be re-typed to switch.
 */
$sjpta_layout = sjpta_field( 'layout', 'split', $sjpta_ctx );
$sjpta_bar    = ( 'bar' === $sjpta_layout );
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-features alignfull<?php echo $sjpta_bar ? ' sjpta-features--bar' : ''; ?>">
	<div class="sjpta-inner sjpta-features__inner">
		<?php if ( $sjpta_bar ) : ?>
			<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_heading ); ?></h2>
			<?php sjpta_section_bar( $sjpta_heading, $sjpta_note, '', '' ); ?>
		<?php else : ?>
		<div class="sjpta-features__copy" data-reveal>
			<?php if ( '' !== $sjpta_eyebrow ) : ?>
				<span class="sjpta-eyebrow sjpta-features__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="sjpta-features__heading">
				<?php echo wp_kses_post( sjpta_highlight_words( $sjpta_heading, $sjpta_highlight ) ); ?>
			</h2>

			<?php if ( '' !== $sjpta_body ) : ?>
				<div class="sjpta-features__body"><?php echo wp_kses_post( $sjpta_body ); ?></div>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_note ) : ?>
				<div class="sjpta-features__note">
					<p><?php echo esc_html( $sjpta_note ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $sjpta_cards ) ) : ?>
			<div class="sjpta-features__grid" data-reveal>
				<div class="sjpta-features__cards" data-stagger>
					<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
						<?php
						$sjpta_title = isset( $sjpta_card['title'] ) ? (string) $sjpta_card['title'] : '';

						if ( '' === $sjpta_title ) {
							continue;
						}

						$sjpta_tone  = isset( $sjpta_card['tone'] ) ? (string) $sjpta_card['tone'] : 'red';
						$sjpta_icon  = isset( $sjpta_card['icon'] ) ? (string) $sjpta_card['icon'] : '';
						$sjpta_text  = isset( $sjpta_card['text'] ) ? (string) $sjpta_card['text'] : '';
						$sjpta_badge = trim( (string) ( $sjpta_card['badge'] ?? '' ) );
						?>
						<div class="sjpta-features__card sjpta-tone--<?php echo esc_attr( $sjpta_tone ); ?>" data-lift>
							<?php if ( '' !== $sjpta_badge ) : ?>
								<span class="sjpta-features__badge"><?php echo esc_html( $sjpta_badge ); ?></span>
							<?php endif; ?>

							<?php if ( '' !== $sjpta_icon ) : ?>
								<span class="sjpta-features__icon">
									<?php echo sjpta_icon( $sjpta_icon, 22, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								</span>
							<?php endif; ?>

							<h3 class="sjpta-features__cardtitle"><?php echo esc_html( $sjpta_title ); ?></h3>

							<?php if ( '' !== $sjpta_text ) : ?>
								<p class="sjpta-features__cardtext"><?php echo esc_html( $sjpta_text ); ?></p>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
