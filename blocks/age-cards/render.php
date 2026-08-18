<?php
/**
 * Age route cards.
 *
 * Three cards, each a whole link. The arrow is decorative: the card's heading
 * carries the destination, so the link text is never just an icon.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx   = sjpta_block_context( $block ?? null );
$sjpta_cards = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'cards', $sjpta_ctx ) : array();

if ( ! is_array( $sjpta_cards ) || array() === $sjpta_cards ) {
	return;
}

/*
 * The cards are H3s directly under the page's H1, which skips a level and
 * leaves the document outline wrong for anyone navigating by headings.
 *
 * The desktop design shows no heading here, but the mobile design labels this
 * exact group "Choose by age" — so the heading is the designer's own words, not
 * an invention. Left visible if the client sets one, and read out but not shown
 * when they do not, which keeps the desktop layout identical to the design
 * while giving the outline its missing level.
 */
$sjpta_heading = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_hidden  = '' === $sjpta_heading;

if ( $sjpta_hidden ) {
	$sjpta_heading = __( 'Choose by age', 'sjptheatrearts' );
}
?>
<section class="sjpta-agecards alignfull">
	<div class="sjpta-agecards__inner">
		<h2 class="sjpta-agecards__heading<?php echo $sjpta_hidden ? ' screen-reader-text' : ''; ?>">
			<?php echo esc_html( $sjpta_heading ); ?>
		</h2>
		<div class="sjpta-agecards__grid" data-stagger>
			<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
				<?php
				$sjpta_title = isset( $sjpta_card['title'] ) ? (string) $sjpta_card['title'] : '';

				if ( '' === $sjpta_title ) {
					continue;
				}

				$sjpta_url   = isset( $sjpta_card['url'] ) ? (string) $sjpta_card['url'] : '';
				$sjpta_style = isset( $sjpta_card['badge_style'] ) ? (string) $sjpta_card['badge_style'] : 'purple';
				$sjpta_image = isset( $sjpta_card['image'] ) ? (int) $sjpta_card['image'] : 0;
				?>
				<a class="sjpta-card" href="<?php echo esc_url( '' !== $sjpta_url ? $sjpta_url : home_url( '/classes/' ) ); ?>" data-lift>
					<div class="sjpta-card__media">
						<?php if ( $sjpta_image ) : ?>
							<?php
							echo wp_get_attachment_image(
								$sjpta_image,
								'large',
								false,
								array(
									'class'   => 'sjpta-card__img',
									'loading' => 'lazy',
									'sizes'   => '(max-width: 782px) 100vw, (max-width: 1023px) 50vw, 420px',
								)
							);
							?>
						<?php endif; ?>

						<?php if ( ! empty( $sjpta_card['badge'] ) ) : ?>
							<span class="sjpta-badge sjpta-badge--<?php echo esc_attr( $sjpta_style ); ?> sjpta-card__badge">
								<?php echo esc_html( (string) $sjpta_card['badge'] ); ?>
							</span>
						<?php endif; ?>
					</div>

					<div class="sjpta-card__body">
						<h3 class="sjpta-card__title"><?php echo esc_html( $sjpta_title ); ?></h3>

						<?php if ( ! empty( $sjpta_card['text'] ) ) : ?>
							<p class="sjpta-card__text"><?php echo esc_html( (string) $sjpta_card['text'] ); ?></p>
						<?php endif; ?>

						<div class="sjpta-card__foot">
							<span class="sjpta-card__meta"><?php echo esc_html( isset( $sjpta_card['meta'] ) ? (string) $sjpta_card['meta'] : '' ); ?></span>
							<span class="sjpta-card__arrow" aria-hidden="true">
								<?php echo sjpta_icon( 'arrow-up-right', 14, '#381064' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
							</span>
						</div>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
