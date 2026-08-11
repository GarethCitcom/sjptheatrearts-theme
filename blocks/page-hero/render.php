<?php
/**
 * Page hero.
 *
 * The top of an inner page: breadcrumb, optional brand mark, heading, intro,
 * two buttons and a row of short reassurance points. The `split` layout puts a
 * two-photo collage alongside; `centred` drops the media column and centres the
 * copy, which is what the pages without hero photography need in phase 6.
 *
 * Built generic rather than Born-To-Be-specific because six more inner pages
 * need exactly this shape.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_layout    = sjpta_field( 'layout', 'split', $sjpta_ctx );
$sjpta_crumb     = sjpta_field( 'breadcrumb', '', $sjpta_ctx );
$sjpta_eyebrow   = sjpta_field( 'eyebrow', '', $sjpta_ctx );
$sjpta_heading   = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_highlight = sjpta_field( 'heading_highlight', '', $sjpta_ctx );
$sjpta_intro     = sjpta_field( 'intro', '', $sjpta_ctx );
$sjpta_cta       = sjpta_field( 'cta_label', '', $sjpta_ctx );
$sjpta_cta_url   = sjpta_field( 'cta_url', '#enquire', $sjpta_ctx );
$sjpta_alt       = sjpta_field( 'alt_label', '', $sjpta_ctx );
$sjpta_alt_url   = sjpta_field( 'alt_url', '', $sjpta_ctx );
$sjpta_card_head = sjpta_field( 'card_heading', '', $sjpta_ctx );
$sjpta_card_note = sjpta_field( 'card_note', '', $sjpta_ctx );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_logo  = $sjpta_has_acf ? (int) get_field( 'logo', $sjpta_ctx ) : 0;
$sjpta_main  = $sjpta_has_acf ? (int) get_field( 'photo_main', $sjpta_ctx ) : 0;
$sjpta_inset = $sjpta_has_acf ? (int) get_field( 'photo_inset', $sjpta_ctx ) : 0;

$sjpta_points = $sjpta_has_acf ? get_field( 'points', $sjpta_ctx ) : array();
$sjpta_points = is_array( $sjpta_points ) ? $sjpta_points : array();

$sjpta_split = 'centred' !== $sjpta_layout && ( $sjpta_main || $sjpta_inset );

/*
 * The floating card is passed to the collage helper as markup because it sits
 * inside the collage's positioning context. Built here and escaped here; the
 * helper runs it through wp_kses_post().
 */
$sjpta_card = '';
if ( '' !== $sjpta_card_head ) {
	$sjpta_card = '<span class="sjpta-collage__cardtitle">' . esc_html( $sjpta_card_head ) . '</span>';

	if ( '' !== $sjpta_card_note ) {
		$sjpta_card .= '<span class="sjpta-collage__cardnote">' . esc_html( $sjpta_card_note ) . '</span>';
	}
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-pagehero sjpta-underlap alignfull<?php echo $sjpta_split ? '' : ' sjpta-pagehero--centred'; ?>">
	<span class="sjpta-pagehero__decor" aria-hidden="true"></span>

	<div class="sjpta-inner sjpta-pagehero__inner">
		<div class="sjpta-pagehero__copy" data-reveal>
			<?php if ( '' !== $sjpta_crumb ) : ?>
				<nav class="sjpta-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'sjptheatrearts' ); ?>">
					<a class="sjpta-crumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'sjptheatrearts' ); ?></a>
					<span class="sjpta-crumbs__sep" aria-hidden="true">&rsaquo;</span>
					<span class="sjpta-crumbs__current" aria-current="page"><?php echo esc_html( $sjpta_crumb ); ?></span>
				</nav>
			<?php endif; ?>

			<?php if ( $sjpta_logo ) : ?>
				<span class="sjpta-pagehero__logo">
					<?php
					/*
					 * Alt text is empty on purpose: the H1 immediately below says
					 * the same thing, and a screen reader announcing the brand
					 * name twice in a row is noise, not information.
					 */
					echo wp_get_attachment_image(
						$sjpta_logo,
						'medium',
						false,
						array(

							/*
							 * Eager, but not high priority: the collage
							 * photograph below is the LCP element, and two
							 * competing high-priority fetches simply slow it
							 * down on a throttled connection.
							 */
							'alt'     => '',
							'loading' => 'eager',
							'sizes'   => '160px',
						)
					);
					?>
				</span>
			<?php elseif ( '' !== $sjpta_eyebrow ) : ?>
				<span class="sjpta-eyebrow sjpta-pagehero__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
			<?php endif; ?>

			<h1 class="sjpta-pagehero__heading">
				<?php echo wp_kses_post( sjpta_highlight_words( $sjpta_heading, $sjpta_highlight ) ); ?>
			</h1>

			<?php if ( '' !== $sjpta_intro ) : ?>
				<p class="sjpta-pagehero__intro"><?php echo esc_html( $sjpta_intro ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_cta || '' !== $sjpta_alt ) : ?>
				<div class="sjpta-pagehero__actions">
					<?php if ( '' !== $sjpta_cta ) : ?>
						<a class="sjpta-btn sjpta-btn--primary sjpta-pagehero__btn" href="<?php echo esc_url( $sjpta_cta_url ); ?>">
							<?php echo esc_html( $sjpta_cta ); ?>
							<?php echo sjpta_icon( 'arrow-right', 15, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</a>
					<?php endif; ?>

					<?php if ( '' !== $sjpta_alt ) : ?>
						<a class="sjpta-btn sjpta-btn--outline sjpta-pagehero__btn" href="<?php echo esc_url( $sjpta_alt_url ); ?>">
							<?php echo esc_html( $sjpta_alt ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $sjpta_points ) ) : ?>
				<ul class="sjpta-pagehero__points">
					<?php foreach ( $sjpta_points as $sjpta_point ) : ?>
						<?php
						$sjpta_text = isset( $sjpta_point['text'] ) ? (string) $sjpta_point['text'] : '';

						if ( '' === $sjpta_text ) {
							continue;
						}

						$sjpta_tone = isset( $sjpta_point['tone'] ) ? (string) $sjpta_point['tone'] : 'accent';
						?>
						<li class="sjpta-pagehero__point sjpta-pagehero__point--<?php echo esc_attr( $sjpta_tone ); ?>">
							<span class="sjpta-pagehero__dot" aria-hidden="true"></span>
							<?php echo esc_html( $sjpta_text ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $sjpta_split ) : ?>
			<div class="sjpta-pagehero__media">
				<?php sjpta_photo_collage( $sjpta_main, $sjpta_inset, false, $sjpta_card, true ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
