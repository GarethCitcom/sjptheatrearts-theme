<?php
/**
 * Enquiries in the dashboard.
 *
 * The Enquiries menu is the one place to deal with everything the site's forms
 * bring in: the list, filtered by form and by whether it has been dealt with,
 * the detail of each one, and a Settings screen that says which addresses each
 * kind of form reaches and how long the stored copies are kept.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SJPTA_ENQUIRY_SETTINGS_SLUG = 'sjpta-enquiry-settings';

/**
 * The states an enquiry moves through.
 *
 * @return array<string,string>
 */
function sjpta_enquiry_statuses(): array {
	return array(
		'new'  => __( 'New', 'sjptheatrearts' ),
		'done' => __( 'Dealt with', 'sjptheatrearts' ),
		'spam' => __( 'Spam', 'sjptheatrearts' ),
	);
}

/**
 * The URL of the list screen.
 *
 * @param array<string,string> $args Extra query arguments.
 *
 * @return string
 */
function sjpta_enquiry_list_url( array $args = array() ): string {
	return add_query_arg( array_merge( array( 'post_type' => SJPTA_ENQUIRY_POST_TYPE ), $args ), admin_url( 'edit.php' ) );
}

/**
 * The URL of the settings screen.
 *
 * @return string
 */
function sjpta_enquiry_settings_url(): string {
	return sjpta_enquiry_list_url( array( 'page' => SJPTA_ENQUIRY_SETTINGS_SLUG ) );
}

/* ---------- Settings screen ---------- */

/**
 * Register the option and the screen.
 *
 * @return void
 */
function sjpta_enquiry_register_settings(): void {
	register_setting(
		'sjpta_enquiry_settings_group',
		SJPTA_ENQUIRY_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'sjpta_enquiry_sanitize_settings',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'sjpta_enquiry_register_settings' );

/**
 * Add the Settings entry under Enquiries.
 *
 * @return void
 */
function sjpta_enquiry_settings_menu(): void {
	add_submenu_page(
		'edit.php?post_type=' . SJPTA_ENQUIRY_POST_TYPE,
		__( 'Enquiry settings', 'sjptheatrearts' ),
		__( 'Settings', 'sjptheatrearts' ),
		'manage_options',
		SJPTA_ENQUIRY_SETTINGS_SLUG,
		'sjpta_enquiry_render_settings'
	);
}
add_action( 'admin_menu', 'sjpta_enquiry_settings_menu' );

/**
 * Clean the settings on save.
 *
 * Every address is checked; anything that is not an email address is dropped
 * and reported, rather than silently kept and silently failing to deliver.
 *
 * @param mixed $input Raw option value.
 *
 * @return array<string,mixed>
 */
function sjpta_enquiry_sanitize_settings( $input ): array {
	$input = is_array( $input ) ? $input : array();
	$out   = array(
		'types'          => array(),
		'retention_days' => 365,
	);

	foreach ( sjpta_enquiry_types() as $type => $def ) {
		$row = isset( $input['types'][ $type ] ) && is_array( $input['types'][ $type ] ) ? $input['types'][ $type ] : array();
		$raw = isset( $row['emails'] ) ? (string) $row['emails'] : '';

		$clean   = sjpta_enquiry_parse_emails( $raw );
		$offered = array_filter( array_map( 'trim', (array) preg_split( '/[\s,;]+/', $raw ) ) );
		$dropped = array_diff( array_map( 'strtolower', $offered ), array_map( 'strtolower', $clean ) );

		if ( ! empty( $dropped ) ) {
			add_settings_error(
				SJPTA_ENQUIRY_OPTION,
				'sjpta-bad-email-' . $type,
				sprintf(
					/* translators: 1: form type, 2: the addresses that were removed. */
					__( '%1$s: these were not valid email addresses and were removed: %2$s', 'sjptheatrearts' ),
					$def['label'],
					implode( ', ', $dropped )
				)
			);
		}

		$out['types'][ $type ] = array(
			'emails'    => implode( ', ', $clean ),
			'responder' => isset( $row['responder'] ) ? sanitize_text_field( (string) $row['responder'] ) : '',
		);
	}

	if ( isset( $input['retention_days'] ) ) {
		$out['retention_days'] = max( 0, min( 3650, (int) $input['retention_days'] ) );
	}

	/*
	 * Turnstile keys are opaque strings from Cloudflare; only whitespace and
	 * anything that is not a key character is stripped. Both or neither: one
	 * on its own is a misconfiguration, and saying so beats a widget that
	 * checks nothing.
	 */
	$site_key = isset( $input['turnstile_site_key'] ) ? preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $input['turnstile_site_key'] ) : '';
	$secret   = isset( $input['turnstile_secret'] ) ? preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $input['turnstile_secret'] ) : '';

	if ( ( '' === $site_key ) !== ( '' === $secret ) ) {
		add_settings_error(
			SJPTA_ENQUIRY_OPTION,
			'sjpta-turnstile-keys',
			__( 'Turnstile needs both the site key and the secret key. It stays off until both are filled in.', 'sjptheatrearts' )
		);
	}

	$out['turnstile_site_key'] = (string) $site_key;
	$out['turnstile_secret']   = (string) $secret;
	$out['akismet']            = ! empty( $input['akismet'] );

	return $out;
}

/**
 * Render the settings screen.
 *
 * @return void
 */
function sjpta_enquiry_render_settings(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = sjpta_enquiry_settings();
	$spam     = sjpta_enquiry_spam_settings();
	$types    = sjpta_enquiry_types();
	$expired  = count( sjpta_enquiry_expired_ids( 1000 ) );
	$next     = wp_next_scheduled( SJPTA_ENQUIRY_CRON );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a redirect flag set by our own admin-post handler, not acting on it.
	$tested = isset( $_GET['sjpta_test'] ) ? sanitize_key( (string) $_GET['sjpta_test'] ) : '';
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- as above.
	$test_note = isset( $_GET['sjpta_note'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['sjpta_note'] ) ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Enquiry settings', 'sjptheatrearts' ); ?></h1>

		<?php settings_errors(); ?>

		<?php if ( 'sent' === $tested ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $test_note ); ?></p></div>
		<?php elseif ( 'failed' === $tested ) : ?>
			<div class="notice notice-error is-dismissible"><p><?php echo esc_html( $test_note ); ?></p></div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Every form on the website is saved here under Enquiries and emailed to the addresses below. Separate several addresses with commas.', 'sjptheatrearts' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sjpta_enquiry_settings_group' ); ?>

			<h2><?php esc_html_e( 'Who receives what', 'sjptheatrearts' ); ?></h2>

			<table class="widefat striped" style="max-width:960px">
				<thead>
					<tr>
						<th scope="col" style="width:200px"><?php esc_html_e( 'Form', 'sjptheatrearts' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Send to', 'sjptheatrearts' ); ?></th>
						<th scope="col" style="width:180px"><?php esc_html_e( 'Who replies', 'sjptheatrearts' ); ?></th>
						<th scope="col" style="width:120px"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $types as $sjpta_type => $sjpta_def ) : ?>
						<?php
						$sjpta_row      = $settings['types'][ $sjpta_type ];
						$sjpta_current  = sjpta_enquiry_recipients( $sjpta_type );
						$sjpta_field_id = 'sjpta-emails-' . $sjpta_type;
						$sjpta_resp_id  = 'sjpta-responder-' . $sjpta_type;
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $sjpta_field_id ); ?>"><strong><?php echo esc_html( $sjpta_def['label'] ); ?></strong></label>
								<p class="description"><?php echo esc_html( $sjpta_def['where'] ); ?></p>
							</th>
							<td>
								<input
									type="text"
									class="regular-text"
									style="width:100%"
									id="<?php echo esc_attr( $sjpta_field_id ); ?>"
									name="<?php echo esc_attr( SJPTA_ENQUIRY_OPTION . '[types][' . $sjpta_type . '][emails]' ); ?>"
									value="<?php echo esc_attr( $sjpta_row['emails'] ); ?>"
									placeholder="<?php echo esc_attr( implode( ', ', $sjpta_current ) ); ?>"
								>
								<p class="description">
									<?php if ( '' === trim( $sjpta_row['emails'] ) ) : ?>
										<?php if ( ! empty( $sjpta_current ) ) : ?>
											<?php
											printf(
												/* translators: %s: email addresses. */
												esc_html__( 'Nothing set here, so currently going to: %s', 'sjptheatrearts' ),
												'<code>' . esc_html( implode( ', ', $sjpta_current ) ) . '</code>'
											);
											?>
										<?php else : ?>
											<?php esc_html_e( 'Nothing set, so these are saved but not emailed.', 'sjptheatrearts' ); ?>
										<?php endif; ?>
									<?php endif; ?>
								</p>
							</td>
							<td>
								<label class="screen-reader-text" for="<?php echo esc_attr( $sjpta_resp_id ); ?>"><?php esc_html_e( 'Name shown in the confirmation', 'sjptheatrearts' ); ?></label>
								<input
									type="text"
									style="width:100%"
									id="<?php echo esc_attr( $sjpta_resp_id ); ?>"
									name="<?php echo esc_attr( SJPTA_ENQUIRY_OPTION . '[types][' . $sjpta_type . '][responder]' ); ?>"
									value="<?php echo esc_attr( $sjpta_row['responder'] ); ?>"
									placeholder="<?php echo esc_attr( sjpta_enquiry_responder( $sjpta_type ) ); ?>"
								>
							</td>
							<td>
								<?php if ( ! empty( $sjpta_current ) ) : ?>
									<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=sjpta_enquiry_test&type=' . $sjpta_type ), 'sjpta_enquiry_test_' . $sjpta_type ) ); ?>">
										<?php esc_html_e( 'Send test', 'sjptheatrearts' ); ?>
									</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p class="description" style="max-width:960px">
				<?php esc_html_e( '"Who replies" is the name used in the thank-you message ("Lottie answers every enquiry herself"). Leave it empty to say "we". "Send test" emails a short test message to the addresses shown, so you can confirm the site can actually deliver mail before relying on it.', 'sjptheatrearts' ); ?>
			</p>

			<h2><?php esc_html_e( 'Spam protection', 'sjptheatrearts' ); ?></h2>

			<p class="description" style="max-width:960px">
				<?php
				printf(
					/* translators: 1: number of days, 2: link to the spam list. */
					esc_html__( 'Every form is already checked with a hidden field, a timing test, a rate limit and content rules (non-Latin text, links, markup). Anything caught is kept for %1$s days under %2$s rather than deleted, emailed to nobody, and can be released with "Not spam". The two services below add stronger checks.', 'sjptheatrearts' ),
					esc_html( (string) SJPTA_ENQUIRY_SPAM_DAYS ),
					'<a href="' . esc_url( sjpta_enquiry_list_url( array( 'sjpta_status' => 'spam' ) ) ) . '">' . esc_html__( 'Enquiries, filtered to Spam', 'sjptheatrearts' ) . '</a>'
				);
				?>
			</p>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="sjpta-turnstile-site"><?php esc_html_e( 'Cloudflare Turnstile site key', 'sjptheatrearts' ); ?></label></th>
					<td>
						<input
							type="text"
							class="regular-text code"
							id="sjpta-turnstile-site"
							name="<?php echo esc_attr( SJPTA_ENQUIRY_OPTION . '[turnstile_site_key]' ); ?>"
							value="<?php echo esc_attr( $spam['turnstile_site_key'] ); ?>"
							autocomplete="off"
							spellcheck="false"
						>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sjpta-turnstile-secret"><?php esc_html_e( 'Cloudflare Turnstile secret key', 'sjptheatrearts' ); ?></label></th>
					<td>
						<input
							type="password"
							class="regular-text code"
							id="sjpta-turnstile-secret"
							name="<?php echo esc_attr( SJPTA_ENQUIRY_OPTION . '[turnstile_secret]' ); ?>"
							value="<?php echo esc_attr( $spam['turnstile_secret'] ); ?>"
							autocomplete="new-password"
							spellcheck="false"
						>
						<p class="description">
							<?php
							printf(
								/* translators: %s: link to the Cloudflare dashboard. */
								esc_html__( 'Free from %s: add a widget for this domain, choose "Managed", and paste the two keys here. Once both are set, every form is checked by Turnstile and a submission it does not verify is quarantined. Visitors are not shown a puzzle unless Cloudflare is unsure about them.', 'sjptheatrearts' ),
								'<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" rel="noopener">' . esc_html__( 'the Cloudflare dashboard', 'sjptheatrearts' ) . '</a>'
							);
							?>
							<?php if ( sjpta_enquiry_turnstile_enabled() ) : ?>
								<strong><?php esc_html_e( 'Turnstile is on.', 'sjptheatrearts' ); ?></strong>
							<?php else : ?>
								<strong><?php esc_html_e( 'Turnstile is off.', 'sjptheatrearts' ); ?></strong>
							<?php endif; ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Akismet', 'sjptheatrearts' ); ?></th>
					<td>
						<label for="sjpta-akismet">
							<input
								type="checkbox"
								id="sjpta-akismet"
								name="<?php echo esc_attr( SJPTA_ENQUIRY_OPTION . '[akismet]' ); ?>"
								value="1"
								<?php checked( $spam['akismet'] ); ?>
							>
							<?php esc_html_e( 'Also check submissions with Akismet', 'sjptheatrearts' ); ?>
						</label>
						<p class="description">
							<?php if ( sjpta_enquiry_akismet_available() ) : ?>
								<?php esc_html_e( 'The Akismet plugin is active and has a key, so this works as soon as it is ticked.', 'sjptheatrearts' ); ?>
							<?php else : ?>
								<?php esc_html_e( 'The Akismet plugin is not active or has no API key yet. Ticking this does nothing until it is; activate it under Plugins and add a key there.', 'sjptheatrearts' ); ?>
							<?php endif; ?>
						</p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'How long to keep them', 'sjptheatrearts' ); ?></h2>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="sjpta-retention"><?php esc_html_e( 'Retention period', 'sjptheatrearts' ); ?></label></th>
					<td>
						<input
							type="number"
							min="0"
							max="3650"
							step="1"
							id="sjpta-retention"
							name="<?php echo esc_attr( SJPTA_ENQUIRY_OPTION . '[retention_days]' ); ?>"
							value="<?php echo esc_attr( (string) $settings['retention_days'] ); ?>"
						>
						<?php esc_html_e( 'days', 'sjptheatrearts' ); ?>
						<p class="description">
							<?php esc_html_e( 'Stored enquiries older than this are deleted permanently once a day. Set 0 to keep them forever. The emails you have already received are not affected.', 'sjptheatrearts' ); ?>
						</p>
						<?php if ( $settings['retention_days'] > 0 ) : ?>
							<p class="description">
								<?php
								printf(
									/* translators: 1: number of enquiries, 2: date and time. */
									esc_html__( '%1$s currently past the retention period. Next clean-up: %2$s.', 'sjptheatrearts' ),
									esc_html( (string) $expired ),
									esc_html( $next ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $next ) : __( 'not scheduled', 'sjptheatrearts' ) )
								);
								?>
							</p>
						<?php endif; ?>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save settings', 'sjptheatrearts' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Send a test email to a type's addresses.
 *
 * Answers the question "is the site actually delivering mail?" without
 * anyone having to fill in a form on the front end and wait.
 *
 * @return void
 */
function sjpta_enquiry_send_test(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'sjptheatrearts' ) );
	}

	$type = isset( $_GET['type'] ) ? sanitize_key( (string) $_GET['type'] ) : '';

	check_admin_referer( 'sjpta_enquiry_test_' . $type );

	$to = sjpta_enquiry_recipients( $type );

	$failure = '';
	$catch   = static function ( $error ) use ( &$failure ): void {
		if ( $error instanceof WP_Error ) {
			$failure = $error->get_error_message();
		}
	};

	add_action( 'wp_mail_failed', $catch );

	$sent = ! empty( $to ) && wp_mail(
		$to,
		/* translators: %s: form type. */
		sprintf( __( 'Test: %s from the SJP website', 'sjptheatrearts' ), sjpta_enquiry_type_label( $type ) ),
		sprintf(
			/* translators: 1: form type, 2: site URL. */
			__( 'This is a test from the Enquiry settings screen. If you are reading it, %1$s messages sent from %2$s will reach this inbox.', 'sjptheatrearts' ),
			sjpta_enquiry_type_label( $type ),
			home_url( '/' )
		),
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);

	remove_action( 'wp_mail_failed', $catch );

	if ( $sent ) {
		$note = sprintf(
			/* translators: %s: email addresses. */
			__( 'Test email handed to the mail server for %s. If it does not arrive within a few minutes, check the spam folder, then the host\'s mail settings.', 'sjptheatrearts' ),
			implode( ', ', $to )
		);
	} else {
		$note = '' !== $failure
			/* translators: %s: error message. */
			? sprintf( __( 'The mail server refused it: %s', 'sjptheatrearts' ), $failure )
			: __( 'The site could not send the test email. The host\'s mail function returned an error without a message; an SMTP plugin is usually the fix.', 'sjptheatrearts' );
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'sjpta_test' => $sent ? 'sent' : 'failed',
				'sjpta_note' => rawurlencode( $note ),
			),
			sjpta_enquiry_settings_url()
		)
	);
	exit;
}
add_action( 'admin_post_sjpta_enquiry_test', 'sjpta_enquiry_send_test' );

/* ---------- Detail screen ---------- */

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
	$status = (string) get_post_meta( $post->ID, '_sjpta_status', true );
	$type   = (string) get_post_meta( $post->ID, '_sjpta_type', true );
	$source = (string) get_post_meta( $post->ID, '_sjpta_source', true );

	wp_nonce_field( 'sjpta_enquiry_status_' . $post->ID, 'sjpta_enquiry_status_nonce' );

	echo '<table class="widefat striped"><tbody>';

	printf(
		'<tr><th scope="row" style="width:220px"><label for="sjpta-status">%1$s</label></th><td><select id="sjpta-status" name="sjpta_status">%2$s</select> <span class="description">%3$s</span></td></tr>',
		esc_html__( 'Status', 'sjptheatrearts' ),
		wp_kses(
			implode(
				'',
				array_map(
					static function ( string $key, string $label ) use ( $status ): string {
						return sprintf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $key ), esc_html( $label ), selected( $status, $key, false ) );
					},
					array_keys( sjpta_enquiry_statuses() ),
					array_values( sjpta_enquiry_statuses() )
				)
			),
			array(
				'option' => array(
					'value'    => true,
					'selected' => true,
				),
			)
		),
		esc_html__( 'Choose "Dealt with" and press Update once you have replied. Choosing "New" for a spam entry releases it: the email is sent as it would have been.', 'sjptheatrearts' )
	);

	$reason = (string) get_post_meta( $post->ID, '_sjpta_spam_reason', true );

	if ( 'spam' === $status && '' !== $reason ) {
		printf(
			'<tr><th scope="row">%1$s</th><td>%2$s<br><a class="button" href="%3$s">%4$s</a></td></tr>',
			esc_html__( 'Held as spam because', 'sjptheatrearts' ),
			esc_html( $reason ),
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=sjpta_enquiry_status&id=' . $post->ID . '&status=new' ), 'sjpta_enquiry_status_' . $post->ID ) ),
			esc_html__( 'Not spam: release and send', 'sjptheatrearts' )
		);
	}

	printf(
		'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
		esc_html__( 'Form', 'sjptheatrearts' ),
		esc_html( sjpta_enquiry_type_label( $type ) )
	);

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
			'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
			esc_html( $field['label'] ),
			wp_kses_post( $value )
		);
	}

	$sent_to = (string) get_post_meta( $post->ID, '_sjpta_sent_to', true );

	printf(
		'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
		esc_html__( 'Emailed to', 'sjptheatrearts' ),
		'' !== $sent_to ? esc_html( $sent_to ) : esc_html__( 'Nobody (no address set for this form)', 'sjptheatrearts' )
	);

	if ( '' !== $source ) {
		printf(
			'<tr><th scope="row">%1$s</th><td><a href="%2$s">%2$s</a></td></tr>',
			esc_html__( 'Sent from', 'sjptheatrearts' ),
			esc_url( $source )
		);
	}

	$ip = (string) get_post_meta( $post->ID, '_sjpta_ip', true );

	if ( '' !== $ip ) {
		printf(
			'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
			esc_html__( 'Sender address', 'sjptheatrearts' ),
			esc_html( $ip )
		);
	}

	$forwarded = (string) get_post_meta( $post->ID, '_sjpta_forwarded', true );

	if ( '' !== $forwarded ) {
		printf(
			'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
			esc_html__( 'Passed to the mailing list', 'sjptheatrearts' ),
			esc_html( $forwarded )
		);
	}

	printf(
		'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
		esc_html__( 'Received', 'sjptheatrearts' ),
		esc_html( get_the_date( '', $post ) . ', ' . get_the_time( '', $post ) )
	);

	echo '</tbody></table>';
}

/**
 * Move an enquiry to a status, releasing it if it was held as spam.
 *
 * The one place a status changes from the admin, so leaving "spam" always
 * sends the email that was held back, whichever screen the click came from.
 *
 * @param int    $post_id Enquiry id.
 * @param string $status  New status key.
 *
 * @return void
 */
function sjpta_enquiry_set_status( int $post_id, string $status ): void {
	if ( ! isset( sjpta_enquiry_statuses()[ $status ] ) ) {
		return;
	}

	$was = (string) get_post_meta( $post_id, '_sjpta_status', true );

	if ( 'spam' === $was && 'spam' !== $status ) {
		sjpta_enquiry_release( $post_id );
	}

	update_post_meta( $post_id, '_sjpta_status', $status );
}

/**
 * Save the status chosen on the detail screen.
 *
 * @param int $post_id Enquiry id.
 *
 * @return void
 */
function sjpta_enquiry_save_status( int $post_id ): void {
	if ( ! isset( $_POST['sjpta_enquiry_status_nonce'], $_POST['sjpta_status'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( (string) wp_unslash( $_POST['sjpta_enquiry_status_nonce'] ) ), 'sjpta_enquiry_status_' . $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	sjpta_enquiry_set_status( $post_id, sanitize_key( (string) wp_unslash( $_POST['sjpta_status'] ) ) );
}
add_action( 'save_post_' . SJPTA_ENQUIRY_POST_TYPE, 'sjpta_enquiry_save_status' );

/* ---------- List screen ---------- */

/**
 * List the useful columns on the enquiries screen.
 *
 * @param array<string,string> $columns Existing columns.
 *
 * @return array<string,string>
 */
function sjpta_enquiry_columns( $columns ): array {
	$columns = (array) $columns;
	unset( $columns['date'] );

	return array_merge(
		$columns,
		array(
			'sjpta_type'   => __( 'Form', 'sjptheatrearts' ),
			'sjpta_status' => __( 'Status', 'sjptheatrearts' ),
			'sjpta_child'  => __( 'Child', 'sjptheatrearts' ),
			'sjpta_class'  => __( 'Class', 'sjptheatrearts' ),
			'sjpta_email'  => __( 'Email', 'sjptheatrearts' ),
			'date'         => __( 'Received', 'sjptheatrearts' ),
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
	$post_id = (int) $post_id;

	if ( 'sjpta_type' === $column ) {
		echo esc_html( sjpta_enquiry_type_label( (string) get_post_meta( $post_id, '_sjpta_type', true ) ) );
		return;
	}

	if ( 'sjpta_status' === $column ) {
		$status = (string) get_post_meta( $post_id, '_sjpta_status', true );
		$status = isset( sjpta_enquiry_statuses()[ $status ] ) ? $status : 'new';
		$label  = sjpta_enquiry_statuses()[ $status ];

		$link = static function ( string $to, string $text ) use ( $post_id ): string {
			return sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=sjpta_enquiry_status&id=' . $post_id . '&status=' . $to ), 'sjpta_enquiry_status_' . $post_id ) ),
				esc_html( $text )
			);
		};

		if ( 'spam' === $status ) {
			$reason  = (string) get_post_meta( $post_id, '_sjpta_spam_reason', true );
			$actions = $link( 'new', __( 'Not spam', 'sjptheatrearts' ) );
			$label  .= '' !== $reason ? '<br><span class="description">' . esc_html( $reason ) . '</span>' : '';
		} elseif ( 'done' === $status ) {
			$actions = $link( 'new', __( 'Reopen', 'sjptheatrearts' ) );
		} else {
			$actions = $link( 'done', __( 'Mark as dealt with', 'sjptheatrearts' ) ) . ' | ' . $link( 'spam', __( 'Spam', 'sjptheatrearts' ) );
		}

		printf(
			'<span class="sjpta-status sjpta-status--%1$s">%2$s</span><br>%3$s',
			esc_attr( $status ),
			wp_kses(
				$label,
				array(
					'br'   => array(),
					'span' => array( 'class' => true ),
				)
			),
			wp_kses( $actions, array( 'a' => array( 'href' => true ) ) )
		);
		return;
	}

	$map = array(
		'sjpta_child' => '_sjpta_child_name',
		'sjpta_class' => '_sjpta_class_want',
		'sjpta_email' => '_sjpta_email',
	);

	if ( isset( $map[ $column ] ) ) {
		echo esc_html( (string) get_post_meta( $post_id, $map[ $column ], true ) );
	}
}
add_action( 'manage_' . SJPTA_ENQUIRY_POST_TYPE . '_posts_custom_column', 'sjpta_enquiry_column', 10, 2 );

/**
 * A little weight on the rows that still need attention.
 *
 * @return void
 */
function sjpta_enquiry_list_styles(): void {
	$screen = get_current_screen();

	if ( ! $screen || SJPTA_ENQUIRY_POST_TYPE !== $screen->post_type ) {
		return;
	}

	echo '<style>.sjpta-status--new{font-weight:700;color:#a34a00}.sjpta-status--done{color:#0f7350}.sjpta-status--spam{color:#8a1f11}</style>';
}
add_action( 'admin_head', 'sjpta_enquiry_list_styles' );

/**
 * Flip an enquiry's status from the list.
 *
 * @return void
 */
function sjpta_enquiry_flip_status(): void {
	$id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
	$status = isset( $_GET['status'] ) ? sanitize_key( (string) $_GET['status'] ) : '';

	check_admin_referer( 'sjpta_enquiry_status_' . $id );

	if ( ! current_user_can( 'edit_post', $id ) || ! isset( sjpta_enquiry_statuses()[ $status ] ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'sjptheatrearts' ) );
	}

	sjpta_enquiry_set_status( $id, $status );

	$back = wp_get_referer();
	wp_safe_redirect( $back ? $back : sjpta_enquiry_list_url() );
	exit;
}
add_action( 'admin_post_sjpta_enquiry_status', 'sjpta_enquiry_flip_status' );

/**
 * Bulk actions for the same.
 *
 * @param array<string,string> $actions Existing bulk actions.
 *
 * @return array<string,string>
 */
function sjpta_enquiry_bulk_actions( $actions ): array {
	$actions = (array) $actions;
	unset( $actions['edit'] );

	$actions['sjpta_done'] = __( 'Mark as dealt with', 'sjptheatrearts' );
	$actions['sjpta_new']  = __( 'Mark as new (releases spam)', 'sjptheatrearts' );
	$actions['sjpta_spam'] = __( 'Mark as spam', 'sjptheatrearts' );

	return $actions;
}
add_filter( 'bulk_actions-edit-' . SJPTA_ENQUIRY_POST_TYPE, 'sjpta_enquiry_bulk_actions' );

/**
 * Apply the bulk actions.
 *
 * @param string     $redirect Where the list goes afterwards.
 * @param string     $action   Chosen action.
 * @param array<int> $ids      Selected enquiries.
 *
 * @return string
 */
function sjpta_enquiry_handle_bulk( $redirect, $action, $ids ): string {
	$map    = array(
		'sjpta_done' => 'done',
		'sjpta_new'  => 'new',
		'sjpta_spam' => 'spam',
	);
	$status = $map[ $action ] ?? '';

	if ( '' === $status ) {
		return (string) $redirect;
	}

	foreach ( (array) $ids as $id ) {
		if ( current_user_can( 'edit_post', (int) $id ) ) {
			sjpta_enquiry_set_status( (int) $id, $status );
		}
	}

	return (string) $redirect;
}
add_filter( 'handle_bulk_actions-edit-' . SJPTA_ENQUIRY_POST_TYPE, 'sjpta_enquiry_handle_bulk', 10, 3 );

/**
 * Filter menus above the list: by form and by status.
 *
 * @param string $post_type The screen's post type.
 *
 * @return void
 */
function sjpta_enquiry_filters( $post_type ): void {
	if ( SJPTA_ENQUIRY_POST_TYPE !== $post_type ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- list filters, read only.
	$type   = isset( $_GET['sjpta_type'] ) ? sanitize_key( (string) $_GET['sjpta_type'] ) : '';
	$status = isset( $_GET['sjpta_status'] ) ? sanitize_key( (string) $_GET['sjpta_status'] ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	echo '<label class="screen-reader-text" for="sjpta-filter-type">' . esc_html__( 'Filter by form', 'sjptheatrearts' ) . '</label>';
	echo '<select id="sjpta-filter-type" name="sjpta_type"><option value="">' . esc_html__( 'All forms', 'sjptheatrearts' ) . '</option>';

	foreach ( sjpta_enquiry_types() as $key => $def ) {
		printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $key ), esc_html( $def['label'] ), selected( $type, $key, false ) );
	}

	echo '</select>';

	echo '<label class="screen-reader-text" for="sjpta-filter-status">' . esc_html__( 'Filter by status', 'sjptheatrearts' ) . '</label>';
	echo '<select id="sjpta-filter-status" name="sjpta_status"><option value="">' . esc_html__( 'Any status', 'sjptheatrearts' ) . '</option>';

	foreach ( sjpta_enquiry_statuses() as $key => $label ) {
		if ( 'spam' === $key ) {
			/* translators: %d: number of quarantined submissions. */
			$label = sprintf( __( 'Spam (%d)', 'sjptheatrearts' ), sjpta_enquiry_count_status( 'spam' ) );
		}

		printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $key ), esc_html( $label ), selected( $status, $key, false ) );
	}

	echo '</select>';
}

/**
 * How many enquiries hold a status.
 *
 * @param string $status Status key.
 *
 * @return int
 */
function sjpta_enquiry_count_status( string $status ): int {
	$query = new WP_Query(
		array(
			'post_type'        => SJPTA_ENQUIRY_POST_TYPE,
			'post_status'      => 'any',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'suppress_filters' => true,
			'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one small admin count.
				array(
					'key'   => '_sjpta_status',
					'value' => $status,
				),
			),
		)
	);

	return (int) $query->found_posts;
}
add_action( 'restrict_manage_posts', 'sjpta_enquiry_filters' );

/**
 * Apply the filter menus.
 *
 * @param WP_Query $query The list query.
 *
 * @return void
 */
function sjpta_enquiry_filter_query( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || SJPTA_ENQUIRY_POST_TYPE !== $query->get( 'post_type' ) ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- list filters, read only.
	$type   = isset( $_GET['sjpta_type'] ) ? sanitize_key( (string) $_GET['sjpta_type'] ) : '';
	$status = isset( $_GET['sjpta_status'] ) ? sanitize_key( (string) $_GET['sjpta_status'] ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$meta = (array) $query->get( 'meta_query', array() );

	if ( '' !== $type ) {
		$meta[] = array(
			'key'   => '_sjpta_type',
			'value' => $type,
		);
	}

	if ( 'done' === $status || 'spam' === $status ) {
		$meta[] = array(
			'key'   => '_sjpta_status',
			'value' => $status,
		);
	} elseif ( 'new' === $status ) {
		// Enquiries stored before statuses existed have no meta and count as new.
		$meta[] = array(
			'relation' => 'OR',
			array(
				'key'     => '_sjpta_status',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_sjpta_status',
				'value'   => array( 'done', 'spam' ),
				'compare' => 'NOT IN',
			),
		);
	} else {
		// "Any status" means any real one: spam stays out of the way until asked for.
		$meta[] = array(
			'relation' => 'OR',
			array(
				'key'     => '_sjpta_status',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_sjpta_status',
				'value'   => 'spam',
				'compare' => '!=',
			),
		);
	}

	if ( ! empty( $meta ) ) {
		$query->set( 'meta_query', $meta ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- an admin list, filtered by the reader's choice.
	}
}
add_action( 'pre_get_posts', 'sjpta_enquiry_filter_query' );

/**
 * A count of new enquiries beside the menu label.
 *
 * @return void
 */
function sjpta_enquiry_menu_bubble(): void {
	global $menu;

	if ( ! is_array( $menu ) ) {
		return;
	}

	$query = new WP_Query(
		array(
			'post_type'        => SJPTA_ENQUIRY_POST_TYPE,
			'post_status'      => 'any',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'suppress_filters' => true,
			'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one small admin count.
				'relation' => 'OR',
				array(
					'key'     => '_sjpta_status',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_sjpta_status',
					'value'   => array( 'done', 'spam' ),
					'compare' => 'NOT IN',
				),
			),
		)
	);

	$count = (int) $query->found_posts;

	if ( $count <= 0 ) {
		return;
	}

	foreach ( $menu as $index => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=' . SJPTA_ENQUIRY_POST_TYPE === $item[2] ) {
			$menu[ $index ][0] .= sprintf( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- adding a count bubble is what this hook is for.
				' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>',
				$count
			);
			break;
		}
	}
}
add_action( 'admin_menu', 'sjpta_enquiry_menu_bubble', 20 );
