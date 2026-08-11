<?php
/**
 * Enquiry form: storage, handling and rendering.
 *
 * One implementation, used by every enquiry panel on the site. Each instance
 * names its own recipient, so Born To Be reaches Madison while the Join page and
 * the homepage reach SJ, without a second copy of any of this.
 *
 * Every enquiry is stored as well as emailed. Email is the part that fails
 * quietly: a misconfigured host, a spam filter, a full mailbox, and a parent's
 * message is gone with no record. The stored copy means it never is.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SJPTA_ENQUIRY_POST_TYPE = 'sjpta-enquiry';

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
 * The designs link the words "privacy notice" from the consent line. WordPress
 * returns an empty string until the privacy page is published, and this install
 * still has it as a draft, so the words render as plain text rather than as a
 * link to a 404. Publishing the page turns every consent line into a link with
 * no code change.
 *
 * @return string
 */
function sjpta_privacy_url(): string {
	return (string) get_privacy_policy_url();
}

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
 * Handle a submitted enquiry, before any output.
 *
 * Deliberately no WordPress nonce. A nonce printed into a page that a host caches
 * goes stale, and the failure lands on a parent who typed everything correctly
 * and gets told to try again. There is no authenticated action here to protect:
 * the risk is spam, which the honeypot and the timing check cover, and neither
 * expires with the cache.
 *
 * @return void
 */
function sjpta_handle_enquiry(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- see the docblock: this is an unauthenticated public form, protected by a signed timestamp instead.
	if ( empty( $_POST['sjpta_enquiry'] ) ) {
		return;
	}

	$posted = wp_unslash( $_POST );

	// Where to send the visitor back to, so the form is on screen when they land.
	$anchor = isset( $posted['sjpta_anchor'] ) ? (string) $posted['sjpta_anchor'] : 'enquire';

	/*
	 * Honeypot. A real browser never fills this in, because it is hidden from
	 * layout and removed from the accessibility tree. Answer a bot with the same
	 * success it would get from a real send, so it learns nothing.
	 */
	if ( ! empty( $posted['sjpta_website'] ) ) {
		sjpta_enquiry_redirect( $anchor );
	}

	$stamp     = isset( $posted['sjpta_t'] ) ? (string) $posted['sjpta_t'] : '';
	$signature = isset( $posted['sjpta_s'] ) ? (string) $posted['sjpta_s'] : '';

	/*
	 * Only a minimum age is checked, never a maximum: on a cached page the
	 * timestamp is as old as the cache entry, and rejecting that would reject
	 * every real visitor.
	 */
	$signed_ok  = '' !== $stamp && hash_equals( sjpta_enquiry_signature( $stamp ), $signature );
	$old_enough = $signed_ok && ( time() - (int) $stamp ) >= SJPTA_ENQUIRY_MIN_SECONDS;

	if ( ! $old_enough ) {
		sjpta_enquiry_redirect( $anchor );
	}

	$fields = sjpta_enquiry_fields();
	$values = array();
	$errors = array();

	foreach ( $fields as $name => $field ) {
		$raw = trim( isset( $posted[ $name ] ) ? (string) $posted[ $name ] : '' );

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

	if ( ! empty( $errors ) ) {
		$GLOBALS['sjpta_enquiry_errors'] = $errors;
		$GLOBALS['sjpta_enquiry_values'] = $values;
		return;
	}

	$recipient = isset( $posted['sjpta_to'] ) ? sanitize_email( (string) $posted['sjpta_to'] ) : '';
	$subject   = isset( $posted['sjpta_subject'] ) ? sanitize_text_field( (string) $posted['sjpta_subject'] ) : '';

	/*
	 * The recipient travels with the form, but is verified against the addresses
	 * the site actually configures. Without that check the hidden field is an
	 * open relay: anyone could post to this endpoint and have WordPress send mail
	 * anywhere they liked.
	 */
	if ( ! in_array( $recipient, sjpta_enquiry_allowed_recipients(), true ) ) {
		$recipient = sjpta_enquiry_default_recipient();
	}

	$stored = sjpta_store_enquiry( $values, $recipient, $subject );
	sjpta_send_enquiry( $values, $recipient, $subject, $stored );

	sjpta_enquiry_redirect( $anchor );
	// phpcs:enable WordPress.Security.NonceVerification.Missing
}
add_action( 'template_redirect', 'sjpta_handle_enquiry' );

/**
 * Every address this site is allowed to send an enquiry to.
 *
 * @return array<int,string>
 */
function sjpta_enquiry_allowed_recipients(): array {
	/*
	 * The contact address carries the same fallback the footer uses. ACF returns
	 * an empty string for an options field nobody has saved yet, rather than the
	 * field's default, so without this the chain fell through to admin_email,
	 * which on this install is the booking provider's support desk. Parents'
	 * enquiries going to a third party is not a failure worth risking.
	 */
	$addresses = array(
		sjpta_setting( 'contact_email', 'sjptheatrearts@yahoo.com' ),
		sjpta_setting( 'class_enquiry_email', '' ),
		sjpta_setting( 'btb_enquiry_email', '' ),
		(string) get_option( 'admin_email', '' ),
	);

	return array_values( array_filter( array_map( 'sanitize_email', $addresses ) ) );
}

/**
 * Turn a routing key into an address.
 *
 * The brief routes by enquiry type: class and enrolment enquiries reach Lottie,
 * Born To Be reaches Madison, everything else reaches the general inbox. Held in
 * one place so a block names a *role* rather than an address, and so an unset
 * address falls back to the contact email instead of losing the enquiry.
 *
 * @param string $key One of `sjp`, `lottie`, `madison`.
 *
 * @return string
 */
function sjpta_enquiry_recipient( string $key ): string {
	$map = array(
		'lottie'  => sjpta_setting( 'class_enquiry_email', '' ),
		'madison' => sjpta_setting( 'btb_enquiry_email', '' ),
		'sjp'     => sjpta_setting( 'contact_email', 'sjptheatrearts@yahoo.com' ),
	);

	$address = sanitize_email( $map[ $key ] ?? '' );

	return '' !== $address ? $address : sjpta_enquiry_default_recipient();
}

/**
 * Who a sender is told will reply.
 *
 * A confirmation that names a person is worth more than one that says "we", but
 * only if the name is right, so it comes from a setting and the sentence falls
 * back to "we" when nobody has filled it in.
 *
 * @param string $key Routing key.
 *
 * @return string Empty when there is no name to give.
 */
function sjpta_enquiry_responder( string $key ): string {
	if ( 'lottie' === $key ) {
		/*
		 * The explicit default matters: ACF hands back an empty string for an
		 * options field nobody has saved, not the field's own default, so
		 * without it the confirmation silently drops back to "we". The name
		 * itself is not invented; the Join and Contact copy both say Lottie
		 * answers enquiries, and clearing the setting is how you get "we".
		 */
		return sjpta_setting( 'class_enquiry_name', 'Lottie' );
	}

	return '';
}

/**
 * Where an enquiry goes when no valid recipient was named.
 *
 * @return string
 */
function sjpta_enquiry_default_recipient(): string {
	$allowed = sjpta_enquiry_allowed_recipients();

	return $allowed ? $allowed[0] : (string) get_option( 'admin_email', '' );
}

/**
 * Send the visitor back to the form, showing its sent state.
 *
 * Post, redirect, get: without the redirect a refresh resubmits the enquiry, and
 * parents refresh.
 *
 * @param string $anchor Section id to return to, so the form is on screen.
 *
 * @return void
 */
function sjpta_enquiry_redirect( string $anchor = 'enquire' ): void {
	$anchor = sanitize_key( $anchor );
	$url    = add_query_arg( 'enquiry', 'sent', get_permalink() );

	if ( '' !== $anchor ) {
		$url .= '#' . $anchor;
	}

	wp_safe_redirect( $url, 303 );
	exit;
}

/**
 * Store an enquiry.
 *
 * @param array<string,string> $values    Sanitised field values.
 * @param string               $recipient Address it was sent to.
 * @param string               $subject   Which form it came from.
 *
 * @return int Post id, or 0 on failure.
 */
function sjpta_store_enquiry( array $values, string $recipient, string $subject ): int {
	$who = $values['parent_name'] ?? '';

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

	update_post_meta( $id, '_sjpta_sent_to', $recipient );
	update_post_meta( $id, '_sjpta_form', $subject );

	return (int) $id;
}

/**
 * Email an enquiry.
 *
 * From stays on this domain and the visitor's address goes in Reply-To. Sending
 * as the visitor would fail SPF and DMARC at most hosts, and the message would
 * be filed as spam or refused outright.
 *
 * @param array<string,string> $values    Sanitised field values.
 * @param string               $recipient Address to send to.
 * @param string               $subject   Which form it came from.
 * @param int                  $stored    Post id of the stored copy, 0 if none.
 *
 * @return bool
 */
function sjpta_send_enquiry( array $values, string $recipient, string $subject, int $stored ): bool {
	if ( '' === $recipient ) {
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

	if ( $stored ) {
		$lines[] = '';
		$lines[] = __( 'Saved on the website at:', 'sjptheatrearts' ) . ' ' . get_edit_post_link( $stored, 'raw' );
	}

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

	if ( ! empty( $values['email'] ) && is_email( $values['email'] ) ) {
		$headers[] = sprintf( 'Reply-To: %s <%s>', $values['parent_name'] ?? '', $values['email'] );
	}

	$heading = '' !== $subject ? $subject : __( 'Website enquiry', 'sjptheatrearts' );

	return wp_mail(
		$recipient,
		/* translators: 1: form name, 2: sender name. */
		sprintf( __( '%1$s from %2$s', 'sjptheatrearts' ), $heading, $values['parent_name'] ?? '' ),
		implode( "\n", $lines ),
		$headers
	);
}

/**
 * Show the enquiry's details on its admin screen.
 *
 * The post type supports a title only, so without this an editor opening an
 * enquiry sees a name and nothing else.
 *
 * @return void
 */
function sjpta_enquiry_meta_box(): void {
	add_meta_box(
		'sjpta-enquiry-detail',
		__( 'Enquiry', 'sjptheatrearts' ),
		'sjpta_render_enquiry_meta_box',
		SJPTA_ENQUIRY_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'sjpta_enquiry_meta_box' );

/**
 * Render the enquiry detail box.
 *
 * @param WP_Post $post The enquiry.
 *
 * @return void
 */
function sjpta_render_enquiry_meta_box( WP_Post $post ): void {
	$fields = sjpta_enquiry_fields();

	echo '<table class="widefat striped"><tbody>';

	foreach ( $fields as $name => $field ) {
		$value = (string) get_post_meta( $post->ID, '_sjpta_' . $name, true );

		if ( '' === $value ) {
			continue;
		}

		if ( 'email' === $field['type'] ) {
			$value = sprintf( '<a href="%1$s">%2$s</a>', esc_url( 'mailto:' . $value ), esc_html( $value ) );
		} elseif ( 'tel' === $field['type'] ) {
			$value = sprintf( '<a href="%1$s">%2$s</a>', esc_url( 'tel:' . preg_replace( '/\s+/', '', $value ) ), esc_html( $value ) );
		} else {
			$value = nl2br( esc_html( $value ) );
		}

		printf(
			'<tr><th scope="row" style="width:220px">%1$s</th><td>%2$s</td></tr>',
			esc_html( $field['label'] ),
			wp_kses_post( $value )
		);
	}

	printf(
		'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
		esc_html__( 'Emailed to', 'sjptheatrearts' ),
		esc_html( (string) get_post_meta( $post->ID, '_sjpta_sent_to', true ) )
	);

	printf(
		'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
		esc_html__( 'Received', 'sjptheatrearts' ),
		esc_html( get_the_date( '', $post ) . ', ' . get_the_time( '', $post ) )
	);

	echo '</tbody></table>';
}

/**
 * List the useful columns on the enquiries screen.
 *
 * @param array<string,string> $columns Existing columns.
 *
 * @return array<string,string>
 */
function sjpta_enquiry_columns( $columns ): array {
	unset( $columns['date'] );

	return array_merge(
		(array) $columns,
		array(
			'sjpta_child' => __( 'Child', 'sjptheatrearts' ),
			'sjpta_class' => __( 'Class', 'sjptheatrearts' ),
			'sjpta_email' => __( 'Email', 'sjptheatrearts' ),
			'sjpta_form'  => __( 'Form', 'sjptheatrearts' ),
			'date'        => __( 'Received', 'sjptheatrearts' ),
		)
	);
}
add_filter( 'manage_' . SJPTA_ENQUIRY_POST_TYPE . '_posts_columns', 'sjpta_enquiry_columns' );

/**
 * Fill the enquiry columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Enquiry id.
 *
 * @return void
 */
function sjpta_enquiry_column( $column, $post_id ): void {
	$map = array(
		'sjpta_child' => '_sjpta_child_name',
		'sjpta_class' => '_sjpta_class_want',
		'sjpta_email' => '_sjpta_email',
		'sjpta_form'  => '_sjpta_form',
	);

	if ( ! isset( $map[ $column ] ) ) {
		return;
	}

	echo esc_html( (string) get_post_meta( (int) $post_id, $map[ $column ], true ) );
}
add_action( 'manage_' . SJPTA_ENQUIRY_POST_TYPE . '_posts_custom_column', 'sjpta_enquiry_column', 10, 2 );
