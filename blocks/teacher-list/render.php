<?php
/**
 * The teaching team.
 *
 * A photograph beside a list of who teaches what. Deliberately not the
 * homepage's `team` block: that one is a grid of portraits, and this is a
 * reading list a parent scans to find the person who takes their child's class.
 *
 * Names, subjects and qualifications are facts about real people. Every one is a
 * field, and an empty field renders nothing rather than a plausible guess. The
 * note under the list exists to say plainly that photographs and qualifications
 * are still being gathered, which is truer than quietly showing four blanks.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label = sjpta_field( 'label', __( 'Meet the teachers', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_note  = sjpta_field( 'note', '', $sjpta_ctx );
$sjpta_cta   = sjpta_field( 'link_label', '', $sjpta_ctx );
$sjpta_url   = sjpta_field( 'link_url', '', $sjpta_ctx );
$sjpta_foot  = sjpta_field( 'footnote', '', $sjpta_ctx );

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_photo   = $sjpta_has_acf ? (int) get_field( 'photo', $sjpta_ctx ) : 0;
$sjpta_caption = sjpta_field( 'photo_caption', '', $sjpta_ctx );

$sjpta_people = $sjpta_has_acf ? get_field( 'people', $sjpta_ctx ) : array();
$sjpta_people = is_array( $sjpta_people ) ? $sjpta_people : array();

if ( empty( $sjpta_people ) ) {
	return;
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-teachers alignfull">
	<div class="sjpta-inner">
		<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_label ); ?></h2>

		<?php sjpta_section_bar( $sjpta_label, $sjpta_note, $sjpta_cta, $sjpta_url ); ?>

		<div class="sjpta-teachers__grid" data-reveal>
			<?php if ( $sjpta_photo ) : ?>
				<div class="sjpta-teachers__media">
					<div class="sjpta-teachers__photo">
						<?php
						echo wp_get_attachment_image(
							$sjpta_photo,
							'large',
							false,
							array(
								'loading' => 'lazy',
								'sizes'   => '(max-width: 1023px) 100vw, 620px',
							)
						);
						?>
						<?php if ( '' !== $sjpta_caption ) : ?>
							<span class="sjpta-teachers__caption"><?php echo esc_html( $sjpta_caption ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<ul class="sjpta-teachers__list">
				<?php foreach ( $sjpta_people as $sjpta_person ) : ?>
					<?php
					$sjpta_name = trim( (string) ( $sjpta_person['name'] ?? '' ) );

					if ( '' === $sjpta_name ) {
						continue;
					}

					$sjpta_role = trim( (string) ( $sjpta_person['role'] ?? '' ) );
					$sjpta_tone = (string) ( $sjpta_person['tone'] ?? 'orange' );
					$sjpta_face = (int) ( $sjpta_person['photo'] ?? 0 );

					/*
					 * The initial is taken from the name rather than typed, so it
					 * cannot end up disagreeing with the person it belongs to.
					 * mb_substr because a name may not start with an ASCII letter.
					 */
					$sjpta_initial = mb_strtoupper( mb_substr( $sjpta_name, 0, 1 ) );
					?>
					<li class="sjpta-teachers__person">
						<?php
						/*
						 * A portrait when there is one, the coloured initial when
						 * there is not. The initial is the stand-in, not the
						 * design: it is sized as a portrait so the row does not
						 * change shape the day a photograph arrives.
						 */
						?>
						<?php if ( $sjpta_face ) : ?>
							<span class="sjpta-teachers__face">
								<?php
								echo wp_get_attachment_image(
									$sjpta_face,
									'thumbnail',
									false,
									array(
										'loading' => 'lazy',
										'sizes'   => '56px',
										'alt'     => $sjpta_name,
									)
								);
								?>
							</span>
						<?php else : ?>
							<span class="sjpta-teachers__initial sjpta-teachers__initial--<?php echo esc_attr( $sjpta_tone ); ?>" aria-hidden="true">
								<?php echo esc_html( $sjpta_initial ); ?>
							</span>
						<?php endif; ?>
						<span class="sjpta-teachers__text">
							<span class="sjpta-teachers__name"><?php echo esc_html( $sjpta_name ); ?></span>
							<?php if ( '' !== $sjpta_role ) : ?>
								<span class="sjpta-teachers__role"><?php echo esc_html( $sjpta_role ); ?></span>
							<?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>

				<?php if ( '' !== $sjpta_foot ) : ?>
					<li class="sjpta-teachers__footnote"><?php echo esc_html( $sjpta_foot ); ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</section>
