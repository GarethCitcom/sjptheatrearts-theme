<?php
/**
 * 301 map from the old sjptheatrearts.co.uk structure.
 *
 * Built from the old site's own sitemap index (2026-08-11):
 * post-sitemap1.xml, page-sitemap1.xml and category-sitemap1.xml.
 *
 * Three slugs survive unchanged, which is the best outcome available: /about/,
 * /classes/ and /contact/ are the same address on both sites and need no rule
 * at all. Everything below either moved or has no equivalent.
 *
 * **Only fires on a 404.** A real page always wins, so publishing a page at one
 * of these paths later retires its rule automatically rather than leaving a
 * redirect that shadows live content. It also means valid URLs pay nothing:
 * WordPress has already decided there is no post before any of this runs.
 *
 * These can move to the host's .htaccess at any point; docs/redirects.md holds
 * the same table in a form that is easy to translate.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Old path to new destination.
 *
 * Keys are paths without the leading or trailing slash. A value starting `http`
 * is used as-is; anything else is treated as a path on this site.
 *
 * @return array<string,string>
 */
function sjpta_redirect_map(): array {
	return array(

		/* Moved, and the reason is obvious. */
		'timetable'                => 'timetable-and-fees/',
		'gallery'                  => 'performances/#gallery',

		/*
		 * The old shop and account pages. WooCommerce ran on that install and
		 * does not on this one; bookings, invoices and payment all happen in the
		 * member portal now, so that is where an old /my-account/ link belongs.
		 */
		'my-account'               => 'PORTAL',

		/*
		 * No equivalent page, so these go to the nearest thing that answers the
		 * same question. Judgement calls, all flagged in docs/redirects.md:
		 *
		 * - news      the new site has no news section; Performances is where
		 *             "what is happening" now lives.
		 * - room-hire not offered on the new site as far as the build knows, so
		 *             it goes to Contact rather than nowhere.
		 * - alumni    About is the only page that talks about the school's history.
		 */
		'news'                     => 'performances/',
		'room-hire'                => 'contact/',
		'alumni'                   => 'about/',

		/*
		 * Old posts. Both are still true subjects: the troupes' Alton Towers
		 * booking is named on the Performances page, and summer schools have
		 * their own card there.
		 */
		'alton-towers-scarefest'   => 'performances/',
		'summer-school-2022'       => 'performances/',

		/*
		 * Placeholder and plugin leftovers from the old build. They carry no
		 * content anybody linked to on purpose, so they go home rather than
		 * pretending to have moved somewhere specific.
		 */
		'title-of-the-blog-post'   => '',
		'title-of-the-blog-post-2' => '',
		'front-user-submit-form'   => '',
		'fe-fs-user-admin'         => '',
		'shop'                     => '',
		'cart'                     => '',
		'checkout'                 => '',
	);
}

/**
 * Send an old URL to its new home.
 *
 * @return void
 */
function sjpta_redirect_legacy(): void {
	if ( ! is_404() ) {
		return;
	}

	/*
	 * esc_url_raw() rather than sanitize_text_field(), which strips percent-encoded
	 * octets and would quietly mangle any old URL containing an encoded character.
	 * Only the path is used, and only to look up a fixed map.
	 */
	$uri = isset( $_SERVER['REQUEST_URI'] )
		? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
		: '';

	$path = wp_parse_url( $uri, PHP_URL_PATH );
	$path = trim( (string) $path, '/' );

	if ( '' === $path ) {
		return;
	}

	/*
	 * Every category on the old site was a stock "Uncategorized" in one of sixty
	 * languages, left behind by a translation plugin. None of them is worth its
	 * own rule, and none has an equivalent here.
	 */
	if ( str_starts_with( $path, 'category/' ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}

	$map = sjpta_redirect_map();

	if ( ! isset( $map[ $path ] ) ) {
		return;
	}

	$target = $map[ $path ];

	if ( 'PORTAL' === $target ) {
		/*
		 * wp_redirect, not wp_safe_redirect: the portal is a different host and
		 * the safe variant would refuse it. The address comes from a setting we
		 * control, not from the request.
		 */
		wp_redirect( sjpta_setting( 'portal_url', 'https://book.sjptheatrearts.co.uk/' ), 301 ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- deliberate off-site redirect to the member portal.
		exit;
	}

	wp_safe_redirect( home_url( '/' . $target ), 301 );
	exit;
}
add_action( 'template_redirect', 'sjpta_redirect_legacy' );
