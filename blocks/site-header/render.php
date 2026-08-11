<?php
/**
 * Site header: utility bar, floating pill nav, mobile menu overlay.
 *
 * Works with JavaScript disabled. The mobile menu is a native <details>, so it
 * opens, closes and is keyboard operable with no script at all. JavaScript only
 * adds the condensing behaviour described in the design, and the page is fully
 * usable without it.
 *
 * The navigation list is rendered twice — once inline in the pill for desktop,
 * once inside the overlay for mobile. Only one is ever displayed, so only one is
 * ever in the accessibility tree. This is deliberate: a single list cannot live
 * in two places in the DOM, and the alternative (revealing one list from the
 * other's open state) depends on overriding the <details> content box, which is
 * not reliable across browsers.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_items    = sjpta_nav_items();
$sjpta_portal   = sjpta_setting( 'portal_url', 'https://book.sjptheatrearts.co.uk/' );
$sjpta_locality = sjpta_setting( 'locality', __( 'Bromsgrove, Worcestershire', 'sjptheatrearts' ) );

/*
 * Sister brands share this header but change four things: the strapline, the
 * Instagram account, and the label and destination of the call to action. Each
 * is an attribute set by the page's template part, defaulting to the SJP site
 * settings, so every existing page is unaffected.
 */
$sjpta_attrs = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$sjpta_strap = ! empty( $sjpta_attrs['strapline'] )
	? (string) $sjpta_attrs['strapline']
	: sjpta_setting( 'strapline', __( 'Dance · Sing · Perform, classes from babies to adults, all abilities welcome', 'sjptheatrearts' ) );

/*
 * The sister brand's Instagram is a site setting, not a value typed into the
 * template part, because the same account is linked from three places on that
 * page. One field, one place to correct it.
 */
$sjpta_variant = isset( $sjpta_attrs['variant'] ) ? (string) $sjpta_attrs['variant'] : '';

$sjpta_insta = 'born-to-be' === $sjpta_variant
	? sjpta_setting( 'btb_instagram_url', 'https://www.instagram.com/born.to.be4/' )
	: sjpta_setting( 'instagram_url', 'https://www.instagram.com/sjp_theatre_arts/' );

if ( ! empty( $sjpta_attrs['instagramUrl'] ) ) {
	$sjpta_insta = (string) $sjpta_attrs['instagramUrl'];
}

$sjpta_join     = get_page_by_path( 'join' );
$sjpta_join_url = $sjpta_join ? get_permalink( $sjpta_join ) : home_url( '/join/' );

$sjpta_cta_label = ! empty( $sjpta_attrs['ctaLabel'] )
	? (string) $sjpta_attrs['ctaLabel']
	: __( 'Enrol now', 'sjptheatrearts' );

$sjpta_cta_url = ! empty( $sjpta_attrs['ctaUrl'] )
	? (string) $sjpta_attrs['ctaUrl']
	: (string) $sjpta_join_url;

$sjpta_logo      = SJPTA_URI . '/assets/images/logo.svg';
$sjpta_logo_w    = SJPTA_URI . '/assets/images/logo-white.svg';
$sjpta_home_name = get_bloginfo( 'name' );

// The homepage hero sits under the nav; every other template starts below it.
$sjpta_classes = 'sjpta-header';
if ( is_front_page() ) {
	$sjpta_classes .= ' sjpta-header--overlap';
}
?>
<div class="<?php echo esc_attr( $sjpta_classes ); ?>" data-sjpta-header>

	<div class="sjpta-utility">
		<div class="sjpta-utility__inner">
			<span class="sjpta-utility__locality">
				<span class="sjpta-utility__dot" aria-hidden="true"></span>
				<?php echo esc_html( $sjpta_locality ); ?>
			</span>

			<span class="sjpta-utility__strapline"><?php echo esc_html( $sjpta_strap ); ?></span>

			<span class="sjpta-utility__links">
				<a class="sjpta-utility__link" href="<?php echo esc_url( $sjpta_insta ); ?>"<?php echo sjpta_external_attr( $sjpta_insta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
					<?php esc_html_e( 'Instagram', 'sjptheatrearts' ); ?>
					<?php echo sjpta_new_tab_note( $sjpta_insta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
				</a>
				<span class="sjpta-utility__divider" aria-hidden="true"></span>
				<a class="sjpta-utility__member" href="<?php echo esc_url( $sjpta_portal ); ?>"<?php echo sjpta_external_attr( $sjpta_portal ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
					<?php echo sjpta_icon( 'lock', 12, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
					<?php esc_html_e( 'Member login', 'sjptheatrearts' ); ?>
					<?php echo sjpta_new_tab_note( $sjpta_portal ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
				</a>
			</span>
		</div>
	</div>

	<div class="sjpta-navwrap">
		<div class="sjpta-pill">
			<a class="sjpta-pill__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: site name. */ __( '%s home', 'sjptheatrearts' ), $sjpta_home_name ) ); ?>">
				<?php /* Intrinsic ratio 1199:875, so 36px tall is 49px wide. Explicit dimensions keep CLS at zero. */ ?>
				<img src="<?php echo esc_url( $sjpta_logo ); ?>" alt="" width="49" height="36" decoding="async">
			</a>

			<span class="sjpta-pill__divider" aria-hidden="true"></span>

			<nav class="sjpta-pill__nav" aria-label="<?php esc_attr_e( 'Primary', 'sjptheatrearts' ); ?>">
				<?php foreach ( $sjpta_items as $sjpta_item ) : ?>
					<a
						class="sjpta-pill__link<?php echo $sjpta_item['current'] ? ' is-current' : ''; ?>"
						href="<?php echo esc_url( $sjpta_item['url'] ); ?>"
						<?php echo $sjpta_item['current'] ? ' aria-current="page"' : ''; ?>
					><?php echo esc_html( $sjpta_item['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>

			<a class="sjpta-btn sjpta-btn--primary sjpta-pill__cta" href="<?php echo esc_url( $sjpta_cta_url ); ?>">
				<?php echo esc_html( $sjpta_cta_label ); ?>
				<?php echo sjpta_icon( 'arrow-right', 14, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
			</a>

			<details class="sjpta-menu">
				<?php
				/*
				 * The summary is both the hamburger and the close button. When the
				 * menu is open it is repositioned over the overlay as the X, so
				 * closing is native browser behaviour and needs no JavaScript.
				 *
				 * A separate close control would be inert without script, and the
				 * fixed overlay covers this summary in its resting position — so
				 * anyone with JavaScript off would be trapped in the menu.
				 */
				?>
				<summary class="sjpta-menu__toggle">
					<span class="screen-reader-text sjpta-menu__label sjpta-menu__label--open"><?php esc_html_e( 'Menu', 'sjptheatrearts' ); ?></span>
					<span class="screen-reader-text sjpta-menu__label sjpta-menu__label--close"><?php esc_html_e( 'Close menu', 'sjptheatrearts' ); ?></span>
					<span class="sjpta-menu__bars" aria-hidden="true"><span></span><span></span></span>
					<span class="sjpta-menu__x" aria-hidden="true">
						<?php echo sjpta_icon( 'close', 16, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
					</span>
				</summary>

				<div class="sjpta-menu__panel">
					<?php /* Decoration lives in its own clipped layer so the blobs cannot make the panel scroll. */ ?>
					<span class="sjpta-menu__decor" aria-hidden="true"></span>

					<div class="sjpta-menu__head">
						<img class="sjpta-menu__logo" src="<?php echo esc_url( $sjpta_logo_w ); ?>" alt="" width="44" height="32" decoding="async">
					</div>

					<nav class="sjpta-menu__nav" aria-label="<?php esc_attr_e( 'Primary, mobile', 'sjptheatrearts' ); ?>">
						<a class="sjpta-menu__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<span><span class="sjpta-menu__num" aria-hidden="true">01</span><?php esc_html_e( 'Home', 'sjptheatrearts' ); ?></span>
							<?php echo sjpta_icon( 'arrow-up-right', 16, '#FF9A3D' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</a>
						<?php foreach ( $sjpta_items as $sjpta_i => $sjpta_item ) : ?>
							<a
								class="sjpta-menu__link"
								href="<?php echo esc_url( $sjpta_item['url'] ); ?>"
								<?php echo $sjpta_item['current'] ? ' aria-current="page"' : ''; ?>
							>
								<span>
									<span class="sjpta-menu__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $sjpta_i + 2 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<?php echo esc_html( $sjpta_item['label'] ); ?>
								</span>
								<?php echo sjpta_icon( 'arrow-up-right', 16, '#FF9A3D' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
							</a>
						<?php endforeach; ?>

						<a class="sjpta-menu__link sjpta-menu__link--member" href="<?php echo esc_url( $sjpta_portal ); ?>"<?php echo sjpta_external_attr( $sjpta_portal ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
							<span>
								<?php echo sjpta_icon( 'lock', 15, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								<?php esc_html_e( 'Member login', 'sjptheatrearts' ); ?>
								<?php echo sjpta_new_tab_note( $sjpta_portal ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
							</span>
						</a>
					</nav>

					<div class="sjpta-menu__foot">
						<a class="sjpta-btn sjpta-btn--primary sjpta-menu__cta" href="<?php echo esc_url( $sjpta_cta_url ); ?>">
							<?php echo esc_html( $sjpta_cta_label ); ?>
						</a>
						<div class="sjpta-menu__meta">
							<span><?php echo esc_html( $sjpta_locality ); ?></span>
							<a href="<?php echo esc_url( $sjpta_insta ); ?>"<?php echo sjpta_external_attr( $sjpta_insta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
								<?php esc_html_e( 'Instagram', 'sjptheatrearts' ); ?>
								<?php echo sjpta_new_tab_note( $sjpta_insta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
							</a>
						</div>
					</div>
				</div>
			</details>
		</div>
	</div>
</div>
