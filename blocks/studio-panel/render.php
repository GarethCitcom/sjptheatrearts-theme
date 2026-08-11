<?php
/**
 * Where the studio is, beside a way to ask about anything else.
 *
 * The four studio facts, address, parking, step-free access and waiting area,
 * are **site settings** rather than fields on this block. They are the same
 * facts wherever they appear, and all four have been outstanding since phase 1,
 * so each renders its own "to confirm" state until somebody fills it in. The
 * design shows exactly that, in the same magenta.
 *
 * The form is sjpta_enquiry_form() in its contact shape: no child fields, a
 * subject menu instead, because half these messages are from adults asking
 * about a wedding dance or a party.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx     = sjpta_block_context( $block ?? null );
$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_photo = $sjpta_has_acf ? (int) get_field( 'photo', $sjpta_ctx ) : 0;

$sjpta_name = sjpta_field( 'name', get_bloginfo( 'name' ), $sjpta_ctx );
$sjpta_maps = sjpta_field( 'maps_label', __( 'Open in maps', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_map  = sjpta_field( 'maps_url', '', $sjpta_ctx );
$sjpta_note = sjpta_field( 'note', '', $sjpta_ctx );

$sjpta_form_head = sjpta_field( 'form_heading', __( 'Send us a message', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_form_text = sjpta_field( 'form_text', '', $sjpta_ctx );
$sjpta_submit    = sjpta_field( 'submit_label', __( 'Send message', 'sjptheatrearts' ), $sjpta_ctx );

$sjpta_foot       = sjpta_field( 'footnote', '', $sjpta_ctx );
$sjpta_foot_label = sjpta_field( 'footnote_label', '', $sjpta_ctx );
$sjpta_foot_url   = sjpta_field( 'footnote_url', '', $sjpta_ctx );

$sjpta_locality = sjpta_setting( 'locality', __( 'Bromsgrove, Worcestershire', 'sjptheatrearts' ) );

/*
 * Deliberately no defaults. Every one of these is a fact about a building that
 * nobody has confirmed, and a plausible guess here is the kind that gets a
 * wheelchair user to a door with a step in front of it.
 */
$sjpta_facts = array(
	array( __( 'Full address', 'sjptheatrearts' ), sjpta_setting( 'full_address', '' ) ),
	array( __( 'Parking', 'sjptheatrearts' ), sjpta_setting( 'parking', '' ) ),
	array( __( 'Step-free access', 'sjptheatrearts' ), sjpta_setting( 'step_free_access', '' ) ),
	array( __( 'Waiting area', 'sjptheatrearts' ), sjpta_setting( 'waiting_area', '' ) ),
);
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-studio alignfull">
	<div class="sjpta-inner sjpta-studio__grid" data-reveal>

		<div class="sjpta-studio__place">
			<?php if ( $sjpta_photo ) : ?>
				<div class="sjpta-studio__media">
					<?php
					echo wp_get_attachment_image(
						$sjpta_photo,
						'large',
						false,
						array(
							'loading' => 'lazy',
							'sizes'   => '(max-width: 1023px) 100vw, 560px',
						)
					);
					?>

					<div class="sjpta-studio__over">
						<span class="sjpta-studio__name"><?php echo esc_html( $sjpta_name ); ?></span>
						<span class="sjpta-studio__locality"><?php echo esc_html( $sjpta_locality ); ?></span>
					</div>

					<?php
					/*
					 * No map link until there is an address to point at. A button
					 * labelled "Open in maps" that opens a search for nothing is
					 * worse than no button.
					 */
					?>
					<?php if ( '' !== $sjpta_map ) : ?>
						<a class="sjpta-btn sjpta-btn--white sjpta-studio__maps" href="<?php echo esc_url( $sjpta_map ); ?>" target="_blank" rel="noopener">
							<?php echo esc_html( $sjpta_maps ); ?>
							<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'sjptheatrearts' ); ?></span>
							<?php echo sjpta_icon( 'arrow-up-right', 12, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<dl class="sjpta-studio__facts">
				<?php foreach ( $sjpta_facts as $sjpta_fact ) : ?>
					<div class="sjpta-studio__fact">
						<dt><?php echo esc_html( $sjpta_fact[0] ); ?></dt>
						<dd class="<?php echo '' === $sjpta_fact[1] ? 'sjpta-toconfirm' : ''; ?>">
							<?php echo esc_html( '' === $sjpta_fact[1] ? __( 'To confirm before launch', 'sjptheatrearts' ) : $sjpta_fact[1] ); ?>
						</dd>
					</div>
				<?php endforeach; ?>
			</dl>

			<?php if ( '' !== $sjpta_note ) : ?>
				<p class="sjpta-studio__note">
					<span class="sjpta-studio__noteicon" aria-hidden="true">i</span>
					<?php echo esc_html( $sjpta_note ); ?>
				</p>
			<?php endif; ?>
		</div>

		<div class="sjpta-studio__form">
			<h2 class="sjpta-studio__formhead"><?php echo esc_html( $sjpta_form_head ); ?></h2>

			<?php if ( '' !== $sjpta_form_text ) : ?>
				<p class="sjpta-studio__formtext"><?php echo esc_html( $sjpta_form_text ); ?></p>
			<?php endif; ?>

			<?php
			sjpta_enquiry_form(
				array(
					'variant'   => 'contact',
					'subject'   => __( 'Message from the contact page', 'sjptheatrearts' ),
					'submit'    => $sjpta_submit,
					'anchor'    => ( isset( $block['anchor'] ) && is_string( $block['anchor'] ) ) ? $block['anchor'] : 'contact',
					'sent_head' => __( 'Thank you, that is on its way.', 'sjptheatrearts' ),
					'sent_text' => __( 'We answer every message ourselves, so it may take a day or two. If it is urgent, please ring us.', 'sjptheatrearts' ),
				)
			);
			?>

			<?php if ( '' !== $sjpta_foot ) : ?>
				<p class="sjpta-studio__foot">
					<?php echo esc_html( $sjpta_foot ); ?>
					<?php if ( '' !== $sjpta_foot_label && '' !== $sjpta_foot_url ) : ?>
						<a href="<?php echo esc_url( $sjpta_foot_url ); ?>"><?php echo esc_html( $sjpta_foot_label ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
