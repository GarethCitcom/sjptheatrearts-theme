<?php
/**
 * Joining is three steps, plus the enrolment panel.
 *
 * The panel carries a real enrolment form, so somebody who only ever scrolls the
 * homepage can enrol without going anywhere. The nav's "Enrol now" still goes to
 * the Join page: two routes to the same action, deliberately.
 *
 * It is the short form rather than Join's long one. This is an aside partway
 * down a homepage, and anyone willing to answer nine questions is on the Join
 * page already. Same function, same handler, same routing either way.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_eyebrow   = sjpta_field( 'eyebrow', '', $sjpta_ctx );
$sjpta_heading   = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_highlight = sjpta_field( 'highlight', '', $sjpta_ctx );
$sjpta_intro     = sjpta_field( 'intro', '', $sjpta_ctx );
$sjpta_footnote  = sjpta_field( 'footnote_strong', '', $sjpta_ctx );
$sjpta_foottext  = sjpta_field( 'footnote_text', '', $sjpta_ctx );

$sjpta_panel_title = sjpta_field( 'panel_title', '', $sjpta_ctx );
$sjpta_panel_text  = sjpta_field( 'panel_text', '', $sjpta_ctx );
$sjpta_panel_label = sjpta_field( 'panel_label', '', $sjpta_ctx );
$sjpta_panel_url   = sjpta_link_or_page( sjpta_field( 'panel_url', '', $sjpta_ctx ), 'join' );

$sjpta_steps = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'steps', $sjpta_ctx ) : array();

if ( ! is_array( $sjpta_steps ) ) {
	$sjpta_steps = array();
}

if ( '' === $sjpta_heading && array() === $sjpta_steps ) {
	return;
}

$sjpta_tones = array( 'orange', 'magenta', 'green' );
?>
<section class="sjpta-join alignfull" id="join">
	<div class="sjpta-join__inner">
		<div class="sjpta-join__panel">
			<span class="sjpta-join__dots" aria-hidden="true"></span>

			<div class="sjpta-join__copy">
				<?php if ( '' !== $sjpta_eyebrow ) : ?>
					<span class="sjpta-badge sjpta-badge--orange sjpta-join__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="sjpta-join__heading">
					<?php
					echo wp_kses(
						sjpta_highlight_words( $sjpta_heading, $sjpta_highlight ),
						array(
							'span' => array( 'class' => array() ),
							'br'   => array(),
						)
					);
					?>
				</h2>

				<?php if ( '' !== $sjpta_intro ) : ?>
					<p class="sjpta-join__intro"><?php echo esc_html( $sjpta_intro ); ?></p>
				<?php endif; ?>

				<?php if ( array() !== $sjpta_steps ) : ?>
					<ol class="sjpta-join__steps">
						<?php foreach ( $sjpta_steps as $sjpta_i => $sjpta_step ) : ?>
							<?php
							$sjpta_title = isset( $sjpta_step['title'] ) ? (string) $sjpta_step['title'] : '';

							if ( '' === $sjpta_title ) {
								continue;
							}

							$sjpta_tone = $sjpta_tones[ $sjpta_i % count( $sjpta_tones ) ];
							?>
							<li class="sjpta-join__step">
								<span class="sjpta-join__num sjpta-join__num--<?php echo esc_attr( $sjpta_tone ); ?>" aria-hidden="true">
									<?php echo esc_html( str_pad( (string) ( $sjpta_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>
								</span>
								<div>
									<p class="sjpta-join__step-title"><?php echo esc_html( $sjpta_title ); ?></p>
									<?php if ( ! empty( $sjpta_step['text'] ) ) : ?>
										<p class="sjpta-join__step-text"><?php echo esc_html( (string) $sjpta_step['text'] ); ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( '' !== $sjpta_footnote || '' !== $sjpta_foottext ) : ?>
					<p class="sjpta-join__footnote">
						<?php if ( '' !== $sjpta_footnote ) : ?>
							<strong><?php echo esc_html( $sjpta_footnote ); ?></strong>
						<?php endif; ?>
						<?php echo esc_html( $sjpta_foottext ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="sjpta-join__cta">
				<?php if ( '' !== $sjpta_panel_title ) : ?>
					<p class="sjpta-join__cta-title"><?php echo esc_html( $sjpta_panel_title ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $sjpta_panel_text ) : ?>
					<p class="sjpta-join__cta-text"><?php echo esc_html( $sjpta_panel_text ); ?></p>
				<?php endif; ?>

				<?php
				/*
				 * The real form, not a link to it. A visitor who simply scrolls the
				 * homepage can ask without navigating anywhere, which is the whole
				 * point of the panel.
				 *
				 * This is an *enquiry*, and says so. Enrolling is the Join page's
				 * longer form; the nav's "Enrol now" goes there, and so does the
				 * "Ready to enrol?" button under this form, carrying across whatever
				 * has been typed. Two forms with two names, so nobody sends an
				 * enquiry believing they have enrolled, or the other way round.
				 *
				 * The short shape, not Join's long one. This is an aside on a
				 * homepage, and a parent who wants to answer nine questions is on
				 * the Join page already.
				 */
				sjpta_enquiry_form(
					array(
						'variant' => 'class',
						'type'    => 'enquiry',
						'subject' => __( 'Enquiry from the homepage', 'sjptheatrearts' ),
						'submit'  => '' !== $sjpta_panel_label ? $sjpta_panel_label : __( 'Send my enquiry', 'sjptheatrearts' ),
						'anchor'  => 'join',
						'after'   => array(
							'text'    => __( 'Ready to enrol?', 'sjptheatrearts' ),
							'label'   => __( 'Enrol now', 'sjptheatrearts' ),
							'url'     => $sjpta_panel_url . '#enquire',
							'prefill' => true,
						),
					)
				);
				?>
			</div>
		</div>
	</div>
</section>
