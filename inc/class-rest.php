<?php
/**
 * The REST endpoint behind the class filters.
 *
 * `GET /wp-json/sjptheatrearts/v1/classes?age=&style=&day=&tag=`
 *
 * Every filter is a multi-select and travels as a comma-separated list of
 * slugs; sjpta_filter_values() is the one parser.
 *
 * Returns the same markup the page renders on a full load, produced by the same
 * function, so a filtered view can never drift from a first load.
 *
 * Public and read-only: it exposes nothing that is not already on a public page.
 * It is an enhancement, not the mechanism, and the page works without it.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the route.
 *
 * @return void
 */
function sjpta_register_class_route(): void {
	register_rest_route(
		'sjptheatrearts/v1',
		'/classes',
		array(
			'methods'             => WP_REST_Server::READABLE,

			/*
			 * Public on purpose. Every class it can return is already published
			 * and readable at its own URL; requiring a nonce here would break on
			 * a cached page for no gain.
			 */
			'permission_callback' => '__return_true',
			'args'                => array(
				'age'   => array( 'default' => '' ),
				'style' => array( 'default' => '' ),
				'day'   => array( 'default' => '' ),
				'tag'   => array( 'default' => '' ),
				'help'  => array(
					'type'    => 'object',
					'default' => array(),
				),
			),
			'callback'            => 'sjpta_rest_classes',
		)
	);
}
add_action( 'rest_api_init', 'sjpta_register_class_route' );

/**
 * Return the filtered cards, and how many there are.
 *
 * @param WP_REST_Request $request The request.
 *
 * @return WP_REST_Response
 */
function sjpta_rest_classes( WP_REST_Request $request ): WP_REST_Response {
	$filters = array(
		'age'   => sjpta_filter_values( $request->get_param( 'age' ) ),
		'style' => sjpta_filter_values( $request->get_param( 'style' ) ),
		'day'   => sjpta_filter_values( $request->get_param( 'day' ) ),
		'tag'   => sjpta_filter_values( $request->get_param( 'tag' ) ),
	);

	$posts = sjpta_query_classes( $filters )['posts'];

	/*
	 * The help card's copy is editable per page, so it travels with the request
	 * rather than being duplicated here. Sanitised on the way in: it is going
	 * straight back out as markup.
	 */
	$help = (array) $request->get_param( 'help' );
	$help = array(
		'help_title' => sanitize_text_field( (string) ( $help['help_title'] ?? '' ) ),
		'help_text'  => sanitize_text_field( (string) ( $help['help_text'] ?? '' ) ),
		'cta_label'  => sanitize_text_field( (string) ( $help['cta_label'] ?? '' ) ),
		'cta_url'    => esc_url_raw( (string) ( $help['cta_url'] ?? '' ) ),
	);

	ob_start();
	sjpta_render_class_cards( $posts, $help );
	$html = (string) ob_get_clean();

	return new WP_REST_Response(
		array(
			'count' => count( $posts ),
			'html'  => $html,
			'label' => sprintf(
				/* translators: %d: number of classes. */
				_n( '%d class', '%d classes', count( $posts ), 'sjptheatrearts' ),
				count( $posts )
			),
		),
		200
	);
}
