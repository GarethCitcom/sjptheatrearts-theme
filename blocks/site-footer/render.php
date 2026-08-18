<?php
/**
 * Shared site footer.
 *
 * Built from design-reference/archive/SJPFooter.dc.html: a sign-up band, four
 * columns, then a small-print row. Gaz chose this over the later single-row
 * footer, but on white rather than the archive's purple, so the design's
 * colour roles are translated rather than copied. Headings take the brand
 * purple, body copy the secondary text tone, and the panels that were white at
 * 8% opacity become the page background against the surface.
 *
 * Every link goes somewhere real. Where the design links to a page that does
 * not exist yet, the link is either dropped or pointed at the page that does
 * carry the content, and the ones nobody can answer for yet (the legal
 * documents) are editable and hidden until they are filled in.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_portal   = sjpta_setting( 'portal_url', 'https://book.sjptheatrearts.co.uk/' );
$sjpta_email    = sjpta_setting( 'contact_email', 'sjptheatrearts@yahoo.com' );
$sjpta_locality = sjpta_setting( 'locality', __( 'Bromsgrove, Worcestershire', 'sjptheatrearts' ) );
$sjpta_tagline  = sjpta_setting( 'footer_tagline', __( 'A welcoming place to find your confidence, develop your talent and love performing.', 'sjptheatrearts' ) );
$sjpta_note     = sjpta_setting( 'footer_note', __( 'Formerly Bromsgrove School of Dance.', 'sjptheatrearts' ) );
$sjpta_logo     = SJPTA_URI . '/assets/images/logo.svg';
$sjpta_name     = get_bloginfo( 'name' );

$sjpta_signup = sjpta_setting( 'newsletter_action', '' );
$sjpta_field  = sjpta_setting( 'newsletter_field', 'EMAIL' );

/** Pages the footer points at, resolved once. */
$sjpta_url = static function ( string $slug, string $fallback ): string {
	$page = get_page_by_path( $slug );

	return $page ? (string) get_permalink( $page ) : home_url( $fallback );
};

$sjpta_classes     = $sjpta_url( 'classes', '/classes/' );
$sjpta_about       = $sjpta_url( 'about', '/about/' );
$sjpta_performance = $sjpta_url( 'performances', '/performances/' );
$sjpta_contact     = $sjpta_url( 'contact', '/contact/' );
$sjpta_timetable   = $sjpta_url( 'timetable-and-fees', '/timetable-and-fees/' );

/*
 * The design lists the four age groups and links each to the class list. Now
 * that the list filters, they link to the filtered view instead: a parent of a
 * three year old lands on the classes for three year olds rather than on all
 * fifteen. The routes come from the same array the homepage and class list use,
 * so a new age group appears here without anyone editing this file.
 */
$sjpta_columns = array(
	array(
		'title' => __( 'Classes', 'sjptheatrearts' ),
		'links' => array(),
	),
	array(
		'title' => __( 'About', 'sjptheatrearts' ),
		'links' => array(
			array( __( 'Our story', 'sjptheatrearts' ), $sjpta_about ),
			array( __( 'Teachers', 'sjptheatrearts' ), $sjpta_about . '#teachers' ),
			array( __( 'Inclusion & safeguarding', 'sjptheatrearts' ), $sjpta_about . '#safeguarding' ),
			array( __( 'Exams & progression', 'sjptheatrearts' ), $sjpta_about . '#exams' ),
			array( __( 'Performances', 'sjptheatrearts' ), $sjpta_performance ),
			array( __( 'Gallery', 'sjptheatrearts' ), $sjpta_performance . '#gallery' ),
		),
	),
);

foreach ( sjpta_age_routes() as $sjpta_slug => $sjpta_route ) {
	/*
	 * "First Steps (2 to 4)", but plain "Adults": that route's short form is its
	 * own name, and "Adults (Adults)" is what happens when a label is assembled
	 * without looking at what it says.
	 */
	$sjpta_label = $sjpta_route['name'];

	if ( $sjpta_route['short'] !== $sjpta_route['name'] ) {
		$sjpta_label = sprintf(
			/* translators: 1: age group name, 2: the ages it covers. */
			__( '%1$s (%2$s)', 'sjptheatrearts' ),
			$sjpta_route['name'],
			$sjpta_route['short']
		);
	}

	$sjpta_columns[0]['links'][] = array(
		$sjpta_label,
		add_query_arg( 'age', $sjpta_slug, $sjpta_classes ) . '#all',
	);
}

/*
 * "Private lessons" has no page of its own, but the class list now filters by
 * tag, so it lands on the classes taught one to one (Gaz, phase 9). The slug
 * is the slugified pill text on those classes; if the client rewords the pill,
 * this filters to an honest empty list rather than breaking.
 */
$sjpta_columns[0]['links'][] = array( __( 'Private lessons', 'sjptheatrearts' ), add_query_arg( 'tag', 'private-one-to-one', $sjpta_classes ) . '#all' );
$sjpta_columns[0]['links'][] = array( __( 'Timetable & fees', 'sjptheatrearts' ), $sjpta_timetable );

/** Social accounts, each shown only when there is one to link to. */
$sjpta_social = array_filter(
	array(
		'instagram' => array( sjpta_setting( 'instagram_url', '' ), __( 'Instagram', 'sjptheatrearts' ) ),
		'facebook'  => array( sjpta_setting( 'facebook_url', '' ), __( 'Facebook', 'sjptheatrearts' ) ),
		'tiktok'    => array( sjpta_setting( 'tiktok_url', '' ), __( 'TikTok', 'sjptheatrearts' ) ),
	),
	static function ( array $account ): bool {
		return '' !== $account[0];
	}
);

$sjpta_legal = function_exists( 'get_field' ) ? get_field( 'legal_links', 'option' ) : array();
$sjpta_legal = is_array( $sjpta_legal ) ? $sjpta_legal : array();
?>
<footer class="sjpta-footer">
	<div class="sjpta-footer__inner">

		<?php
		/*
		 * Sign-up band. The address goes to this site first, which stores it
		 * under Enquiries and passes it to the mailing tool from the server
		 * (inc/newsletter.php). The tool still owns the list, the double opt-in
		 * and the unsubscribe; the visitor just stays on the page. With
		 * JavaScript on the script posts it and swaps in the thank-you; with it
		 * off the form posts back to this page and lands on the same message.
		 *
		 * With no address configured there is no field at all, only a way to get
		 * in touch. A box that swallowed a parent's email address and did nothing
		 * with it would be worse than not asking.
		 */
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a redirect flag, not acting on it.
		$sjpta_signup_sent = isset( $_GET['newsletter'] ) && 'sent' === $_GET['newsletter'];

		$sjpta_signup_errors = isset( $GLOBALS['sjpta_newsletter_errors'] ) && is_array( $GLOBALS['sjpta_newsletter_errors'] ) ? $GLOBALS['sjpta_newsletter_errors'] : array();
		$sjpta_signup_values = isset( $GLOBALS['sjpta_newsletter_values'] ) && is_array( $GLOBALS['sjpta_newsletter_values'] ) ? $GLOBALS['sjpta_newsletter_values'] : array();
		$sjpta_signup_error  = isset( $sjpta_signup_errors['email'] ) ? (string) $sjpta_signup_errors['email'] : '';
		$sjpta_signup_stamp  = (string) time();
		?>
		<div class="sjpta-footer__signup" id="signup">
			<div class="sjpta-footer__signuptext">
				<h2 class="sjpta-footer__signuphead"><?php esc_html_e( 'Get the term dates and news', 'sjptheatrearts' ); ?></h2>
				<p class="sjpta-footer__signupnote"><?php esc_html_e( 'Occasional emails about term dates, shows and workshop places. Nothing else.', 'sjptheatrearts' ); ?></p>
			</div>

			<?php if ( '' !== $sjpta_signup && $sjpta_signup_sent ) : ?>
				<p class="sjpta-footer__signupsent" role="status" tabindex="-1">
					<span class="sjpta-footer__signuptick" aria-hidden="true"><?php echo sjpta_icon( 'check', 16, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?></span>
					<?php esc_html_e( 'Thank you. Please check your inbox to confirm.', 'sjptheatrearts' ); ?>
				</p>
			<?php elseif ( '' !== $sjpta_signup ) : ?>
				<form
					class="sjpta-footer__signupform<?php echo '' !== $sjpta_signup_error ? ' has-error' : ''; ?>"
					action="<?php echo esc_url( get_permalink() ? get_permalink() . '#signup' : home_url( '/#signup' ) ); ?>"
					method="post"
					novalidate
					data-sjpta-form="newsletter"
					data-endpoint="<?php echo esc_url( rest_url( SJPTA_ENQUIRY_REST_NS . '/newsletter' ) ); ?>"
					data-sending="<?php esc_attr_e( 'Sending', 'sjptheatrearts' ); ?>"
				>
					<label class="screen-reader-text" for="sjpta-signup-email">
						<?php esc_html_e( 'Your email address', 'sjptheatrearts' ); ?>
					</label>
					<input
						class="sjpta-footer__signupinput"
						id="sjpta-signup-email"
						type="email"
						name="<?php echo esc_attr( '' !== $sjpta_field ? $sjpta_field : 'EMAIL' ); ?>"
						data-field="email"
						value="<?php echo esc_attr( (string) ( $sjpta_signup_values['email'] ?? '' ) ); ?>"
						placeholder="<?php esc_attr_e( 'Your email address', 'sjptheatrearts' ); ?>"
						autocomplete="email"
						required
						<?php echo '' !== $sjpta_signup_error ? 'aria-invalid="true" aria-describedby="sjpta-signup-email-error"' : ''; ?>
					>
					<button class="sjpta-btn sjpta-btn--primary sjpta-footer__signupbtn" type="submit">
						<?php esc_html_e( 'Subscribe', 'sjptheatrearts' ); ?>
					</button>

					<div class="sjpta-footer__hp" aria-hidden="true">
						<label for="sjpta-signup-website"><?php esc_html_e( 'Leave this field empty', 'sjptheatrearts' ); ?></label>
						<input type="text" id="sjpta-signup-website" name="sjpta_website" tabindex="-1" autocomplete="off" value="">
					</div>
					<input type="hidden" name="sjpta_newsletter" value="1">
					<input type="hidden" name="sjpta_type" value="newsletter">
					<input type="hidden" name="sjpta_source" value="<?php echo esc_url( (string) get_permalink() ); ?>">
					<input type="hidden" name="sjpta_t" value="<?php echo esc_attr( $sjpta_signup_stamp ); ?>">
					<input type="hidden" name="sjpta_s" value="<?php echo esc_attr( sjpta_enquiry_signature( $sjpta_signup_stamp ) ); ?>">

					<?php if ( '' !== $sjpta_signup_error ) : ?>
						<span class="sjpta-footer__signuperror" id="sjpta-signup-email-error" role="alert"><?php echo esc_html( $sjpta_signup_error ); ?></span>
					<?php endif; ?>

					<template data-sjpta-sent>
						<p class="sjpta-footer__signupsent" role="status" tabindex="-1">
							<span class="sjpta-footer__signuptick" aria-hidden="true"><?php echo sjpta_icon( 'check', 16, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?></span>
							<?php esc_html_e( 'Thank you. Please check your inbox to confirm.', 'sjptheatrearts' ); ?>
						</p>
					</template>
				</form>
			<?php else : ?>
				<p class="sjpta-footer__signupsoon">
					<a class="sjpta-btn sjpta-btn--plum" href="<?php echo esc_url( $sjpta_contact ); ?>">
						<?php esc_html_e( 'Ask to be kept posted', 'sjptheatrearts' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>

		<div class="sjpta-footer__columns">
			<div class="sjpta-footer__brand">
				<a class="sjpta-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: site name. */ __( '%s home', 'sjptheatrearts' ), $sjpta_name ) ); ?>">
					<?php /* Intrinsic ratio 1199:875, so 62px tall is 85px wide. */ ?>
					<img src="<?php echo esc_url( $sjpta_logo ); ?>" alt="" width="85" height="62" loading="lazy" decoding="async">
				</a>

				<?php
				/*
				 * One sentence, written whole in the setting. The locality was
				 * appended here at first, which read "Dance, sing and perform in
				 * Bromsgrove. Based in Bromsgrove, Worcestershire." A field that
				 * already says where the school is cannot be told again by code
				 * that cannot read it.
				 */
				?>
				<?php if ( '' !== $sjpta_tagline ) : ?>
					<p class="sjpta-footer__tagline"><?php echo esc_html( $sjpta_tagline ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $sjpta_social ) ) : ?>
					<ul class="sjpta-footer__social">
						<?php foreach ( $sjpta_social as $sjpta_key => $sjpta_account ) : ?>
							<li>
								<?php
								/*
								 * The warning goes in the aria-label, not in a nested
								 * screen-reader span: an aria-label replaces the element's
								 * content as its accessible name, so a span inside this
								 * icon-only link would never be announced.
								 */
								$sjpta_sociallabel = sjpta_is_external_url( $sjpta_account[0] )
									? sprintf(
										/* translators: %s: the name of a social network. */
										__( '%s (opens in a new tab)', 'sjptheatrearts' ),
										$sjpta_account[1]
									)
									: $sjpta_account[1];
								?>
								<a href="<?php echo esc_url( $sjpta_account[0] ); ?>" aria-label="<?php echo esc_attr( $sjpta_sociallabel ); ?>"<?php echo sjpta_external_attr( $sjpta_account[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
									<?php echo sjpta_icon( $sjpta_key, 18, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php foreach ( $sjpta_columns as $sjpta_column ) : ?>
				<nav class="sjpta-footer__column" aria-label="<?php echo esc_attr( $sjpta_column['title'] ); ?>">
					<h2 class="sjpta-footer__coltitle"><?php echo esc_html( $sjpta_column['title'] ); ?></h2>
					<ul class="sjpta-footer__collinks">
						<?php foreach ( $sjpta_column['links'] as $sjpta_link ) : ?>
							<li><a href="<?php echo esc_url( $sjpta_link[1] ); ?>"><?php echo esc_html( $sjpta_link[0] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endforeach; ?>

			<div class="sjpta-footer__column">
				<h2 class="sjpta-footer__coltitle"><?php esc_html_e( 'Get in touch', 'sjptheatrearts' ); ?></h2>
				<ul class="sjpta-footer__contact">
					<?php if ( '' !== $sjpta_email ) : ?>
						<li>
							<span class="sjpta-footer__conticon" aria-hidden="true">
								<?php echo sjpta_icon( 'mail', 17, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
							</span>
							<span><a href="<?php echo esc_url( 'mailto:' . $sjpta_email ); ?>"><?php echo esc_html( $sjpta_email ); ?></a></span>
						</li>
					<?php endif; ?>

					<li>
						<span class="sjpta-footer__conticon" aria-hidden="true">
							<?php echo sjpta_icon( 'pin', 17, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</span>
						<span>
							<?php echo esc_html( $sjpta_locality ); ?><br>
							<a class="sjpta-footer__strong" href="<?php echo esc_url( $sjpta_contact ); ?>">
								<?php esc_html_e( 'Studio address & parking', 'sjptheatrearts' ); ?>
							</a>
						</span>
					</li>

					<li>
						<span class="sjpta-footer__conticon" aria-hidden="true">
							<?php echo sjpta_icon( 'lock', 17, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</span>
						<span>
							<?php esc_html_e( 'Existing family?', 'sjptheatrearts' ); ?>
							<a class="sjpta-footer__strong" href="<?php echo esc_url( $sjpta_portal ); ?>"<?php echo sjpta_external_attr( $sjpta_portal ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
								<?php esc_html_e( 'Member login', 'sjptheatrearts' ); ?>
								<?php echo sjpta_new_tab_note( $sjpta_portal ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
							</a>
						</span>
					</li>
				</ul>

				<?php
				/*
				 * The booking system's own mark, linking to the vendor's site
				 * (Gaz, phase 9). Their http URL redirects to https, so the
				 * https form is linked directly. Intrinsic ratio 624.68:202.62,
				 * so 148px wide is 48px tall.
				 */
				$sjpta_tempo = 'https://tempo-book-it.com/';
				?>
				<a class="sjpta-footer__tempo" href="<?php echo esc_url( $sjpta_tempo ); ?>"<?php echo sjpta_external_attr( $sjpta_tempo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
					<img
						src="<?php echo esc_url( SJPTA_URI . '/assets/images/tempo.svg' ); ?>"
						alt="<?php esc_attr_e( 'Powered by Tempo', 'sjptheatrearts' ); ?>"
						width="148"
						height="48"
						loading="lazy"
						decoding="async"
					>
					<?php echo sjpta_new_tab_note( $sjpta_tempo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
				</a>
			</div>
		</div>

		<div class="sjpta-footer__bottom">
			<span class="sjpta-footer__legal">
				<?php
				printf(
					/* translators: 1: year, 2: site name. */
					esc_html__( '© %1$s %2$s.', 'sjptheatrearts' ),
					esc_html( wp_date( 'Y' ) ),
					esc_html( $sjpta_name )
				);
				?>
				<?php echo esc_html( $sjpta_note ); ?>
			</span>

			<ul class="sjpta-footer__small">
				<?php foreach ( $sjpta_legal as $sjpta_link ) : ?>
					<?php
					$sjpta_label = trim( (string) ( $sjpta_link['label'] ?? '' ) );
					$sjpta_href  = trim( (string) ( $sjpta_link['url'] ?? '' ) );
					?>
					<?php if ( '' !== $sjpta_label && '' !== $sjpta_href ) : ?>
						<li>
							<a href="<?php echo esc_url( $sjpta_href ); ?>"<?php echo sjpta_external_attr( $sjpta_href ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
								<?php echo esc_html( $sjpta_label ); ?>
								<?php echo sjpta_new_tab_note( $sjpta_href ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php
				/*
				 * Cookie preferences. The consent script owns the dialog and hooks
				 * this by its data-cc attribute, so the link is not an ACF legal row
				 * and always renders: a visitor who accepted once has to be able to
				 * change their mind, whether or not anyone has filled the options in.
				 */
				?>
				<li>
					<a href="#" data-cc="c-settings" aria-haspopup="dialog">
						<?php esc_html_e( 'Cookie preferences', 'sjptheatrearts' ); ?>
					</a>
				</li>
			</ul>

			<a class="sjpta-footer__top-link" href="#sjpta-top">
				<?php esc_html_e( 'Back to top', 'sjptheatrearts' ); ?>
				<span class="sjpta-footer__top-icon" aria-hidden="true">
					<?php echo sjpta_icon( 'arrow-up', 11, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
				</span>
			</a>
		</div>
	</div>
</footer>
