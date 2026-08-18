<?php
/**
 * Enquiries: form types, settings, handling, storage and email.
 *
 * One implementation, used by every form on the site. Each form names its
 * *type* (class enquiry, enrolment, contact, Born To Be, newsletter) in a
 * hidden field, and the type alone decides where the email goes: the
 * addresses live on the Enquiries → Settings screen and are resolved on the
 * server, so nothing about routing ever travels in the form.
 *
 * Every submission is stored as well as emailed. Email is the part that fails
 * quietly: a misconfigured host, a spam filter, a full mailbox, and a parent's
 * message is gone with no record. The stored copy means it never is. Stored
 * copies are pruned after the retention period set on the same screen.
 *
 * Two ways in, one path through. A form posts to the REST endpoint from
 * JavaScript, or to its own page when scripting is off; both call
 * sjpta_enquiry_process(), so validation, spam checks, storage and email
 * cannot drift apart. The spam checks themselves live in enquiry-spam.php.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SJPTA_ENQUIRY_POST_TYPE = 'sjpta-enquiry';
const SJPTA_ENQUIRY_OPTION    = 'sjpta_enquiry_settings';
const SJPTA_ENQUIRY_REST_NS   = 'sjptheatrearts/v1';
const SJPTA_ENQUIRY_CRON      = 'sjpta_enquiry_prune';

/**
 * The shortest believable time between a page loading and a form being sent.
 *
 * A person has to read seven labels and type into at least three of them. Under
 * three seconds is a script.
 */
const SJPTA_ENQUIRY_MIN_SECONDS = 3;

/**
 * Register the enquiry post type.
 *
 * Not public and not queryable: these are private messages about children, and
 * they must never be reachable from the front end, a feed, the REST API or a
 * search engine.
 *
 * @return void
 */
function sjpta_register_enquiry_post_type(): void {
	register_post_type(
		SJPTA_ENQUIRY_POST_TYPE,
		array(
			'labels'              => array(
				'name'          => __( 'Enquiries', 'sjptheatrearts' ),
				'singular_name' => __( 'Enquiry', 'sjptheatrearts' ),
				'menu_name'     => __( 'Enquiries', 'sjptheatrearts' ),
				'all_items'     => __( 'All enquiries', 'sjptheatrearts' ),
				'edit_item'     => __( 'Enquiry', 'sjptheatrearts' ),
				'search_items'  => __( 'Search enquiries', 'sjptheatrearts' ),
				'not_found'     => __( 'No enquiries yet.', 'sjptheatrearts' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
			'supports'            => array( 'title' ),
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'capabilities'        => array(
				// Enquiries arrive from the public; nobody authors one by hand.
				'create_posts' => 'do_not_allow',
			),
			'has_archive'         => false,
			'rewrite'             => false,
			'delete_with_user'    => false,
		)
	);
}
add_action( 'init', 'sjpta_register_enquiry_post_type' );

/* ---------- Form types and settings ---------- */

/**
 * The kinds of form the site has.
 *
 * `legacy` names the SJP settings field that held the address before the
 * Enquiries settings screen existed, so an install that never visits the new
 * screen keeps sending exactly where it did. `mail_default` says whether a type
 * with no address of its own should fall back to the contact inbox: a parent's
 * enquiry must never be lost, but a newsletter sign-up is a notification, not a
 * message, and nobody asked to be told about each one.
 *
 * @return array<string,array<string,mixed>>
 */
function sjpta_enquiry_types(): array {
	return array(
		'enquiry'    => array(
			'label'        => __( 'Class enquiry', 'sjptheatrearts' ),
			'where'        => __( 'The homepage panel and every class page.', 'sjptheatrearts' ),
			'legacy'       => 'class_enquiry_email',
			'mail_default' => true,
		),
		'enrolment'  => array(
			'label'        => __( 'Enrolment', 'sjptheatrearts' ),
			'where'        => __( 'The enrolment form on the Join page.', 'sjptheatrearts' ),
			'legacy'       => 'class_enquiry_email',
			'mail_default' => true,
		),
		'contact'    => array(
			'label'        => __( 'Contact message', 'sjptheatrearts' ),
			'where'        => __( 'The Contact page.', 'sjptheatrearts' ),
			'legacy'       => 'contact_email',
			'mail_default' => true,
		),
		'born-to-be' => array(
			'label'        => __( 'Born To Be enquiry', 'sjptheatrearts' ),
			'where'        => __( 'The Born To Be page.', 'sjptheatrearts' ),
			'legacy'       => 'btb_enquiry_email',
			'mail_default' => true,
		),
		'newsletter' => array(
			'label'        => __( 'Newsletter sign-up', 'sjptheatrearts' ),
			'where'        => __( 'The sign-up box in the footer. Leave the address empty to record sign-ups without emailing anyone.', 'sjptheatrearts' ),
			'legacy'       => '',
			'mail_default' => false,
		),
	);
}

/**
 * A type's human name.
 *
 * @param string $type Type key.
 *
 * @return string
 */
function sjpta_enquiry_type_label( string $type ): string {
	$types = sjpta_enquiry_types();

	return isset( $types[ $type ] ) ? (string) $types[ $type ]['label'] : __( 'Enquiry', 'sjptheatrearts' );
}

/**
 * The block-level routing keys the panels used before form types existed.
 *
 * Kept so a page saved with the old "recipient" field still routes correctly
 * without anyone re-saving it.
 *
 * @param string $route  Old key: `sjp`, `lottie`, `madison`.
 * @param string $layout Form layout, which tells Join apart from a class page.
 *
 * @return string Type key.
 */
function sjpta_enquiry_type_from_route( string $route, string $layout = '' ): string {
	if ( 'madison' === $route ) {
		return 'born-to-be';
	}

	if ( 'lottie' === $route ) {
		return 'join' === $layout ? 'enrolment' : 'enquiry';
	}

	return 'contact';
}

/**
 * The saved settings, filled out with defaults.
 *
 * @return array{types:array<string,array{emails:string,responder:string}>,retention_days:int}
 */
function sjpta_enquiry_settings(): array {
	$saved = get_option( SJPTA_ENQUIRY_OPTION, array() );
	$saved = is_array( $saved ) ? $saved : array();

	$out = array(
		'types'          => array(),
		'retention_days' => isset( $saved['retention_days'] ) ? max( 0, (int) $saved['retention_days'] ) : 365,
	);

	foreach ( array_keys( sjpta_enquiry_types() ) as $type ) {
		$row = isset( $saved['types'][ $type ] ) && is_array( $saved['types'][ $type ] ) ? $saved['types'][ $type ] : array();

		$out['types'][ $type ] = array(
			'emails'    => isset( $row['emails'] ) ? (string) $row['emails'] : '',
			'responder' => isset( $row['responder'] ) ? (string) $row['responder'] : '',
		);
	}

	return $out;
}

/**
 * Turn a comma or newline separated list into clean addresses.
 *
 * @param string $raw_list Raw list.
 *
 * @return array<int,string>
 */
function sjpta_enquiry_parse_emails( string $raw_list ): array {
	$out = array();

	foreach ( (array) preg_split( '/[\s,;]+/', $raw_list ) as $one ) {
		$one = sanitize_email( trim( $one ) );

		if ( '' !== $one && is_email( $one ) && ! in_array( $one, $out, true ) ) {
			$out[] = $one;
		}
	}

	return $out;
}

/**
 * The address the site fell back to before any of this was configurable.
 *
 * ACF returns an empty string for an options field nobody has saved, rather
 * than the field's default, so the same explicit fallback the footer uses is
 * passed here. Without it the chain reached admin_email, which on this install
 * is the booking provider's support desk.
 *
 * @return string
 */
function sjpta_enquiry_contact_email(): string {
	return sanitize_email( sjpta_setting( 'contact_email', 'sjptheatrearts@yahoo.com' ) );
}

/**
 * Where a type's email goes.
 *
 * Settings screen first; then the SJP settings field the type used to read;
 * then, for anything that is a message from a person, the contact inbox.
 *
 * @param string $type Type key.
 *
 * @return array<int,string> Addresses, possibly empty.
 */
function sjpta_enquiry_recipients( string $type ): array {
	$types    = sjpta_enquiry_types();
	$settings = sjpta_enquiry_settings();

	$emails = sjpta_enquiry_parse_emails( $settings['types'][ $type ]['emails'] ?? '' );

	if ( ! empty( $emails ) ) {
		return $emails;
	}

	$legacy = isset( $types[ $type ] ) ? (string) $types[ $type ]['legacy'] : '';

	if ( '' !== $legacy ) {
		$emails = sjpta_enquiry_parse_emails( sjpta_setting( $legacy, '' ) );

		if ( ! empty( $emails ) ) {
			return $emails;
		}
	}

	if ( isset( $types[ $type ] ) && $types[ $type ]['mail_default'] ) {
		return sjpta_enquiry_parse_emails( sjpta_enquiry_contact_email() );
	}

	return array();
}

/**
 * Who a sender is told will reply.
 *
 * A confirmation that names a person is worth more than one that says "we", but
 * only if the name is right, so it comes from a setting and the sentence falls
 * back to "we" when nobody has filled it in. Class and enrolment enquiries keep
 * the name the SJP settings screen already holds ("Lottie") unless the
 * Enquiries screen says otherwise.
 *
 * @param string $type Type key.
 *
 * @return string Empty when there is no name to give.
 */
function sjpta_enquiry_responder( string $type ): string {
	$settings = sjpta_enquiry_settings();
	$name     = trim( $settings['types'][ $type ]['responder'] ?? '' );

	if ( '' !== $name ) {
		return $name;
	}

	if ( in_array( $type, array( 'enquiry', 'enrolment' ), true ) ) {
		return sjpta_setting( 'class_enquiry_name', 'Lottie' );
	}

	return '';
}

/**
 * Old routing helper, kept for anything still calling it.
 *
 * @param string $key Old routing key.
 *
 * @return string First address the matching type would reach.
 */
function sjpta_enquiry_recipient( string $key ): string {
	$to = sjpta_enquiry_recipients( sjpta_enquiry_type_from_route( $key ) );

	return $to[0] ?? '';
}

/* ---------- Fields ---------- */

/**
 * The fields an enquiry can carry.
 *
 * Shared by the renderer, the handler and the admin screen so the three cannot
 * drift apart.
 *
 * @return array<string,array<string,mixed>>
 */
function sjpta_enquiry_fields(): array {
	return array(

		/*
		 * Error text is written per field rather than assembled from the label.
		 * "Please give us your " . "Your name" reads as "your your name", and a
		 * parent who has just been told they made a mistake deserves better than
		 * a sentence built by string concatenation.
		 */
		'parent_name'     => array(
			'label'        => __( 'Your name', 'sjptheatrearts' ),
			'type'         => 'text',
			'required'     => true,
			'width'        => 'half',
			'autocomplete' => 'name',
			'missing'      => __( 'Please tell us your name.', 'sjptheatrearts' ),
		),
		'child_name'      => array(
			'label' => __( "Child's name", 'sjptheatrearts' ),
			'type'  => 'text',
			'width' => 'half',
		),
		'child_age'       => array(
			'label' => __( "Child's age", 'sjptheatrearts' ),
			'type'  => 'text',
			'width' => 'half',
		),
		'class_want'      => array(
			'label' => __( 'Which class', 'sjptheatrearts' ),
			'type'  => 'select',
			'width' => 'half',
		),
		'email'           => array(
			'label'        => __( 'Email', 'sjptheatrearts' ),
			'type'         => 'email',
			'required'     => true,
			'width'        => 'half',
			'autocomplete' => 'email',
			'missing'      => __( 'Please give us an email address so we can reply.', 'sjptheatrearts' ),
			'invalid'      => __( 'That email address does not look right. Please check it.', 'sjptheatrearts' ),
		),
		'phone'           => array(
			'label'        => __( 'Phone', 'sjptheatrearts' ),
			'type'         => 'tel',
			'width'        => 'half',
			'autocomplete' => 'tel',
		),
		'message'         => array(
			'label' => __( 'Anything you would like us to know', 'sjptheatrearts' ),
			'type'  => 'textarea',
			'width' => 'full',
		),

		/*
		 * The Join form's three extra questions, and Contact's one.
		 *
		 * `interest` is a single choice because "Not sure yet" cannot be true at
		 * the same time as anything else; `days` is a multiple choice because a
		 * family usually has more than one evening free. Both render as real
		 * radios and checkboxes behind pill labels, so they work with no
		 * JavaScript and answer the keyboard exactly as a person expects.
		 */
		'interest'        => array(
			'label'   => __( 'What are they interested in?', 'sjptheatrearts' ),
			'type'    => 'radio',
			'width'   => 'full',
			'choices' => array(
				__( 'Not sure yet', 'sjptheatrearts' ),
				__( 'Dance', 'sjptheatrearts' ),
				__( 'Singing', 'sjptheatrearts' ),
				__( 'Acting', 'sjptheatrearts' ),
				__( 'Acro', 'sjptheatrearts' ),
			),
		),
		'experience'      => array(
			'label'   => __( 'Experience', 'sjptheatrearts' ),
			'type'    => 'select',
			'width'   => 'half',
			'choices' => array(
				__( 'First ever class', 'sjptheatrearts' ),
				__( 'Some experience', 'sjptheatrearts' ),
				__( 'Trained for years', 'sjptheatrearts' ),
			),
		),
		'days'            => array(
			'label' => __( 'Days that work for you', 'sjptheatrearts' ),
			'type'  => 'checkgroup',
			'width' => 'full',
		),
		'topic'           => array(
			'label'   => __( 'What is it about?', 'sjptheatrearts' ),
			'type'    => 'select',
			'width'   => 'half',
			'choices' => array(
				__( 'General question', 'sjptheatrearts' ),
				__( 'Private lessons', 'sjptheatrearts' ),
				__( 'Additional needs tuition', 'sjptheatrearts' ),
				__( 'Parties and events', 'sjptheatrearts' ),
				__( 'Wedding dance lessons', 'sjptheatrearts' ),
				__( 'Show tickets', 'sjptheatrearts' ),
				__( 'Working with SJP', 'sjptheatrearts' ),
			),
		),

		/*
		 * Consent to be contacted about classes, as the class page and Join
		 * designs both show it. Deliberately NOT required: this is permission to
		 * follow up about classes later, and answering the enquiry someone has
		 * just typed does not depend on it. Making it a condition of sending
		 * would block a parent whose question we were always going to answer.
		 */
		'consent_contact' => array(
			'label' => __( 'I am happy for SJP Theatre Arts to contact me about classes.', 'sjptheatrearts' ),
			'type'  => 'checkbox',
			'width' => 'full',
		),
	);
}

/**
 * The answers a choice field is allowed to come back with.
 *
 * Two of them are not fixed lists: the days come from the timetable, and the
 * class menu comes from whatever the panel was given. Both are resolved here so
 * the handler validates against exactly what the form offered.
 *
 * @param string              $name    Field name.
 * @param array<string,mixed> $field   Field definition.
 * @param array<int,string>   $classes Class options this form was built with.
 *
 * @return array<int,string>
 */
function sjpta_enquiry_choices( string $name, array $field, array $classes = array() ): array {
	if ( 'days' === $name ) {
		return sjpta_enquiry_days();
	}

	if ( 'class_want' === $name ) {
		return array_values( array_map( 'strval', $classes ) );
	}

	return array_values( array_map( 'strval', (array) ( $field['choices'] ?? array() ) ) );
}

/**
 * The days a family could realistically choose from.
 *
 * Taken from the timetable rather than typed, so the list follows the term: if
 * the school stops teaching on a Thursday, the Thursday pill goes on its own.
 * Falls back to Monday through Saturday when the feed has nothing, which is the
 * state it was in for most of this build.
 *
 * @return array<int,string>
 */
function sjpta_enquiry_days(): array {
	$names = array(
		1 => __( 'Mon', 'sjptheatrearts' ),
		2 => __( 'Tue', 'sjptheatrearts' ),
		3 => __( 'Wed', 'sjptheatrearts' ),
		4 => __( 'Thu', 'sjptheatrearts' ),
		5 => __( 'Fri', 'sjptheatrearts' ),
		6 => __( 'Sat', 'sjptheatrearts' ),
		7 => __( 'Sun', 'sjptheatrearts' ),
	);

	$days = array();

	if ( function_exists( 'sjpta_timetable' ) ) {
		foreach ( sjpta_timetable()['sessions'] as $session ) {
			$days[ (int) $session['day'] ] = true;
		}
	}

	$days = array_keys( $days );
	sort( $days );

	if ( empty( $days ) ) {
		$days = array( 1, 2, 3, 4, 5, 6 );
	}

	$out = array();

	foreach ( $days as $day ) {
		$out[] = $names[ $day ];
	}

	$out[] = __( 'Any day', 'sjptheatrearts' );

	return $out;
}

/**
 * Where the privacy notice lives, if it has been published.
 *
 * WordPress returns an empty string until the privacy page is published, so the
 * words render as plain text rather than as a link to a 404. Publishing the
 * page turns every consent line into a link with no code change.
 *
 * @return string
 */
function sjpta_privacy_url(): string {
	return (string) get_privacy_policy_url();
}

/* ---------- Spam checks ---------- */

/**
 * Sign a value so the form cannot be replayed with a forged timestamp.
 *
 * @param string $value Value to sign.
 *
 * @return string
 */
function sjpta_enquiry_signature( string $value ): string {
	return hash_hmac( 'sha256', $value, wp_salt( 'nonce' ) );
}

/**
 * The page a submission came from, if it is one of ours.
 *
 * @param array<string,mixed> $posted Unslashed request body.
 *
 * @return string
 */
function sjpta_enquiry_source( array $posted ): string {
	$candidates = array(
		isset( $posted['sjpta_source'] ) ? (string) $posted['sjpta_source'] : '',
		(string) wp_get_referer(),
	);

	$home = (string) wp_parse_url( home_url(), PHP_URL_HOST );

	foreach ( $candidates as $url ) {
		$url = esc_url_raw( $url );

		if ( '' !== $url && strcasecmp( (string) wp_parse_url( $url, PHP_URL_HOST ), $home ) === 0 ) {
			return $url;
		}
	}

	return '';
}

/* ---------- Processing ---------- */

/**
 * Validate, store and send one enquiry.
 *
 * The one path every submission takes, whether it arrived by REST or by a
 * plain POST. Returns a verdict rather than acting on it, so the two callers
 * can answer in their own way: JSON for one, a redirect for the other.
 *
 * A bot gets `spam => true` and `ok => true`. It is told it succeeded, but
 * nothing is sent: the submission is quarantined under Enquiries with the
 * status "Spam" and the reason, so a wrong verdict can be put right.
 *
 * @param array<string,mixed> $posted Unslashed request body.
 *
 * @return array{ok:bool,spam:bool,errors:array<string,string>,values:array<string,string>,id:int}
 */
function sjpta_enquiry_process( array $posted ): array {
	$result = array(
		'ok'     => false,
		'spam'   => false,
		'errors' => array(),
		'values' => array(),
		'id'     => 0,
	);

	$fields = sjpta_enquiry_fields();
	$values = array();
	$errors = array();

	foreach ( $fields as $name => $field ) {
		$raw = trim( isset( $posted[ $name ] ) && ! is_array( $posted[ $name ] ) ? (string) $posted[ $name ] : '' );

		/*
		 * A checkbox is answered by being absent, so it is recorded as a word
		 * rather than left blank. "No" in the email is a decision the visitor
		 * made; an empty line is a field somebody forgot to fill in, and the two
		 * must not look the same to whoever reads it.
		 *
		 * Only forms that actually show the checkbox record an answer. Born To Be
		 * does not ask, and writing "No" against a question nobody was asked would
		 * tell whoever reads the enquiry that a parent declined when they were
		 * never given the chance. A form that asks says so with a hidden field.
		 */
		if ( 'checkbox' === $field['type'] ) {
			if ( empty( $posted[ 'sjpta_asked_' . $name ] ) ) {
				continue;
			}

			$values[ $name ] = '' !== $raw
				? __( 'Yes', 'sjptheatrearts' )
				: __( 'No', 'sjptheatrearts' );
			continue;
		}

		/*
		 * A group of checkboxes arrives as an array. Each answer is checked
		 * against the choices the form actually offered rather than trusted:
		 * anyone can post whatever they like to a public endpoint, and this text
		 * ends up in an email somebody reads.
		 */
		if ( 'checkgroup' === $field['type'] ) {
			$allowed = sjpta_enquiry_choices( $name, $field );
			$picked  = array();

			foreach ( (array) ( $posted[ $name ] ?? array() ) as $one ) {
				$one = sanitize_text_field( (string) $one );

				if ( in_array( $one, $allowed, true ) ) {
					$picked[] = $one;
				}
			}

			$values[ $name ] = implode( ', ', $picked );
			continue;
		}

		if ( 'radio' === $field['type'] || 'select' === $field['type'] ) {
			$allowed = sjpta_enquiry_choices( $name, $field );
			$one     = sanitize_text_field( $raw );

			/*
			 * Only fields with a list this side knows about are held to it. The
			 * class menu is not one: Born To Be names its own two sessions and a
			 * class page posts its own title, so there is no canonical list to
			 * check against and a strict test would silently blank the one field
			 * that says which class the enquiry is about.
			 */
			$values[ $name ] = ( empty( $allowed ) || in_array( $one, $allowed, true ) ) ? $one : '';
			continue;
		}

		$value = 'textarea' === $field['type']
			? sanitize_textarea_field( $raw )
			: sanitize_text_field( $raw );

		if ( 'email' === $field['type'] ) {
			$value = sanitize_email( $raw );
		}

		$values[ $name ] = $value;

		/*
		 * "Typed nothing" and "typed something unusable" are different mistakes
		 * and get different advice. sanitize_email() empties an invalid address,
		 * so without comparing against the raw input a parent who mistyped their
		 * address would be told they had left it blank.
		 */
		if ( '' === $value && '' !== $raw ) {
			$errors[ $name ] = $field['invalid'] ?? __( 'Please check that, it does not look right.', 'sjptheatrearts' );
			continue;
		}

		if ( ! empty( $field['required'] ) && '' === $value ) {
			$errors[ $name ] = $field['missing'] ?? __( 'Please fill this in.', 'sjptheatrearts' );
			continue;
		}

		if ( 'email' === $field['type'] && '' !== $value && ! is_email( $value ) ) {
			$errors[ $name ] = $field['invalid'] ?? __( 'That does not look right. Please check it.', 'sjptheatrearts' );
		}
	}

	$result['values'] = $values;

	$type = isset( $posted['sjpta_type'] ) ? sanitize_key( (string) $posted['sjpta_type'] ) : '';

	if ( ! isset( sjpta_enquiry_types()[ $type ] ) || 'newsletter' === $type ) {
		$type = 'contact';
	}

	$subject = isset( $posted['sjpta_subject'] ) ? sanitize_text_field( (string) $posted['sjpta_subject'] ) : '';
	$source  = sjpta_enquiry_source( $posted );

	/*
	 * The free checks run before validation: a bot that left a required field
	 * empty is still a bot, and it is told "sent" rather than handed a list of
	 * the fields to fill in next time. The checks that call out (Turnstile,
	 * Akismet) wait until the submission is complete, so a parent who missed
	 * a field does not spend a Turnstile token on the attempt.
	 */
	$reason = sjpta_enquiry_spam_reason( $posted, $values, $type, 'local' );

	if ( '' !== $reason ) {
		$result['ok']   = true;
		$result['spam'] = true;
		$result['id']   = sjpta_enquiry_quarantine( $values, $type, $subject, $source, $reason );

		return $result;
	}

	if ( ! empty( $errors ) ) {
		$result['errors'] = $errors;

		return $result;
	}

	$reason = sjpta_enquiry_spam_reason( $posted, $values, $type, 'remote' );

	if ( '' !== $reason ) {
		$result['ok']   = true;
		$result['spam'] = true;
		$result['id']   = sjpta_enquiry_quarantine( $values, $type, $subject, $source, $reason );

		return $result;
	}

	$to = sjpta_enquiry_recipients( $type );
	$id = sjpta_store_enquiry( $values, $type, $to, $subject, $source );
	sjpta_send_enquiry( $values, $type, $to, $subject, $id, $source );

	$result['ok'] = true;
	$result['id'] = $id;

	return $result;
}

/**
 * Handle a plain POST, before any output.
 *
 * The path with JavaScript off, and the fallback the script takes when the
 * endpoint cannot be reached. Post, redirect, get: without the redirect a
 * refresh resubmits the enquiry, and parents refresh.
 *
 * @return void
 */
function sjpta_handle_enquiry(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- an unauthenticated public form, protected by the checks in enquiry-spam.php; see sjpta_enquiry_spam_reason().
	if ( empty( $_POST['sjpta_enquiry'] ) ) {
		return;
	}

	$posted = wp_unslash( $_POST );
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	// Where to send the visitor back to, so the form is on screen when they land.
	$anchor = isset( $posted['sjpta_anchor'] ) ? (string) $posted['sjpta_anchor'] : 'enquire';

	$result = sjpta_enquiry_process( is_array( $posted ) ? $posted : array() );

	if ( $result['ok'] ) {
		sjpta_enquiry_redirect( $anchor );
	}

	$GLOBALS['sjpta_enquiry_errors'] = $result['errors'];
	$GLOBALS['sjpta_enquiry_values'] = $result['values'];
}
add_action( 'template_redirect', 'sjpta_handle_enquiry' );

/**
 * Send the visitor back to the form, showing its sent state.
 *
 * @param string $anchor Section id to return to, so the form is on screen.
 * @param string $flag   Query flag to set. `enquiry` for the enquiry form.
 *
 * @return void
 */
function sjpta_enquiry_redirect( string $anchor = 'enquire', string $flag = 'enquiry' ): void {
	$anchor = sanitize_key( $anchor );
	$url    = add_query_arg( sanitize_key( $flag ), 'sent', get_permalink() );

	if ( '' !== $anchor ) {
		$url .= '#' . $anchor;
	}

	wp_safe_redirect( $url, 303 );
	exit;
}

/* ---------- REST ---------- */

/**
 * The endpoint the script posts to.
 *
 * Public on purpose, like the form it serves: a nonce here would break on a
 * cached page for no gain, and the same spam checks apply.
 *
 * @return void
 */
function sjpta_register_enquiry_route(): void {
	register_rest_route(
		SJPTA_ENQUIRY_REST_NS,
		'/enquiry',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'sjpta_rest_enquiry',
		)
	);
}
add_action( 'rest_api_init', 'sjpta_register_enquiry_route' );

/**
 * Answer the script.
 *
 * @param WP_REST_Request $request The request.
 *
 * @return WP_REST_Response
 */
function sjpta_rest_enquiry( WP_REST_Request $request ): WP_REST_Response {
	$posted = $request->get_body_params();
	$result = sjpta_enquiry_process( is_array( $posted ) ? $posted : array() );

	return sjpta_enquiry_rest_response( $result );
}

/**
 * Shape a processing verdict as JSON.
 *
 * Nothing about the stored copy or the spam verdict goes back to the browser:
 * a bot is told it succeeded, and a visitor does not need a post id.
 *
 * @param array<string,mixed> $result From sjpta_enquiry_process() or the newsletter equivalent.
 *
 * @return WP_REST_Response
 */
function sjpta_enquiry_rest_response( array $result ): WP_REST_Response {
	$body = array(
		'ok'     => (bool) $result['ok'],
		'errors' => (array) ( $result['errors'] ?? array() ),
	);

	if ( ! $body['ok'] ) {
		$body['summary'] = __( 'We could not send that. Please check:', 'sjptheatrearts' );
	}

	$response = new WP_REST_Response( $body, 200 );
	$response->header( 'Cache-Control', 'no-store' );

	return $response;
}

/* ---------- Storage and email ---------- */

/**
 * Store a submission.
 *
 * @param array<string,string> $values  Sanitised field values.
 * @param string               $type    Type key.
 * @param array<int,string>    $to      Addresses it was emailed to.
 * @param string               $subject Which form it came from.
 * @param string               $source  Page it was sent from.
 *
 * @return int Post id, or 0 on failure.
 */
function sjpta_store_enquiry( array $values, string $type, array $to, string $subject = '', string $source = '' ): int {
	$who = $values['parent_name'] ?? '';

	if ( '' === $who ) {
		$who = $values['email'] ?? '';
	}

	if ( ! empty( $values['child_name'] ) ) {
		/* translators: 1: parent name, 2: child name. */
		$title = sprintf( __( '%1$s, about %2$s', 'sjptheatrearts' ), $who, $values['child_name'] );
	} else {
		$title = $who;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => SJPTA_ENQUIRY_POST_TYPE,
			'post_status' => 'private',
			'post_title'  => wp_slash( $title ),
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		return 0;
	}

	foreach ( $values as $name => $value ) {
		update_post_meta( $id, '_sjpta_' . $name, $value );
	}

	update_post_meta( $id, '_sjpta_type', $type );
	update_post_meta( $id, '_sjpta_status', 'new' );
	update_post_meta( $id, '_sjpta_sent_to', implode( ', ', $to ) );
	update_post_meta( $id, '_sjpta_form', $subject );
	update_post_meta( $id, '_sjpta_source', $source );

	return (int) $id;
}

/**
 * Email a submission.
 *
 * From stays on this domain and the visitor's address goes in Reply-To. Sending
 * as the visitor would fail SPF and DMARC at most hosts, and the message would
 * be filed as spam or refused outright.
 *
 * @param array<string,string> $values  Sanitised field values.
 * @param string               $type    Type key.
 * @param array<int,string>    $to      Addresses to send to.
 * @param string               $subject Which form it came from.
 * @param int                  $stored  Post id of the stored copy, 0 if none.
 * @param string               $source  Page it was sent from.
 *
 * @return bool
 */
function sjpta_send_enquiry( array $values, string $type, array $to, string $subject, int $stored, string $source = '' ): bool {
	if ( empty( $to ) ) {
		return false;
	}

	$fields = sjpta_enquiry_fields();
	$lines  = array();

	foreach ( $fields as $name => $field ) {
		$value = $values[ $name ] ?? '';

		if ( '' === $value ) {
			continue;
		}

		$lines[] = $field['label'] . ': ' . $value;
	}

	$lines[] = '';
	$lines[] = __( 'Form:', 'sjptheatrearts' ) . ' ' . sjpta_enquiry_type_label( $type ) . ( '' !== $subject ? ' (' . $subject . ')' : '' );

	if ( '' !== $source ) {
		$lines[] = __( 'Sent from:', 'sjptheatrearts' ) . ' ' . $source;
	}

	if ( $stored ) {
		$lines[] = __( 'Saved on the website at:', 'sjptheatrearts' ) . ' ' . admin_url( 'post.php?post=' . $stored . '&action=edit' );
	}

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

	if ( ! empty( $values['email'] ) && is_email( $values['email'] ) ) {
		$headers[] = sprintf( 'Reply-To: %s <%s>', $values['parent_name'] ?? '', $values['email'] );
	}

	$who = $values['parent_name'] ?? '';

	if ( '' === $who ) {
		$who = $values['email'] ?? '';
	}

	return wp_mail(
		$to,
		/* translators: 1: form type, 2: sender name. */
		sprintf( __( '%1$s from %2$s', 'sjptheatrearts' ), sjpta_enquiry_type_label( $type ), $who ),
		implode( "\n", $lines ),
		$headers
	);
}

/* ---------- Retention ---------- */

/**
 * Make sure the daily prune is scheduled.
 *
 * @return void
 */
function sjpta_enquiry_schedule_prune(): void {
	if ( ! wp_next_scheduled( SJPTA_ENQUIRY_CRON ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', SJPTA_ENQUIRY_CRON );
	}
}
add_action( 'init', 'sjpta_enquiry_schedule_prune' );

/**
 * Stop the prune when the theme is switched away.
 *
 * @return void
 */
function sjpta_enquiry_unschedule_prune(): void {
	$stamp = wp_next_scheduled( SJPTA_ENQUIRY_CRON );

	if ( $stamp ) {
		wp_unschedule_event( $stamp, SJPTA_ENQUIRY_CRON );
	}
}
add_action( 'switch_theme', 'sjpta_enquiry_unschedule_prune' );

/**
 * The enquiries older than the retention period.
 *
 * @param int $limit How many ids to return at most.
 *
 * @return array<int,int>
 */
function sjpta_enquiry_expired_ids( int $limit = 200 ): array {
	$days = sjpta_enquiry_settings()['retention_days'];

	if ( $days <= 0 ) {
		return array();
	}

	$ids = get_posts(
		array(
			'post_type'        => SJPTA_ENQUIRY_POST_TYPE,
			'post_status'      => 'any',
			'posts_per_page'   => $limit,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'date_query'       => array(
				array(
					'column' => 'post_date_gmt',
					'before' => gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS ),
				),
			),
		)
	);

	return array_map( 'intval', (array) $ids );
}

/**
 * Delete enquiries past their retention period.
 *
 * Runs daily. Deletes outright rather than trashing: the point of a retention
 * period is that the data is gone, and a trash bin that keeps it for another
 * thirty days would quietly break the promise.
 *
 * @return int How many were removed.
 */
function sjpta_enquiry_prune(): int {
	$count = 0;

	foreach ( sjpta_enquiry_expired_ids() as $id ) {
		if ( wp_delete_post( $id, true ) ) {
			++$count;
		}
	}

	return $count;
}
add_action( SJPTA_ENQUIRY_CRON, 'sjpta_enquiry_prune' );
