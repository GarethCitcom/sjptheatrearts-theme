<?php
/**
 * Enquiry panel.
 *
 * The left column is the design's invitation: brand mark, heading, copy and the
 * practical facts. The right column is the enquiry form.
 *
 * The form itself is sjpta_enquiry_form(), shared with every other enquiry point
 * on the site, so there is only ever one to keep working. Each panel names its
 * own recipient: Born To Be reaches Madison, the Join page reaches SJ.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_heading = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_intro   = sjpta_field( 'intro', '', $sjpta_ctx );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_logo = $sjpta_has_acf ? (int) get_field( 'logo', $sjpta_ctx ) : 0;

$sjpta_points = $sjpta_has_acf ? get_field( 'points', $sjpta_ctx ) : array();
$sjpta_points = is_array( $sjpta_points ) ? $sjpta_points : array();

$sjpta_form_head = sjpta_field( 'form_heading', '', $sjpta_ctx );
$sjpta_form_text = sjpta_field( 'form_text', '', $sjpta_ctx );
$sjpta_submit    = sjpta_field( 'submit_label', __( 'Send enquiry', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_consent   = sjpta_field( 'consent', '', $sjpta_ctx );
$sjpta_sent_head = sjpta_field( 'sent_heading', '', $sjpta_ctx );
$sjpta_sent_text = sjpta_field( 'sent_text', '', $sjpta_ctx );

/*
 * Which mailbox this panel writes to. The value is checked against the addresses
 * the site configures before anything is sent, so a tampered form cannot make
 * WordPress mail a stranger.
 */
$sjpta_route     = sjpta_field( 'recipient', 'sjp', $sjpta_ctx );
$sjpta_recipient = sjpta_enquiry_recipient( $sjpta_route );

$sjpta_choices = $sjpta_has_acf ? get_field( 'class_options', $sjpta_ctx ) : array();
$sjpta_options = array();

foreach ( is_array( $sjpta_choices ) ? $sjpta_choices : array() as $sjpta_choice ) {
	$sjpta_option = isset( $sjpta_choice['text'] ) ? (string) $sjpta_choice['text'] : '';

	if ( '' !== $sjpta_option ) {
		$sjpta_options[] = $sjpta_option;
	}
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-enquiry alignfull">
	<div class="sjpta-inner sjpta-enquiry__inner">
		<div class="sjpta-enquiry__panel" data-reveal>
			<div class="sjpta-enquiry__invite">
				<?php if ( $sjpta_logo ) : ?>
					<span class="sjpta-enquiry__logo">
						<?php
						echo wp_get_attachment_image(
							$sjpta_logo,
							'medium',
							false,
							array(
								'alt'     => '',
								'loading' => 'lazy',
								'sizes'   => '120px',
							)
						);
						?>
					</span>
				<?php endif; ?>

				<h2 class="sjpta-enquiry__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>

				<?php if ( '' !== $sjpta_intro ) : ?>
					<p class="sjpta-enquiry__intro"><?php echo esc_html( $sjpta_intro ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $sjpta_points ) ) : ?>
					<ul class="sjpta-enquiry__points">
						<?php foreach ( $sjpta_points as $sjpta_point ) : ?>
							<?php
							$sjpta_line = isset( $sjpta_point['text'] ) ? (string) $sjpta_point['text'] : '';

							if ( '' === $sjpta_line ) {
								continue;
							}

							$sjpta_tone = isset( $sjpta_point['tone'] ) ? (string) $sjpta_point['tone'] : 'accent';
							?>
							<li class="sjpta-enquiry__point sjpta-enquiry__point--<?php echo esc_attr( $sjpta_tone ); ?>">
								<span class="sjpta-enquiry__dot" aria-hidden="true"></span>
								<?php echo esc_html( $sjpta_line ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="sjpta-enquiry__form">
				<?php
				sjpta_enquiry_form(
					array(
						'variant'   => sjpta_field( 'variant', 'stack', $sjpta_ctx ),
						'recipient' => $sjpta_recipient,
						'route'     => $sjpta_route,
						'subject'   => $sjpta_heading,
						'classes'   => $sjpta_options,
						'heading'   => $sjpta_form_head,
						'intro'     => $sjpta_form_text,
						'submit'    => $sjpta_submit,
						'consent'   => $sjpta_consent,
						'sent_head' => $sjpta_sent_head,
						'sent_text' => $sjpta_sent_text,
						'anchor'    => ( isset( $block['anchor'] ) && is_string( $block['anchor'] ) ) ? $block['anchor'] : 'enquire',
					)
				);
				?>
			</div>
		</div>
	</div>
</section>
