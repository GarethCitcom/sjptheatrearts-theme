<?php
/**
 * FAQ.
 *
 * Native <details> elements, so the accordions open, close and are keyboard
 * operable with no JavaScript at all. The chevron rotates from CSS on
 * `details[open]` for the same reason; motion.js is not involved.
 *
 * Built generic: the Join design carries six of these and Timetable & fees five.
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
$sjpta_intro   = sjpta_field( 'intro', '', $sjpta_ctx );
$sjpta_label   = sjpta_field( 'link_label', '', $sjpta_ctx );
$sjpta_url     = sjpta_field( 'link_url', '', $sjpta_ctx );
$sjpta_layout  = sjpta_field( 'layout', 'split', $sjpta_ctx );

/*
 * Two arrangements, because two designs ask for two. Join and Born To Be put
 * the heading in a column beside the questions; Timetable & fees puts it in a
 * section bar over a narrow single column, which suits a list of five short
 * questions with no intro to carry the left-hand column.
 */
$sjpta_bar = ( 'bar' === $sjpta_layout );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_items = $sjpta_has_acf ? get_field( 'items', $sjpta_ctx ) : array();
$sjpta_items = is_array( $sjpta_items ) ? $sjpta_items : array();
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-faq alignfull<?php echo $sjpta_bar ? ' sjpta-faq--bar' : ''; ?>">
	<div class="sjpta-inner sjpta-faq__inner">
		<?php if ( $sjpta_bar ) : ?>
			<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_heading ); ?></h2>
			<?php sjpta_section_bar( $sjpta_heading, $sjpta_intro, $sjpta_label, $sjpta_url ); ?>
		<?php else : ?>
		<div class="sjpta-faq__copy">
			<?php if ( '' !== $sjpta_eyebrow ) : ?>
				<span class="sjpta-eyebrow sjpta-faq__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="sjpta-faq__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>

			<?php if ( '' !== $sjpta_intro ) : ?>
				<p class="sjpta-faq__intro"><?php echo esc_html( $sjpta_intro ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_label && '' !== $sjpta_url ) : ?>
				<a class="sjpta-faq__link" href="<?php echo esc_url( $sjpta_url ); ?>">
					<?php echo esc_html( $sjpta_label ); ?>
					<?php echo sjpta_icon( 'chevron-right', 12, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
				</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $sjpta_items ) ) : ?>
			<div class="sjpta-faq__list">
				<?php foreach ( $sjpta_items as $sjpta_item ) : ?>
					<?php
					$sjpta_q = isset( $sjpta_item['question'] ) ? (string) $sjpta_item['question'] : '';
					$sjpta_a = isset( $sjpta_item['answer'] ) ? (string) $sjpta_item['answer'] : '';

					if ( '' === $sjpta_q ) {
						continue;
					}
					?>
					<details class="sjpta-faq__item"<?php echo empty( $sjpta_item['open'] ) ? '' : ' open'; ?>>
						<summary class="sjpta-faq__q">
							<?php echo esc_html( $sjpta_q ); ?>
							<span class="sjpta-faq__chevron" data-chevron aria-hidden="true">
								<?php echo sjpta_icon( 'chevron-down', 14, 'var(--sjpta-accent-text)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
							</span>
						</summary>

						<?php if ( '' !== $sjpta_a ) : ?>
							<p class="sjpta-faq__a"><?php echo esc_html( $sjpta_a ); ?></p>
						<?php endif; ?>
					</details>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
