<?php
/**
 * The team.
 *
 * A teacher card shows a photograph when one exists, and otherwise a coloured
 * tile bearing their initial — the design's own device, and the honest fallback
 * while current headshots are still being gathered (brief §15). Nobody gets a
 * stand-in photograph of someone else.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label      = sjpta_field( 'label', '', $sjpta_ctx );
$sjpta_note       = sjpta_field( 'label_note', '', $sjpta_ctx );
$sjpta_link_label = sjpta_field( 'link_label', '', $sjpta_ctx );
$sjpta_link_url   = sjpta_link_or_page( sjpta_field( 'link_url', '', $sjpta_ctx ), 'about' );

$sjpta_safe_strong = sjpta_field( 'safeguarding_strong', '', $sjpta_ctx );
$sjpta_safe_text   = sjpta_field( 'safeguarding_text', '', $sjpta_ctx );

$sjpta_people = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'people', $sjpta_ctx ) : array();

if ( ! is_array( $sjpta_people ) ) {
	$sjpta_people = array();
}

if ( array() === $sjpta_people && '' === $sjpta_safe_text ) {
	return;
}

$sjpta_badge = SJPTA_URI . '/assets/images/safeguarding-white.png';
?>
<section class="sjpta-team alignfull">
	<div class="sjpta-team__inner">
		<?php sjpta_section_bar( $sjpta_label, $sjpta_note, $sjpta_link_label, $sjpta_link_url ); ?>

		<?php if ( array() !== $sjpta_people ) : ?>
			<ul class="sjpta-team__grid" data-stagger>
				<?php foreach ( $sjpta_people as $sjpta_person ) : ?>
					<?php
					$sjpta_name = isset( $sjpta_person['name'] ) ? trim( (string) $sjpta_person['name'] ) : '';

					if ( '' === $sjpta_name ) {
						continue;
					}

					$sjpta_photo = isset( $sjpta_person['photo'] ) ? (int) $sjpta_person['photo'] : 0;
					$sjpta_tone  = isset( $sjpta_person['tone'] ) ? (string) $sjpta_person['tone'] : 'purple';
					$sjpta_role  = isset( $sjpta_person['role'] ) ? (string) $sjpta_person['role'] : '';

					// mb_substr keeps a multi-byte first character intact.
					$sjpta_initial = function_exists( 'mb_substr' ) ? mb_substr( $sjpta_name, 0, 1 ) : substr( $sjpta_name, 0, 1 );
					?>
					<li>
						<a class="sjpta-person" href="<?php echo esc_url( $sjpta_link_url ); ?>" data-lift>
							<?php if ( $sjpta_photo ) : ?>
								<span class="sjpta-person__media">
									<?php
									echo wp_get_attachment_image(
										$sjpta_photo,
										'medium_large',
										false,
										array(
											'class'   => 'sjpta-person__img',
											'loading' => 'lazy',
											'sizes'   => '(max-width: 640px) 100vw, 300px',
										)
									);
									?>
								</span>
							<?php else : ?>
								<span class="sjpta-person__media sjpta-person__media--<?php echo esc_attr( $sjpta_tone ); ?>" aria-hidden="true">
									<span class="sjpta-person__initial"><?php echo esc_html( $sjpta_initial ); ?></span>
								</span>
							<?php endif; ?>

							<span class="sjpta-person__foot">
								<span>
									<span class="sjpta-person__name"><?php echo esc_html( $sjpta_name ); ?></span>
									<?php if ( '' !== $sjpta_role ) : ?>
										<span class="sjpta-person__role"><?php echo esc_html( $sjpta_role ); ?></span>
									<?php endif; ?>
								</span>
								<span class="sjpta-person__arrow" aria-hidden="true">
									<?php echo sjpta_icon( 'arrow-up-right', 14, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								</span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( '' !== $sjpta_safe_strong || '' !== $sjpta_safe_text ) : ?>
			<div class="sjpta-team__safeguarding">
				<span class="sjpta-team__badge">
					<img src="<?php echo esc_url( $sjpta_badge ); ?>" alt="<?php esc_attr_e( 'Dance School Safeguarding Services', 'sjptheatrearts' ); ?>" width="103" height="50" loading="lazy" decoding="async">
				</span>
				<p class="sjpta-team__safeguarding-text">
					<?php if ( '' !== $sjpta_safe_strong ) : ?>
						<strong><?php echo esc_html( $sjpta_safe_strong ); ?></strong>
					<?php endif; ?>
					<?php echo esc_html( $sjpta_safe_text ); ?>
				</p>
			</div>
		<?php endif; ?>
	</div>
</section>
