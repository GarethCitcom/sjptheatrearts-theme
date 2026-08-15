<?php
/**
 * The footer newsletter sign-up.
 *
 * The address is stored under Enquiries like every other submission, emailed
 * to whoever the Settings screen names (nobody, by default), and passed on to
 * the mailing tool from the server. The tool still owns the list, the double
 * opt-in and the unsubscribe; the difference from posting straight to it is
 * that the visitor stays on the page, and a sign-up that the tool refuses or
 * never receives is not simply lost.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Where sign-ups are forwarded to, if anywhere.
 *
 * @return string
 */
function sjpta_newsletter_action(): string {
	return esc_url_raw( sjpta_setting( 'newsletter_action', '' ) );
}

/**
 * The name of the address field the mailing tool expects.
 *
 * @return string
 */
function sjpta_newsletter_field(): string {
	$field = sjpta_setting( 'newsletter_field', 'EMAIL' );

	return '' !== $field ? $field : 'EMAIL';
}

/**
 * Validate, store, email and forward one sign-up.
 *
 * Same shape of verdict as sjpta_enquiry_process(), so the REST endpoint and
 * the plain POST handler answer it the same way.
 *
 * @param array<string,mixed> $posted Unslashed request body.
 *
 * @return array{ok:bool,spam:bool,errors:array<string,string>,values:array<string,string>,id:int}
 */
function sjpta_newsletter_process( array $posted ): array {
	$result = array(
		'ok'     => false,
		'spam'   => false,
		'errors' => array(),
		'values' => array(),
		'id'     => 0,
	);

	if ( ! sjpta_enquiry_looks_human( $posted ) ) {
		$result['ok']   = true;
		$result['spam'] = true;

		return $result;
	}

	$field = sjpta_newsletter_field();
	$raw   = trim( isset( $posted[ $field ] ) ? (string) $posted[ $field ] : (string) ( $posted['email'] ?? '' ) );
	$email = sanitize_email( $raw );

	$result['values'] = array( 'email' => $email );

	if ( '' === $raw ) {
		$result['errors']['email'] = __( 'Please type your email address.', 'sjptheatrearts' );

		return $result;
	}

	if ( '' === $email || ! is_email( $email ) ) {
		$result['errors']['email'] = __( 'That email address does not look right. Please check it.', 'sjptheatrearts' );

		return $result;
	}

	$to = sjpta_enquiry_recipients( 'newsletter' );
	$id = sjpta_store_enquiry( array( 'email' => $email ), 'newsletter', $to, __( 'Footer sign-up', 'sjptheatrearts' ), sjpta_enquiry_source( $posted ) );

	$forwarded = sjpta_newsletter_forward( $email );

	if ( $id ) {
		update_post_meta( $id, '_sjpta_forwarded', $forwarded );
	}

	sjpta_send_enquiry( array( 'email' => $email ), 'newsletter', $to, __( 'Footer sign-up', 'sjptheatrearts' ), $id );

	$result['ok'] = true;
	$result['id'] = $id;

	return $result;
}

/**
 * Pass an address to the mailing tool.
 *
 * A plain form post from the server, exactly what the tool's own embed code
 * sends from a browser, so it works with any provider that publishes such an
 * endpoint (Mailchimp, Brevo, MailerLite). Success is a 2xx or a redirect: the
 * tools answer with an HTML page, and reading it for a verdict would tie this
 * to one provider's markup.
 *
 * @param string $email Address to sign up.
 *
 * @return string A short note for the stored record.
 */
function sjpta_newsletter_forward( string $email ): string {
	$action = sjpta_newsletter_action();

	if ( '' === $action ) {
		return __( 'No mailing list address is set under SJP settings.', 'sjptheatrearts' );
	}

	$response = wp_remote_post(
		$action,
		array(
			'timeout'     => 8,
			'redirection' => 0,
			'body'        => array( sjpta_newsletter_field() => $email ),
			'headers'     => array( 'Referer' => home_url( '/' ) ),
			'user-agent'  => 'SJPTheatreArts/' . SJPTA_VERSION . ' (' . home_url( '/' ) . ')',
		)
	);

	if ( is_wp_error( $response ) ) {
		/* translators: %s: error message. */
		return sprintf( __( 'Failed: %s', 'sjptheatrearts' ), $response->get_error_message() );
	}

	$code = (int) wp_remote_retrieve_response_code( $response );

	if ( $code >= 200 && $code < 400 ) {
		/* translators: %s: HTTP status code. */
		return sprintf( __( 'Yes (HTTP %s)', 'sjptheatrearts' ), (string) $code );
	}

	/* translators: %s: HTTP status code. */
	return sprintf( __( 'Refused (HTTP %s)', 'sjptheatrearts' ), (string) $code );
}

/**
 * Handle a plain POST from the footer, before any output.
 *
 * @return void
 */
function sjpta_handle_newsletter(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- an unauthenticated public form, protected by a honeypot and a signed timestamp; see sjpta_enquiry_looks_human().
	if ( empty( $_POST['sjpta_newsletter'] ) ) {
		return;
	}

	$posted = wp_unslash( $_POST );
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	$result = sjpta_newsletter_process( is_array( $posted ) ? $posted : array() );

	if ( $result['ok'] ) {
		sjpta_enquiry_redirect( 'signup', 'newsletter' );
	}

	$GLOBALS['sjpta_newsletter_errors'] = $result['errors'];
	$GLOBALS['sjpta_newsletter_values'] = $result['values'];
}
add_action( 'template_redirect', 'sjpta_handle_newsletter' );

/**
 * The endpoint the script posts sign-ups to.
 *
 * @return void
 */
function sjpta_register_newsletter_route(): void {
	register_rest_route(
		SJPTA_ENQUIRY_REST_NS,
		'/newsletter',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'sjpta_rest_newsletter',
		)
	);
}
add_action( 'rest_api_init', 'sjpta_register_newsletter_route' );

/**
 * Answer the script.
 *
 * @param WP_REST_Request $request The request.
 *
 * @return WP_REST_Response
 */
function sjpta_rest_newsletter( WP_REST_Request $request ): WP_REST_Response {
	$posted = $request->get_body_params();

	return sjpta_enquiry_rest_response( sjpta_newsletter_process( is_array( $posted ) ? $posted : array() ) );
}
