<?php
/**
 * Head hygiene.
 *
 * Minimal in phase 1 — enough to keep the Lighthouse gate honest from the first
 * rendered template. Resource hints, image handling and the fuller pass belong
 * to phase 8.
 *
 * Nothing here blanket-dequeues anything a plugin may need.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Note: raising `styles_inline_size_limit` was tried and reverted.
 *
 * It did remove the last render-blocking stylesheet and improved FCP (1.7s →
 * 1.4s), but it inlined the header's 10KB on every page, pushing total page CSS
 * to 66KB — past the 60KB budget — and LCP did not move. Leaving core's default
 * keeps that shared 10KB in one cacheable file across page views, which is the
 * better trade for a multi-page site. Revisit in the phase 8 performance pass,
 * after the CSS itself has been trimmed.
 */

/**
 * Remove the emoji detection script and its styles.
 *
 * Roughly 12KB of JavaScript plus a DNS lookup, for a feature no modern browser
 * needs. Removed front end and admin alike.
 *
 * @return void
 */
function sjpta_disable_emoji(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'sjpta_disable_emoji' );

/**
 * Drop the emoji entry from the TinyMCE plugin list left behind by the above.
 *
 * @param array<string,string>|mixed $plugins Registered TinyMCE plugins.
 *
 * @return array<string,string> Filtered plugins.
 */
function sjpta_disable_emoji_tinymce( $plugins ): array {
	if ( ! is_array( $plugins ) ) {
		return array();
	}

	return array_diff( $plugins, array( 'wpemoji' ) );
}
add_filter( 'tiny_mce_plugins', 'sjpta_disable_emoji_tinymce' );
