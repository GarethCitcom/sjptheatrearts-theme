<?php
/**
 * The enquiry form itself.
 *
 * Rendered by sjpta_enquiry_form(), which any block can call. Born To Be and the
 * class pages use it today; the Join page and the homepage join teaser use the
 * same function in phase 7. There is only ever one form to keep working.
 *
 * The three designs ask for three different *shapes* of that one form, which is
 * what `variant` is for. Born To Be picks the class from a menu and offers a
 * message box; a class page already knows which class it is, so it shows the
 * chosen class with a link to change it, and swaps the message box for the
 * consent line. Join adds more again in phase 7. Same fields, same handler, same
 * validation underneath: only the layout and the wording differ.
 *
 * Works with JavaScript off: a plain POST back to the same URL, handled in
 * inc/enquiry.php before any output, then a redirect so a refresh cannot send
 * the enquiry twice.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * How each design arranges the fields.
 *
 * `rows` lists the fields in visual order, grouped into rows. `cols` names the
 * row's column template where it is not simply even; the class page's second row
 * is a narrow age box beside two full-width ones, exactly as drawn.
 *
 * `overrides` re-labels a field for its context without forking the field list.
 * A class page says "Student's name" because an adult tap class has no child in
 * it; Born To Be says "Child's name" because its two classes are for children.
 * Both post the same `child_name`, so the handler and the stored enquiry do not
 * care which page they came from.
 *
 * @param string $variant Layout name.
 *
 * @return array<string,mixed>
 */
function sjpta_enquiry_layout( string $variant ): array {
	$layouts = array(

		/* Born To Be: pick a class from the menu, and room to explain. */
		'stack'   => array(
			'rows'      => array(
				array( 'fields' => array( 'parent_name', 'child_name' ) ),
				array( 'fields' => array( 'child_age', 'class_want' ) ),
				array( 'fields' => array( 'email', 'phone' ) ),
				array( 'fields' => array( 'message' ) ),
			),
			'overrides' => array(),
			'consent'   => 'note',
			'optional'  => true,
		),

		/* A class page: the class is already known, so it is shown, not chosen. */
		'class'   => array(
			'rows'      => array(
				array( 'fields' => array( 'parent_name', 'child_name' ) ),
				array(
					'fields' => array( 'child_age', 'email', 'phone' ),
					'cols'   => 'age',
				),
			),
			'overrides' => array(
				'parent_name' => array( 'placeholder' => __( 'Parent or carer', 'sjptheatrearts' ) ),
				'child_name'  => array(
					'label'       => __( "Student's name", 'sjptheatrearts' ),
					'placeholder' => __( 'Who is the class for?', 'sjptheatrearts' ),
				),
				'child_age'   => array(
					'label'       => __( 'Age', 'sjptheatrearts' ),
					'placeholder' => __( 'e.g. 9', 'sjptheatrearts' ),
				),
				'email'       => array( 'placeholder' => __( 'you@example.com', 'sjptheatrearts' ) ),
				'phone'       => array( 'placeholder' => __( 'Mobile number', 'sjptheatrearts' ) ),
			),
			'consent'   => 'check',

			/*
			 * No visible "(optional)" here, as the design draws it. Five short
			 * fields of which three are optional means three markers, which reads
			 * as clutter rather than as help. The `required` attribute still says
			 * so to assistive technology, and every field has a placeholder
			 * showing what belongs in it.
			 */
			'optional'  => false,
		),

		/*
		 * The Join page: the longest of the four, because it is the one place
		 * where a parent who does not yet know what they want is trying to say
		 * so. The extra questions are all optional, and "Not sure yet" is a real
		 * first answer rather than an empty state.
		 */
		'join'    => array(
			'rows'      => array(
				array( 'heading' => __( 'About you', 'sjptheatrearts' ) ),
				array( 'fields' => array( 'parent_name', 'email' ) ),
				array( 'fields' => array( 'phone' ) ),
				array( 'heading' => __( 'About the student', 'sjptheatrearts' ) ),
				array( 'fields' => array( 'child_name', 'child_age' ) ),
				array( 'fields' => array( 'interest' ) ),
				array( 'fields' => array( 'experience', 'class_want' ) ),
				array( 'fields' => array( 'days' ) ),
				array( 'fields' => array( 'message' ) ),
			),
			'overrides' => array(
				'parent_name' => array( 'placeholder' => __( 'Parent or carer', 'sjptheatrearts' ) ),
				'email'       => array( 'placeholder' => __( 'you@example.com', 'sjptheatrearts' ) ),
				'phone'       => array( 'placeholder' => __( 'Mobile number', 'sjptheatrearts' ) ),
				'child_name'  => array(
					'label'       => __( "Student's name", 'sjptheatrearts' ),
					'placeholder' => __( 'Who is the class for?', 'sjptheatrearts' ),
				),
				'child_age'   => array(
					'label'       => __( 'Age', 'sjptheatrearts' ),
					'placeholder' => __( 'e.g. 7', 'sjptheatrearts' ),
				),
				'class_want'  => array( 'label' => __( 'Class of interest', 'sjptheatrearts' ) ),
				'message'     => array(
					'label'       => __( 'Anything we should know?', 'sjptheatrearts' ),
					'placeholder' => __( 'For example: they are shy in new groups, or a sibling already attends.', 'sjptheatrearts' ),
				),
			),
			'consent'   => 'check',
			'optional'  => true,
		),

		/*
		 * Contact: anything that is not an enrolment. No child fields at all,
		 * because half these messages are from adults asking about their own
		 * wedding dance or a party, and asking for a student's age would be an
		 * odd first question.
		 */
		'contact' => array(
			'rows'      => array(
				array( 'fields' => array( 'parent_name', 'email' ) ),
				array( 'fields' => array( 'phone', 'topic' ) ),
				array( 'fields' => array( 'message' ) ),
			),
			'overrides' => array(
				'parent_name' => array(
					'label'       => __( 'Your name', 'sjptheatrearts' ),
					'placeholder' => __( 'First and last name', 'sjptheatrearts' ),
				),
				'email'       => array( 'placeholder' => __( 'you@example.com', 'sjptheatrearts' ) ),
				'phone'       => array( 'placeholder' => __( 'Mobile number', 'sjptheatrearts' ) ),
				'message'     => array(
					'label'       => __( 'Your message', 'sjptheatrearts' ),
					'placeholder' => __( 'Tell us what you need and we will come back to you.', 'sjptheatrearts' ),
				),
			),
			'consent'   => 'check',
			'optional'  => true,
		),
	);

	return $layouts[ $variant ] ?? $layouts['stack'];
}

/**
 * The consent line, with the privacy notice linked if there is one to link to.
 *
 * @param string $variant Layout name, so Contact can ask the right question.
 *
 * @return string Escaped markup.
 */
function sjpta_enquiry_consent_line( string $variant = '' ): string {
	$url  = sjpta_privacy_url();
	$name = __( 'privacy notice', 'sjptheatrearts' );

	$notice = '' !== $url
		? '<a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a>'
		: esc_html( $name );

	/*
	 * Contact asks for permission to reply, not permission to market. Half these
	 * messages are about a wedding dance or a party, and asking those people to
	 * agree to hear about children's classes is asking the wrong question.
	 */
	if ( 'contact' === $variant ) {
		return sprintf(
			/* translators: %s: the words "privacy notice", linked once the privacy page is published. */
			esc_html__( 'I am happy for SJP Theatre Arts to reply to this message. See our %s.', 'sjptheatrearts' ),
			$notice
		);
	}

	return sprintf(
		/* translators: %s: the words "privacy notice", linked once the privacy page is published. */
		esc_html__( 'I am happy for SJP Theatre Arts to contact me about classes. See our %s.', 'sjptheatrearts' ),
		$notice
	);
}

/**
 * Render the enquiry form, or its sent state.
 *
 * Accepted keys:
 *
 * - `variant`   Layout name, see sjpta_enquiry_layout(). Default `stack`.
 * - `route`     Routing key (`sjp`, `lottie`, `madison`), used to name who replies.
 * - `recipient` Address enquiries go to. Falls back to the site contact email.
 * - `subject`   Names the form in the email subject and the admin list.
 * - `classes`   Options for the "which class" menu. An empty list hides it.
 * - `chosen`    Class this form is already about: `value`, `url`, `link`. Shown
 *               instead of the menu, and posted as a hidden field.
 * - `heading`   Heading above the form.
 * - `intro`     Line under the heading.
 * - `submit`    Button label.
 * - `consent`   Small print under the button, on variants that use small print.
 * - `sent_head` Heading of the sent state.
 * - `sent_text` Body of the sent state.
 * - `anchor`    Section id to return to after sending.
 *
 * @param array<string,mixed> $args Options, as listed above.
 *
 * @return void
 */
function sjpta_enquiry_form( array $args = array() ): void {
	$defaults = array(
		'variant'   => 'stack',
		'route'     => '',
		'recipient' => '',
		'subject'   => __( 'Website enquiry', 'sjptheatrearts' ),
		'classes'   => array(),
		'chosen'    => array(),
		'heading'   => '',
		'intro'     => '',
		'submit'    => __( 'Send enquiry', 'sjptheatrearts' ),
		'consent'   => __( 'We use your details only to answer this enquiry. Nothing is shared with anyone else.', 'sjptheatrearts' ),
		'sent_head' => __( 'Thank you, that is on its way.', 'sjptheatrearts' ),
		'sent_text' => __( 'We answer every message ourselves, so it may take a day or two. If it is urgent, please ring us.', 'sjptheatrearts' ),
		'anchor'    => 'enquire',
	);

	$args = wp_parse_args( $args, $defaults );

	/*
	 * An unfilled ACF field arrives as an empty string, which wp_parse_args()
	 * treats as a real value. These four have to keep their defaults, or the
	 * button loses its label and a parent gets no confirmation that anything
	 * happened.
	 */
	foreach ( array( 'submit', 'consent', 'sent_head', 'sent_text' ) as $sjpta_key ) {
		if ( ! is_string( $args[ $sjpta_key ] ) || '' === trim( $args[ $sjpta_key ] ) ) {
			$args[ $sjpta_key ] = $defaults[ $sjpta_key ];
		}
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a redirect flag, not acting on it.
	$sent = isset( $_GET['enquiry'] ) && 'sent' === $_GET['enquiry'];

	/*
	 * The confirmation names whoever will actually reply, where the site knows.
	 * A parent who has just handed over their child's details is owed something
	 * better than "we"; the name comes from a setting, so it can never be a
	 * guess, and the sentence falls back to "we" when nobody has set one.
	 */
	if ( $sent && ! empty( $args['route'] ) ) {
		$sjpta_who = sjpta_enquiry_responder( (string) $args['route'] );

		if ( '' !== $sjpta_who && $args['sent_text'] === $defaults['sent_text'] ) {
			$args['sent_text'] = sprintf(
				/* translators: %s: the name of the person who answers enquiries. */
				__( '%s answers every enquiry herself, so it may take a day or two. If it is urgent, please ring us.', 'sjptheatrearts' ),
				$sjpta_who
			);
		}
	}

	if ( $sent ) {
		?>
		<div class="sjpta-form__sent" role="status">
			<span class="sjpta-form__senticon" aria-hidden="true">
				<?php echo sjpta_icon( 'check', 22, 'var(--sjpta-accent)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
			</span>
			<h3 class="sjpta-form__senthead"><?php echo esc_html( (string) $args['sent_head'] ); ?></h3>
			<p class="sjpta-form__senttext"><?php echo esc_html( (string) $args['sent_text'] ); ?></p>
		</div>
		<?php
		return;
	}

	$variant = is_string( $args['variant'] ) ? $args['variant'] : 'stack';
	$layout  = sjpta_enquiry_layout( $variant );
	$fields  = sjpta_enquiry_fields();
	$chosen  = is_array( $args['chosen'] ) ? $args['chosen'] : array();

	$errors = isset( $GLOBALS['sjpta_enquiry_errors'] ) && is_array( $GLOBALS['sjpta_enquiry_errors'] )
		? $GLOBALS['sjpta_enquiry_errors']
		: array();
	$values = isset( $GLOBALS['sjpta_enquiry_values'] ) && is_array( $GLOBALS['sjpta_enquiry_values'] )
		? $GLOBALS['sjpta_enquiry_values']
		: array();

	$recipient = sanitize_email( (string) $args['recipient'] );

	if ( '' === $recipient || ! in_array( $recipient, sjpta_enquiry_allowed_recipients(), true ) ) {
		$recipient = sjpta_enquiry_default_recipient();
	}

	$stamp = (string) time();
	?>
	<form class="sjpta-form sjpta-form--<?php echo esc_attr( $variant ); ?>" method="post" action="<?php echo esc_url( get_permalink() . '#' . $args['anchor'] ); ?>" novalidate>

		<?php if ( '' !== $args['heading'] ) : ?>
			<h3 class="sjpta-form__heading"><?php echo esc_html( (string) $args['heading'] ); ?></h3>
		<?php endif; ?>

		<?php if ( '' !== $args['intro'] ) : ?>
			<p class="sjpta-form__intro"><?php echo esc_html( (string) $args['intro'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $errors ) ) : ?>
			<?php
			/*
			 * The summary comes first so it is read before the fields, and every
			 * entry links to the input it is about. No colour-only signalling:
			 * each message is a sentence.
			 */
			?>
			<div class="sjpta-form__errors" role="alert">
				<p class="sjpta-form__errorhead"><?php esc_html_e( 'We could not send that. Please check:', 'sjptheatrearts' ); ?></p>
				<ul>
					<?php foreach ( $errors as $sjpta_name => $sjpta_message ) : ?>
						<li><a href="#sjpta-<?php echo esc_attr( $sjpta_name ); ?>"><?php echo esc_html( $sjpta_message ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $chosen['value'] ) ) : ?>
			<?php
			/*
			 * The class is settled, so it is stated rather than asked. "Change" is
			 * a real link to the class list: no JavaScript decides anything here,
			 * and the hidden field below carries the answer the menu would have.
			 */
			?>
			<div class="sjpta-form__chosen">
				<div class="sjpta-form__chosentext">
					<span class="sjpta-form__chosenlabel"><?php esc_html_e( 'Enquiring about', 'sjptheatrearts' ); ?></span>
					<span class="sjpta-form__chosenvalue"><?php echo esc_html( (string) $chosen['value'] ); ?></span>
				</div>

				<?php if ( ! empty( $chosen['url'] ) ) : ?>
					<a class="sjpta-form__change" href="<?php echo esc_url( (string) $chosen['url'] ); ?>">
						<?php
						echo esc_html(
							! empty( $chosen['link'] ) ? (string) $chosen['link'] : __( 'Change', 'sjptheatrearts' )
						);
						?>
						<span class="screen-reader-text">
							<?php
							printf(
								/* translators: %s: class name. */
								esc_html__( 'the class you are enquiring about, currently %s', 'sjptheatrearts' ),
								esc_html( (string) $chosen['value'] )
							);
							?>
						</span>
					</a>
				<?php endif; ?>
			</div>

			<input type="hidden" name="class_want" value="<?php echo esc_attr( (string) $chosen['value'] ); ?>">
		<?php endif; ?>

		<?php foreach ( $layout['rows'] as $sjpta_row ) : ?>
			<?php
			/*
			 * A row can be a heading instead of fields. The Join form is long
			 * enough that "About you" and "About the student" are the difference
			 * between a form and a wall, and they are real headings so anyone
			 * moving through the page by heading meets the same two groups.
			 */
			if ( isset( $sjpta_row['heading'] ) ) {
				printf(
					'<h4 class="sjpta-form__group">%s</h4>',
					esc_html( (string) $sjpta_row['heading'] )
				);
				continue;
			}

			$sjpta_names = array();

			foreach ( $sjpta_row['fields'] as $sjpta_name ) {
				if ( ! isset( $fields[ $sjpta_name ] ) ) {
					continue;
				}

				/*
				 * The class menu stands down when there is nothing to choose
				 * between. Only that one: every other select carries its own
				 * fixed list and does not depend on what the page passed in.
				 */
				if ( 'class_want' === $sjpta_name && empty( $args['classes'] ) ) {
					continue;
				}

				$sjpta_names[] = $sjpta_name;
			}

			if ( empty( $sjpta_names ) ) {
				continue;
			}

			$sjpta_cols = $sjpta_row['cols'] ?? (string) count( $sjpta_names );
			?>
			<div class="sjpta-form__row sjpta-form__row--<?php echo esc_attr( $sjpta_cols ); ?>">
				<?php foreach ( $sjpta_names as $sjpta_name ) : ?>
					<?php
					$sjpta_field = array_merge(
						$fields[ $sjpta_name ],
						$layout['overrides'][ $sjpta_name ] ?? array()
					);

					$sjpta_id      = 'sjpta-' . $sjpta_name;
					$sjpta_error   = $errors[ $sjpta_name ] ?? '';
					$sjpta_value   = $values[ $sjpta_name ] ?? '';
					$sjpta_classes = 'sjpta-form__field';

					if ( '' !== $sjpta_error ) {
						$sjpta_classes .= ' has-error';
					}

					$sjpta_place = isset( $sjpta_field['placeholder'] )
						? ' placeholder="' . esc_attr( (string) $sjpta_field['placeholder'] ) . '"'
						: '';

					$sjpta_described = '' !== $sjpta_error
						? ' aria-describedby="' . esc_attr( $sjpta_id . '-error' ) . '" aria-invalid="true"'
						: '';
					?>
					<?php
					$sjpta_group = ( 'radio' === $sjpta_field['type'] || 'checkgroup' === $sjpta_field['type'] );

					/*
					 * A group of radios or checkboxes is a fieldset with a legend,
					 * not a label pointing at one input. Without that, a screen
					 * reader announces five unrelated pills and never says what
					 * question they answer.
					 */
					$sjpta_wrap = $sjpta_group ? 'fieldset' : 'div';
					?>
					<<?php echo esc_attr( $sjpta_wrap ); ?> class="<?php echo esc_attr( $sjpta_classes ); ?>">
						<?php if ( $sjpta_group ) : ?>
							<legend class="sjpta-form__label">
								<?php echo esc_html( $sjpta_field['label'] ); ?>
								<?php if ( ! empty( $layout['optional'] ) && empty( $sjpta_field['required'] ) ) : ?>
									<span class="sjpta-form__optional"><?php esc_html_e( '(optional)', 'sjptheatrearts' ); ?></span>
								<?php endif; ?>
							</legend>
						<?php else : ?>
							<label class="sjpta-form__label" for="<?php echo esc_attr( $sjpta_id ); ?>">
								<?php echo esc_html( $sjpta_field['label'] ); ?>
								<?php if ( ! empty( $layout['optional'] ) && empty( $sjpta_field['required'] ) ) : ?>
									<span class="sjpta-form__optional"><?php esc_html_e( '(optional)', 'sjptheatrearts' ); ?></span>
								<?php endif; ?>
							</label>
						<?php endif; ?>

						<?php if ( $sjpta_group ) : ?>
							<?php
							/*
							 * Real radios and checkboxes behind pill labels. The
							 * input is visually hidden rather than display:none, so
							 * it keeps its place in the tab order and its focus
							 * ring can be drawn on the pill.
							 */
							$sjpta_picked     = array_map( 'trim', explode( ',', (string) $sjpta_value ) );
							$sjpta_kind       = 'radio' === $sjpta_field['type'] ? 'radio' : 'checkbox';
							$sjpta_field_name = 'radio' === $sjpta_field['type'] ? $sjpta_name : $sjpta_name . '[]';
							?>
							<div class="sjpta-form__pills">
								<?php foreach ( sjpta_enquiry_choices( $sjpta_name, $sjpta_field, (array) $args['classes'] ) as $sjpta_i => $sjpta_choice ) : ?>
									<?php $sjpta_pill_id = $sjpta_id . '-' . $sjpta_i; ?>
									<label class="sjpta-form__pill" for="<?php echo esc_attr( $sjpta_pill_id ); ?>">
										<input
											type="<?php echo esc_attr( $sjpta_kind ); ?>"
											id="<?php echo esc_attr( $sjpta_pill_id ); ?>"
											name="<?php echo esc_attr( $sjpta_field_name ); ?>"
											value="<?php echo esc_attr( $sjpta_choice ); ?>"
											<?php checked( true, in_array( $sjpta_choice, $sjpta_picked, true ) ); ?>
										>
										<span><?php echo esc_html( $sjpta_choice ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>

						<?php elseif ( 'textarea' === $sjpta_field['type'] ) : ?>
							<textarea
								class="sjpta-form__input"
								id="<?php echo esc_attr( $sjpta_id ); ?>"
								name="<?php echo esc_attr( $sjpta_name ); ?>"
								rows="4"
								<?php echo $sjpta_place; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
								<?php echo $sjpta_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
							><?php echo esc_textarea( $sjpta_value ); ?></textarea>

						<?php elseif ( 'select' === $sjpta_field['type'] ) : ?>
							<select
								class="sjpta-form__input"
								id="<?php echo esc_attr( $sjpta_id ); ?>"
								name="<?php echo esc_attr( $sjpta_name ); ?>"
							>
								<?php foreach ( sjpta_enquiry_choices( $sjpta_name, $sjpta_field, (array) $args['classes'] ) as $sjpta_option ) : ?>
									<option value="<?php echo esc_attr( (string) $sjpta_option ); ?>" <?php selected( $sjpta_value, (string) $sjpta_option ); ?>>
										<?php echo esc_html( (string) $sjpta_option ); ?>
									</option>
								<?php endforeach; ?>
							</select>

						<?php else : ?>
							<input
								class="sjpta-form__input"
								type="<?php echo esc_attr( $sjpta_field['type'] ); ?>"
								id="<?php echo esc_attr( $sjpta_id ); ?>"
								name="<?php echo esc_attr( $sjpta_name ); ?>"
								value="<?php echo esc_attr( $sjpta_value ); ?>"
								<?php echo $sjpta_place; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
								<?php echo isset( $sjpta_field['autocomplete'] ) ? ' autocomplete="' . esc_attr( $sjpta_field['autocomplete'] ) . '"' : ''; ?>
								<?php echo ! empty( $sjpta_field['required'] ) ? ' required' : ''; ?>
								<?php echo $sjpta_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
							>
						<?php endif; ?>

						<?php
						/*
						 * Under the input, not above it. Above, the message pushed
						 * its own field down and the inputs in a row stopped
						 * lining up with each other, so one mistake made the whole
						 * row look broken. The error summary at the top of the form
						 * is what announces the problem; aria-describedby repeats
						 * it when focus reaches the field.
						 */
						?>
						<?php if ( '' !== $sjpta_error ) : ?>
							<span class="sjpta-form__error" id="<?php echo esc_attr( $sjpta_id . '-error' ); ?>"><?php echo esc_html( $sjpta_error ); ?></span>
						<?php endif; ?>
					</<?php echo esc_attr( $sjpta_wrap ); ?>>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

		<?php
		/*
		 * The honeypot. Moved off screen rather than display:none, and taken out
		 * of the tab order and the accessibility tree, so no keyboard or screen
		 * reader user can reach it while a form-filling bot still sees a field.
		 */
		?>
		<div class="sjpta-form__hp" aria-hidden="true">
			<label for="sjpta-website"><?php esc_html_e( 'Leave this field empty', 'sjptheatrearts' ); ?></label>
			<input type="text" id="sjpta-website" name="sjpta_website" tabindex="-1" autocomplete="off" value="">
		</div>

		<input type="hidden" name="sjpta_enquiry" value="1">
		<input type="hidden" name="sjpta_anchor" value="<?php echo esc_attr( (string) $args['anchor'] ); ?>">
		<input type="hidden" name="sjpta_to" value="<?php echo esc_attr( $recipient ); ?>">
		<input type="hidden" name="sjpta_subject" value="<?php echo esc_attr( (string) $args['subject'] ); ?>">
		<input type="hidden" name="sjpta_t" value="<?php echo esc_attr( $stamp ); ?>">
		<input type="hidden" name="sjpta_s" value="<?php echo esc_attr( sjpta_enquiry_signature( $stamp ) ); ?>">

		<?php if ( 'check' === $layout['consent'] ) : ?>
			<input type="hidden" name="sjpta_asked_consent_contact" value="1">

			<label class="sjpta-form__check" for="sjpta-consent_contact">
				<input
					type="checkbox"
					id="sjpta-consent_contact"
					name="consent_contact"
					value="1"
					<?php checked( __( 'Yes', 'sjptheatrearts' ), $values['consent_contact'] ?? '' ); ?>
				>
				<span><?php echo sjpta_enquiry_consent_line( $variant ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_enquiry_consent_line(). ?></span>
			</label>
		<?php endif; ?>

		<button class="sjpta-btn sjpta-btn--primary sjpta-form__submit" type="submit">
			<?php echo esc_html( (string) $args['submit'] ); ?>
		</button>

		<?php if ( 'note' === $layout['consent'] && '' !== $args['consent'] ) : ?>
			<p class="sjpta-form__consent"><?php echo esc_html( (string) $args['consent'] ); ?></p>
		<?php endif; ?>
	</form>
	<?php
}
