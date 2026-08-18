<?php
/**
 * SJP Theatre Arts theme bootstrap.
 *
 * Thin by design: this file defines constants and requires the modules in
 * inc/. Behaviour belongs in those modules, not here.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme version, read from the style.css header so it cannot drift.
 * Used to cache-bust enqueued assets.
 */
if ( ! defined( 'SJPTA_VERSION' ) ) {
	$sjpta_theme = wp_get_theme( get_template() );
	define( 'SJPTA_VERSION', (string) $sjpta_theme->get( 'Version' ) );
	unset( $sjpta_theme );
}

if ( ! defined( 'SJPTA_DIR' ) ) {
	define( 'SJPTA_DIR', get_template_directory() );
}

if ( ! defined( 'SJPTA_URI' ) ) {
	define( 'SJPTA_URI', get_template_directory_uri() );
}

require_once SJPTA_DIR . '/inc/setup.php';
require_once SJPTA_DIR . '/inc/enqueue.php';
require_once SJPTA_DIR . '/inc/blocks.php';
require_once SJPTA_DIR . '/inc/classes.php';
require_once SJPTA_DIR . '/inc/class-import.php';
require_once SJPTA_DIR . '/inc/class-rest.php';
require_once SJPTA_DIR . '/inc/tempo/client.php';
require_once SJPTA_DIR . '/inc/tempo/cache.php';
require_once SJPTA_DIR . '/inc/tempo/match.php';
require_once SJPTA_DIR . '/inc/tempo/cli.php';
require_once SJPTA_DIR . '/inc/enquiry.php';
require_once SJPTA_DIR . '/inc/enquiry-spam.php';
require_once SJPTA_DIR . '/inc/enquiry-admin.php';
require_once SJPTA_DIR . '/inc/enquiry-form.php';
require_once SJPTA_DIR . '/inc/newsletter.php';
require_once SJPTA_DIR . '/inc/seo.php';
require_once SJPTA_DIR . '/inc/redirects.php';
require_once SJPTA_DIR . '/inc/performance.php';
