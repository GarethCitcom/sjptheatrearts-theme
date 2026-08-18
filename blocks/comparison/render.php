<?php
/**
 * Comparison.
 *
 * Two cards answering "which one is which?". Each carries a brand mark, a
 * one-line summary, a labelled list of facts and a link. One card can be
 * emphasised with a ring, which is how the design points at the page's own
 * subject rather than the other brand.
 *
 * The labelled list is a real <dl>: these are terms and their values, and a
 * screen reader reading "Who for, anyone who wants to perform" is the whole
 * point of the section.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_eyebrow  = sjpta_field( 'eyebrow', '', $sjpta_ctx );
$sjpta_heading  = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_intro    = sjpta_field( 'intro', '', $sjpta_ctx );
$sjpta_footnote = sjpta_field( 'footnote', '', $sjpta_ctx );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_cards = $sjpta_has_acf ? get_field( 'cards', $sjpta_ctx ) : array();
$sjpta_cards = is_array( $sjpta_cards ) ? $sjpta_cards : array();
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-compare alignfull">
	<div class="sjpta-inner sjpta-compare__inner">
		<div class="sjpta-compare__head">
			<?php if ( '' !== $sjpta_eyebrow ) : ?>
				<span class="sjpta-eyebrow sjpta-compare__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="sjpta-compare__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>

			<?php if ( '' !== $sjpta_intro ) : ?>
				<p class="sjpta-compare__intro"><?php echo esc_html( $sjpta_intro ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $sjpta_cards ) ) : ?>
			<div class="sjpta-compare__grid" data-reveal>
				<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
					<?php
					$sjpta_title = isset( $sjpta_card['title'] ) ? (string) $sjpta_card['title'] : '';

					if ( '' === $sjpta_title ) {
						continue;
					}

					$sjpta_mark     = isset( $sjpta_card['mark'] ) ? (string) $sjpta_card['mark'] : 'none';
					$sjpta_logo     = isset( $sjpta_card['logo'] ) ? (int) $sjpta_card['logo'] : 0;
					$sjpta_text     = isset( $sjpta_card['text'] ) ? (string) $sjpta_card['text'] : '';
					$sjpta_rows     = ( isset( $sjpta_card['rows'] ) && is_array( $sjpta_card['rows'] ) ) ? $sjpta_card['rows'] : array();
					$sjpta_label    = isset( $sjpta_card['link_label'] ) ? (string) $sjpta_card['link_label'] : '';
					$sjpta_url      = isset( $sjpta_card['link_url'] ) ? (string) $sjpta_card['link_url'] : '';
					$sjpta_emphasis = ! empty( $sjpta_card['emphasis'] );
					?>
					<article class="sjpta-compare__card<?php echo $sjpta_emphasis ? ' is-emphasised' : ''; ?>">
						<?php if ( 'sjp' === $sjpta_mark ) : ?>
							<?php /* The SJP mark ships with the theme rather than the media library, so it is never accidentally deleted. */ ?>
							<img class="sjpta-compare__mark" src="<?php echo esc_url( SJPTA_URI . '/assets/images/logo.svg' ); ?>" alt="" width="55" height="40" loading="lazy" decoding="async">
						<?php elseif ( 'image' === $sjpta_mark && $sjpta_logo ) : ?>
							<span class="sjpta-compare__mark sjpta-compare__mark--tall">
								<?php
								/*
								 * `medium`, not `thumbnail`: the thumbnail size is a
								 * hard square crop, which cuts the wordmark off a logo
								 * that is not square.
								 */
								echo wp_get_attachment_image(
									$sjpta_logo,
									'medium',
									false,
									array(
										'alt'     => '',
										'loading' => 'lazy',
										'sizes'   => '80px',
									)
								);
								?>
							</span>
						<?php endif; ?>

						<h3 class="sjpta-compare__cardtitle"><?php echo esc_html( $sjpta_title ); ?></h3>

						<?php if ( '' !== $sjpta_text ) : ?>
							<p class="sjpta-compare__cardtext"><?php echo esc_html( $sjpta_text ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $sjpta_rows ) ) : ?>
							<dl class="sjpta-compare__rows">
								<?php foreach ( $sjpta_rows as $sjpta_row ) : ?>
									<?php
									$sjpta_term = isset( $sjpta_row['label'] ) ? (string) $sjpta_row['label'] : '';
									$sjpta_val  = isset( $sjpta_row['value'] ) ? (string) $sjpta_row['value'] : '';

									if ( '' === $sjpta_term && '' === $sjpta_val ) {
										continue;
									}
									?>
									<div class="sjpta-compare__row">
										<dt><?php echo esc_html( $sjpta_term ); ?></dt>
										<dd><?php echo esc_html( $sjpta_val ); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>

						<?php if ( '' !== $sjpta_label && '' !== $sjpta_url ) : ?>
							<a class="sjpta-compare__link" href="<?php echo esc_url( $sjpta_url ); ?>">
								<?php echo esc_html( $sjpta_label ); ?>
								<?php echo sjpta_icon( 'chevron-right', 12, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
							</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $sjpta_footnote ) : ?>
			<p class="sjpta-compare__footnote"><?php echo esc_html( $sjpta_footnote ); ?></p>
		<?php endif; ?>
	</div>
</section>
