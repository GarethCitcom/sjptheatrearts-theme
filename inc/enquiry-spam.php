<?php
/**
 * Enquiries: spam checks.
 *
 * Every form on the site (class enquiry, enrolment, contact, Born To Be, the
 * footer sign-up) goes through sjpta_enquiry_spam_reason() before anything is
 * stored or emailed. It answers with an empty string for a person and a short
 * reason for a bot, and the caller quarantines the submission: stored under
 * Enquiries with the status "Spam", emailed to nobody, and told it succeeded.
 *
 * Nothing is dropped on the floor. Every check here can be wrong about a real
 * parent once in a while, so a wrong verdict costs a click on "Not spam" in
 * the admin rather than a lost message. Quarantined submissions are pruned
 * after SJPTA_ENQUIRY_SPAM_DAYS.
 *
 * The layers, cheapest first:
 *
 *  1. Honeypot and signed timestamp (sjpta_enquiry_looks_human()).
 *  2. Interaction token: a hidden field the script fills on the first key
 *     press or click. Without it the form must have been open longer.
 *  3. Rate limit per address, so a flood is cut off after a handful.
 *  4. Content rules: non-Latin script, links, markup, name-as-message.
 *  5. Cloudflare Turnstile, when keys are set on Enquiries > Settings.
 *  6. Akismet, when the plugin is active and has a key.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** How long a quarantined submission is kept before it is deleted. */
const SJPTA_ENQUIRY_SPAM_DAYS = 30;

/**
 * Minimum age of a form that was posted without the interaction token.
 *
 * With scripting off nothing can fill the token, so the only tell left is
 * time. Eight seconds is still well under what a person needs to read the
 * labels and type an address; a script that fetched the page and posted
 * straight back is caught.
 */
const SJPTA_ENQUIRY_MIN_SECONDS_NO_TOKEN = 8;

/** Submissions allowed per address inside SJPTA_ENQUIRY_RATE_WINDOW. */
const SJPTA_ENQUIRY_RATE_LIMIT = 5;

/** Length of the rate-limit window, in seconds. */
const SJPTA_ENQUIRY_RATE_WINDOW = 10 * MINUTE_IN_SECONDS;

/** The field Turnstile's token arrives in. */
const SJPTA_ENQUIRY_TURNSTILE_FIELD = 'sjpta_ts';

/* ---------- Settings ---------- */

/**
 * The spam settings, filled out with defaults.
 *
 * Kept alongside the routing settings in the same option so the one settings
 * screen saves everything.
 *
 * @return array{turnstile_site_key:string,turnstile_secret:string,akismet:bool}
 */
function sjpta_enquiry_spam_settings(): array {
	$saved = get_option( SJPTA_ENQUIRY_OPTION, array() );
	$saved = is_array( $saved ) ? $saved : array();

	return array(
		'turnstile_site_key' => isset( $saved['turnstile_site_key'] ) ? trim( (string) $saved['turnstile_site_key'] ) : '',
		'turnstile_secret'   => isset( $saved['turnstile_secret'] ) ? trim( (string) $saved['turnstile_secret'] ) : '',
		'akismet'            => ! isset( $saved['akismet'] ) || ! empty( $saved['akismet'] ),
	);
}

/**
 * Is Turnstile switched on?
 *
 * Both keys are needed: a site key alone would draw a widget nothing checks,
 * and a secret alone has nothing to check.
 *
 * @return bool
 */
function sjpta_enquiry_turnstile_enabled(): bool {
	$settings = sjpta_enquiry_spam_settings();

	return '' !== $settings['turnstile_site_key'] && '' !== $settings['turnstile_secret'];
}

/**
 * The Turnstile site key, for the form to render the widget with.
 *
 * @return string Empty when Turnstile is off.
 */
function sjpta_enquiry_turnstile_site_key(): string {
	return sjpta_enquiry_turnstile_enabled() ? sjpta_enquiry_spam_settings()['turnstile_site_key'] : '';
}

/**
 * Can Akismet be asked?
 *
 * The plugin has to be active and hold a key. Both are checked at call time,
 * so switching the plugin on later starts the checks with no change here.
 *
 * @return bool
 */
function sjpta_enquiry_akismet_available(): bool {
	if ( ! class_exists( 'Akismet' ) || ! method_exists( 'Akismet', 'get_api_key' ) || ! method_exists( 'Akismet', 'http_post' ) ) {
		return false;
	}

	return '' !== (string) Akismet::get_api_key();
}

/**
 * Is Akismet switched on and usable?
 *
 * @return bool
 */
function sjpta_enquiry_akismet_enabled(): bool {
	return sjpta_enquiry_spam_settings()['akismet'] && sjpta_enquiry_akismet_available();
}

/* ---------- The visitor ---------- */

/**
 * The visitor's address, as well as it can be known.
 *
 * Cloudflare's header first, for a site behind its proxy; otherwise whatever
 * the server saw. Used for the rate limit and passed to Turnstile and Akismet.
 * A forged header only helps a bot dodge the rate limit, which is the least
 * of the checks, so it is not worth refusing.
 *
 * @return string
 */
function sjpta_enquiry_visitor_ip(): string {
	$candidates = array(
		isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) : '',
		isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['REMOTE_ADDR'] ) ) : '',
	);

	foreach ( $candidates as $ip ) {
		$ip = trim( $ip );

		if ( '' !== $ip && filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}
	}

	return '';
}

/**
 * The visitor's browser string, trimmed to something storable.
 *
 * @return string
 */
function sjpta_enquiry_visitor_agent(): string {
	$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['HTTP_USER_AGENT'] ) ) : '';

	return substr( $agent, 0, 300 );
}

/* ---------- Interaction token ---------- */

/**
 * The value the script writes into the interaction field.
 *
 * Signed against the same timestamp as the form, so it can neither be reused
 * on another form nor guessed. Printed on the form as a data attribute; the
 * point is not that it is secret, but that only a browser that fires a real
 * key or pointer event on the form gets it copied into the field.
 *
 * @param string $stamp The form's timestamp.
 *
 * @return string
 */
function sjpta_enquiry_interaction_token( string $stamp ): string {
	return sjpta_enquiry_signature( 'interaction:' . $stamp );
}

/* ---------- Rate limit ---------- */

/**
 * Count this submission against the visitor's address and say if it is over.
 *
 * Counted before it is judged, so a flood of spam is cut off as surely as a
 * flood of anything else. A visitor with no known address is not limited:
 * better one bot through than every parent behind a broken proxy locked out.
 *
 * @return bool True when this submission is over the limit.
 */
function sjpta_enquiry_rate_limited(): bool {
	$ip = sjpta_enquiry_visitor_ip();

	if ( '' === $ip ) {
		return false;
	}

	$key   = 'sjpta_rl_' . md5( $ip );
	$count = (int) get_transient( $key ) + 1;

	/*
	 * The window is renewed on every hit, so it measures a gap: five in ten
	 * minutes, and after that a ten-minute pause before the next is counted
	 * afresh. A trickle that never pauses stays limited, which is what a
	 * trickle deserves.
	 */
	set_transient( $key, $count, SJPTA_ENQUIRY_RATE_WINDOW );

	return $count > SJPTA_ENQUIRY_RATE_LIMIT;
}

/* ---------- Content rules ---------- */

/**
 * The text a visitor typed, joined for the content checks.
 *
 * Only free-text fields: a select or a checkbox holds words this side chose.
 *
 * @param array<string,string> $values Sanitised field values.
 *
 * @return array<string,string> Field name => text, free-text fields only.
 */
function sjpta_enquiry_typed_text( array $values ): array {
	$fields = function_exists( 'sjpta_enquiry_fields' ) ? sjpta_enquiry_fields() : array();
	$out    = array();

	foreach ( $values as $name => $value ) {
		$type = isset( $fields[ $name ] ) ? (string) $fields[ $name ]['type'] : 'text';

		if ( in_array( $type, array( 'text', 'textarea', 'email', 'tel' ), true ) && '' !== $value ) {
			$out[ $name ] = $value;
		}
	}

	return $out;
}

/**
 * Does the text contain a script no parent here writes in?
 *
 * The school teaches in English, in England, and every enquiry it has ever
 * had was typed in the Latin alphabet. Cyrillic, CJK, Arabic, Hebrew, Thai
 * and the like are treated as spam. Accents and other Latin extensions are
 * fine: a name like "Zoë" or "José" must never be refused.
 *
 * @param string $text Text to check.
 *
 * @return bool
 */
function sjpta_enquiry_has_foreign_script( string $text ): bool {
	return 1 === preg_match( '/[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}\p{Arabic}\p{Hebrew}\p{Thai}\p{Devanagari}\p{Bengali}\p{Georgian}\p{Armenian}]/u', $text );
}

/**
 * Does the text carry a link?
 *
 * Bare "www." and "http" both count, and so does the "example . com" spacing
 * bots use to slip past simpler tests. Parents ask about classes; they do not
 * paste URLs.
 *
 * @param string $text Text to check.
 *
 * @return bool
 */
function sjpta_enquiry_has_link( string $text ): bool {
	// An email address is not a link, and a parent may well type theirs into the message.
	$text = (string) preg_replace( '/[^\s@<>]+@[^\s@<>]+\.[a-z]{2,}/i', ' ', $text );

	if ( 1 === preg_match( '#(https?:|ftp:|www\.)#i', $text ) ) {
		return true;
	}

	// bare-domain.tld with a real-looking TLD, e.g. "site.ru", "shop . com".
	return 1 === preg_match( '/\b[a-z0-9-]+\s?\.\s?(com|net|org|ru|info|biz|xyz|top|club|site|online|shop|store|io|co|uk|de|cn|pro|link|click)\b/i', $text );
}

/**
 * Judge the words themselves.
 *
 * @param array<string,string> $values Sanitised field values.
 *
 * @return string Reason, or empty when nothing looks wrong.
 */
function sjpta_enquiry_content_reason( array $values ): string {
	$typed = sjpta_enquiry_typed_text( $values );

	if ( empty( $typed ) ) {
		return '';
	}

	$all     = implode( "\n", array_diff_key( $typed, array( 'email' => 1 ) ) );
	$message = (string) ( $values['message'] ?? '' );
	$name    = (string) ( $values['parent_name'] ?? '' );
	$email   = (string) ( $values['email'] ?? '' );

	if ( sjpta_enquiry_has_foreign_script( implode( "\n", $typed ) ) ) {
		return __( 'Text in a non-Latin alphabet', 'sjptheatrearts' );
	}

	if ( sjpta_enquiry_has_link( $all ) ) {
		return __( 'Contains a link', 'sjptheatrearts' );
	}

	// HTML is already stripped by the sanitiser; BBCode is not, and only forum spam writes it.
	if ( 1 === preg_match( '/\[(?:url|link|b|i|img)[=\]]/i', $all ) ) {
		return __( 'Contains markup', 'sjptheatrearts' );
	}

	// A name that is a sentence, or that carries digits, was not typed by its owner.
	if ( '' !== $name && ( mb_strlen( $name ) > 60 || 1 === preg_match( '/\d{3,}/', $name ) ) ) {
		return __( 'Name does not look like a name', 'sjptheatrearts' );
	}

	// The same string in name and message is a script filling every box with one value.
	if ( '' !== $message && '' !== $name && 0 === strcasecmp( trim( $message ), trim( $name ) ) ) {
		return __( 'Name and message are the same', 'sjptheatrearts' );
	}

	// A message that is one long unbroken token is a key, a hash, or a paste, not a question.
	if ( '' !== $message && mb_strlen( $message ) > 40 && false === strpos( trim( $message ), ' ' ) ) {
		return __( 'Message is one unbroken string', 'sjptheatrearts' );
	}

	// Throwaway address patterns that only ever arrive with spam.
	if ( '' !== $email && 1 === preg_match( '/@(?:[a-z0-9-]+\.)*(?:ru|su|by|kz|top|xyz|click|link)$/i', $email ) ) {
		return __( 'Email domain seen only in spam', 'sjptheatrearts' );
	}

	return '';
}

/* ---------- Turnstile ---------- */

/**
 * Ask Cloudflare whether the widget's token is real.
 *
 * Fails open on a network error: if Cloudflare cannot be reached, a parent's
 * message is not the thing to sacrifice. The other layers still apply.
 *
 * @param string $token What the widget put in the form.
 *
 * @return string Reason, or empty when verified (or unverifiable).
 */
function sjpta_enquiry_turnstile_reason( string $token ): string {
	if ( '' === $token ) {
		return __( 'Turnstile check missing', 'sjptheatrearts' );
	}

	$secret = sjpta_enquiry_spam_settings()['turnstile_secret'];
	$body   = array(
		'secret'   => $secret,
		'response' => $token,
	);

	$ip = sjpta_enquiry_visitor_ip();

	if ( '' !== $ip ) {
		$body['remoteip'] = $ip;
	}

	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 6,
			'body'    => $body,
		)
	);

	if ( is_wp_error( $response ) ) {
		return '';
	}

	$data = json_decode( (string) wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $data ) ) {
		return '';
	}

	if ( ! empty( $data['success'] ) ) {
		return '';
	}

	$codes = isset( $data['error-codes'] ) && is_array( $data['error-codes'] ) ? implode( ', ', array_map( 'strval', $data['error-codes'] ) ) : '';

	// A key problem is our mistake, not the visitor's: fail open and say so in the record.
	if ( false !== strpos( $codes, 'secret' ) || false !== strpos( $codes, 'sitekey' ) ) {
		return '';
	}

	/* translators: %s: error codes from Cloudflare. */
	return '' !== $codes ? sprintf( __( 'Turnstile check failed (%s)', 'sjptheatrearts' ), $codes ) : __( 'Turnstile check failed', 'sjptheatrearts' );
}

/* ---------- Akismet ---------- */

/**
 * Ask Akismet.
 *
 * Sent as a `contact-form` submission, which is what Akismet expects from a
 * form that is not a comment. Fails open on any answer that is not a clear
 * "true".
 *
 * @param array<string,string> $values Sanitised field values.
 * @param string               $type   Form type key.
 *
 * @return string Reason, or empty when Akismet calls it ham (or cannot say).
 */
function sjpta_enquiry_akismet_reason( array $values, string $type ): string {
	if ( ! sjpta_enquiry_akismet_enabled() ) {
		return '';
	}

	$content = array();

	foreach ( sjpta_enquiry_typed_text( $values ) as $name => $value ) {
		if ( ! in_array( $name, array( 'parent_name', 'email', 'phone' ), true ) ) {
			$content[] = $value;
		}
	}

	$args = array(
		'blog'                 => home_url( '/' ),
		'blog_lang'            => get_locale(),
		'blog_charset'         => get_option( 'blog_charset', 'UTF-8' ),
		'user_ip'              => sjpta_enquiry_visitor_ip(),
		'user_agent'           => sjpta_enquiry_visitor_agent(),
		'referrer'             => (string) wp_get_referer(),
		'permalink'            => home_url( '/' ),
		'comment_type'         => 'newsletter' === $type ? 'signup' : 'contact-form',
		'comment_author'       => (string) ( $values['parent_name'] ?? '' ),
		'comment_author_email' => (string) ( $values['email'] ?? '' ),
		'comment_content'      => implode( "\n\n", $content ),
	);

	$query = method_exists( 'Akismet', 'build_query' ) ? Akismet::build_query( $args ) : http_build_query( $args );

	// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	$response = Akismet::http_post( $query, 'comment-check' );

	if ( ! is_array( $response ) || ! isset( $response[1] ) ) {
		return '';
	}

	return 'true' === trim( (string) $response[1] ) ? __( 'Akismet says spam', 'sjptheatrearts' ) : '';
}

/* ---------- The verdict ---------- */

/**
 * Why a submission is spam, or nothing if it is not.
 *
 * Two stages, so the caller can run the free checks before validation and
 * the ones that cost a network round trip only on a submission that is
 * otherwise complete. `local` is the honeypot, timestamp, interaction token,
 * rate limit and content rules; `remote` is Turnstile and Akismet; `all`
 * runs both in that order. Each stage stops at the first objection.
 *
 * @param array<string,mixed>  $posted Unslashed request body.
 * @param array<string,string> $values Sanitised field values (may be partial).
 * @param string               $type   Form type key.
 * @param string               $stage  `local`, `remote` or `all`.
 *
 * @return string Empty for a person, otherwise a short reason for the record.
 */
function sjpta_enquiry_spam_reason( array $posted, array $values, string $type, string $stage = 'all' ): string {
	if ( 'remote' !== $stage ) {
		$reason = sjpta_enquiry_spam_reason_local( $posted, $values );

		if ( '' !== $reason ) {
			return $reason;
		}
	}

	if ( 'local' !== $stage ) {
		return sjpta_enquiry_spam_reason_remote( $posted, $values, $type );
	}

	return '';
}

/**
 * The checks that cost nothing.
 *
 * The rate limit is counted before any verdict, so a flood is cut off
 * whatever else it says.
 *
 * @param array<string,mixed>  $posted Unslashed request body.
 * @param array<string,string> $values Sanitised field values (may be partial).
 *
 * @return string Reason, or empty.
 */
function sjpta_enquiry_spam_reason_local( array $posted, array $values ): string {
	$limited = sjpta_enquiry_rate_limited();

	if ( ! empty( $posted['sjpta_website'] ) ) {
		return __( 'Filled the hidden field', 'sjptheatrearts' );
	}

	$stamp     = isset( $posted['sjpta_t'] ) ? (string) $posted['sjpta_t'] : '';
	$signature = isset( $posted['sjpta_s'] ) ? (string) $posted['sjpta_s'] : '';

	if ( '' === $stamp || ! hash_equals( sjpta_enquiry_signature( $stamp ), $signature ) ) {
		return __( 'Form timestamp missing or forged', 'sjptheatrearts' );
	}

	$age   = time() - (int) $stamp;
	$token = isset( $posted['sjpta_h'] ) ? (string) $posted['sjpta_h'] : '';

	if ( '' !== $token && ! hash_equals( sjpta_enquiry_interaction_token( $stamp ), $token ) ) {
		return __( 'Interaction token forged', 'sjptheatrearts' );
	}

	$minimum = '' !== $token ? SJPTA_ENQUIRY_MIN_SECONDS : SJPTA_ENQUIRY_MIN_SECONDS_NO_TOKEN;

	if ( $age < $minimum ) {
		/* translators: %d: number of seconds. */
		return sprintf( __( 'Sent %d seconds after the page loaded', 'sjptheatrearts' ), max( 0, $age ) );
	}

	if ( $limited ) {
		return __( 'Too many submissions from one address', 'sjptheatrearts' );
	}

	return sjpta_enquiry_content_reason( $values );
}

/**
 * The checks that ask another service.
 *
 * @param array<string,mixed>  $posted Unslashed request body.
 * @param array<string,string> $values Sanitised field values.
 * @param string               $type   Form type key.
 *
 * @return string Reason, or empty.
 */
function sjpta_enquiry_spam_reason_remote( array $posted, array $values, string $type ): string {
	if ( sjpta_enquiry_turnstile_enabled() ) {
		$reason = sjpta_enquiry_turnstile_reason( isset( $posted[ SJPTA_ENQUIRY_TURNSTILE_FIELD ] ) ? (string) $posted[ SJPTA_ENQUIRY_TURNSTILE_FIELD ] : '' );

		if ( '' !== $reason ) {
			return $reason;
		}
	}

	return sjpta_enquiry_akismet_reason( $values, $type );
}

/**
 * The old yes/no question, kept for anything still asking it.
 *
 * @param array<string,mixed> $posted Unslashed request body.
 *
 * @return bool
 */
function sjpta_enquiry_looks_human( array $posted ): bool {
	return '' === sjpta_enquiry_spam_reason( $posted, array(), 'contact' );
}

/* ---------- Quarantine ---------- */

/**
 * Keep a spam submission where an admin can see it.
 *
 * Same storage as a real enquiry, with the status "spam" and the reason
 * recorded, so a wrong verdict is one click from being put right. Values are
 * cut short: nobody needs ten kilobytes of a bot's message on file.
 *
 * @param array<string,string> $values  Sanitised field values.
 * @param string               $type    Type key.
 * @param string               $subject Which form it came from.
 * @param string               $source  Page it was sent from.
 * @param string               $reason  Why it was judged spam.
 *
 * @return int Post id, or 0.
 */
function sjpta_enquiry_quarantine( array $values, string $type, string $subject, string $source, string $reason ): int {
	foreach ( $values as $name => $value ) {
		$values[ $name ] = mb_substr( (string) $value, 0, 2000 );
	}

	$id = sjpta_store_enquiry( $values, $type, array(), $subject, $source );

	if ( $id ) {
		update_post_meta( $id, '_sjpta_status', 'spam' );
		update_post_meta( $id, '_sjpta_spam_reason', $reason );
		update_post_meta( $id, '_sjpta_ip', sjpta_enquiry_visitor_ip() );
		update_post_meta( $id, '_sjpta_agent', sjpta_enquiry_visitor_agent() );
	}

	return (int) $id;
}

/**
 * The quarantined submissions older than their keep.
 *
 * @param int $limit How many ids to return at most.
 *
 * @return array<int,int>
 */
function sjpta_enquiry_expired_spam_ids( int $limit = 200 ): array {
	$ids = get_posts(
		array(
			'post_type'        => SJPTA_ENQUIRY_POST_TYPE,
			'post_status'      => 'any',
			'posts_per_page'   => $limit,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- a daily clean-up.
				array(
					'key'   => '_sjpta_status',
					'value' => 'spam',
				),
			),
			'date_query'       => array(
				array(
					'column' => 'post_date_gmt',
					'before' => gmdate( 'Y-m-d H:i:s', time() - SJPTA_ENQUIRY_SPAM_DAYS * DAY_IN_SECONDS ),
				),
			),
		)
	);

	return array_map( 'intval', (array) $ids );
}

/**
 * Delete quarantined submissions past their keep.
 *
 * Runs on the same daily hook as the retention prune.
 *
 * @return int How many were removed.
 */
function sjpta_enquiry_prune_spam(): int {
	$count = 0;

	foreach ( sjpta_enquiry_expired_spam_ids() as $id ) {
		if ( wp_delete_post( $id, true ) ) {
			++$count;
		}
	}

	return $count;
}
add_action( SJPTA_ENQUIRY_CRON, 'sjpta_enquiry_prune_spam' );

/**
 * Let a quarantined submission through after all.
 *
 * Marks it new and sends the email that was held back, to the addresses the
 * form would have used. Called from the "Not spam" links in the admin.
 *
 * @param int $post_id Enquiry id.
 *
 * @return bool Whether an email went out.
 */
function sjpta_enquiry_release( int $post_id ): bool {
	$post = get_post( $post_id );

	if ( ! $post || SJPTA_ENQUIRY_POST_TYPE !== $post->post_type ) {
		return false;
	}

	$type    = (string) get_post_meta( $post_id, '_sjpta_type', true );
	$subject = (string) get_post_meta( $post_id, '_sjpta_form', true );
	$source  = (string) get_post_meta( $post_id, '_sjpta_source', true );
	$values  = array();

	foreach ( array_keys( sjpta_enquiry_fields() ) as $name ) {
		$value = (string) get_post_meta( $post_id, '_sjpta_' . $name, true );

		if ( '' !== $value ) {
			$values[ $name ] = $value;
		}
	}

	if ( ! isset( sjpta_enquiry_types()[ $type ] ) ) {
		$type = 'contact';
	}

	$to = sjpta_enquiry_recipients( $type );

	update_post_meta( $post_id, '_sjpta_status', 'new' );
	update_post_meta( $post_id, '_sjpta_sent_to', implode( ', ', $to ) );
	delete_post_meta( $post_id, '_sjpta_spam_reason' );

	if ( 'newsletter' === $type && function_exists( 'sjpta_newsletter_forward' ) && ! empty( $values['email'] ) ) {
		update_post_meta( $post_id, '_sjpta_forwarded', sjpta_newsletter_forward( $values['email'] ) );
	}

	return sjpta_send_enquiry( $values, $type, $to, $subject, $post_id, $source );
}

/* ---------- Form markup ---------- */

/**
 * The attributes a form carries for the script's spam layers.
 *
 * `data-h` is the interaction token the script copies into the hidden field
 * on the first real key press or click. `data-turnstile` is the Turnstile
 * site key, present only when Turnstile is on, and tells the script to load
 * the widget once the visitor starts on the form.
 *
 * @param string $stamp The form's timestamp.
 *
 * @return string Escaped attributes, with a leading space.
 */
function sjpta_enquiry_form_spam_attrs( string $stamp ): string {
	$attrs = ' data-h="' . esc_attr( sjpta_enquiry_interaction_token( $stamp ) ) . '"';
	$key   = sjpta_enquiry_turnstile_site_key();

	if ( '' !== $key ) {
		$attrs .= ' data-turnstile="' . esc_attr( $key ) . '"';
	}

	return $attrs;
}

/**
 * Print the hidden interaction field and, when on, the Turnstile mount.
 *
 * The mount is an empty element the script renders the widget into. It
 * takes no space until Cloudflare decides to show something, and with
 * scripting off it is simply an empty div.
 *
 * @param string $css_class Class for the Turnstile mount.
 *
 * @return void
 */
function sjpta_enquiry_spam_fields( string $css_class = 'sjpta-form__turnstile' ): void {
	echo '<input type="hidden" name="sjpta_h" value="">' . "\n";

	if ( sjpta_enquiry_turnstile_enabled() ) {
		printf( '<div class="%s" data-turnstile-mount></div>' . "\n", esc_attr( $css_class ) );
	}
}
