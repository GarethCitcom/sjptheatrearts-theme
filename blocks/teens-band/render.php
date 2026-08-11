<?php
/**
 * Teens band.
 *
 * Speaks directly to the teenager rather than the parent, per the content
 * strategy. The background photo sits behind a gradient scrim so the white
 * heading keeps its contrast whatever the image.
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
$sjpta_text      = sjpta_field( 'text', '', $sjpta_ctx );

$sjpta_primary_label   = sjpta_field( 'primary_label', '', $sjpta_ctx );
$sjpta_secondary_label = sjpta_field( 'secondary_label', '', $sjpta_ctx );

$sjpta_join    = get_page_by_path( 'join' );
$sjpta_classes = get_page_by_path( 'classes' );

$sjpta_primary_url   = sjpta_field( 'primary_url', $sjpta_join ? (string) get_permalink( $sjpta_join ) : home_url( '/join/' ), $sjpta_ctx );
$sjpta_secondary_url = sjpta_field( 'secondary_url', $sjpta_classes ? (string) get_permalink( $sjpta_classes ) : home_url( '/classes/' ), $sjpta_ctx );

$sjpta_bg     = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? (int) get_field( 'background', $sjpta_ctx ) : 0;
$sjpta_routes = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'routes', $sjpta_ctx ) : array();

if ( ! is_array( $sjpta_routes ) ) {
	$sjpta_routes = array();
}

if ( '' === $sjpta_heading && array() === $sjpta_routes ) {
	return;
}
?>
<section class="sjpta-teens alignfull">
	<div class="sjpta-teens__inner">
		<div class="sjpta-teens__panel">
			<div class="sjpta-teens__lead">
				<?php if ( $sjpta_bg ) : ?>
					<?php
					echo wp_get_attachment_image(
						$sjpta_bg,
						'large',
						false,
						array(
							'class'   => 'sjpta-teens__bg',
							'loading' => 'lazy',
							'alt'     => '',
							'sizes'   => '(max-width: 1023px) 100vw, 740px',
						)
					);
					?>
				<?php endif; ?>
				<span class="sjpta-teens__scrim" aria-hidden="true"></span>

				<div class="sjpta-teens__copy">
					<?php if ( '' !== $sjpta_eyebrow ) : ?>
						<span class="sjpta-badge sjpta-badge--magenta sjpta-teens__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
					<?php endif; ?>

					<?php if ( '' !== $sjpta_heading ) : ?>
						<h2 class="sjpta-teens__heading">
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
					<?php endif; ?>

					<?php if ( '' !== $sjpta_text ) : ?>
						<p class="sjpta-teens__text"><?php echo esc_html( $sjpta_text ); ?></p>
					<?php endif; ?>

					<div class="sjpta-teens__actions">
						<?php if ( '' !== $sjpta_primary_label ) : ?>
							<a class="sjpta-btn sjpta-btn--primary sjpta-teens__btn" href="<?php echo esc_url( $sjpta_primary_url ); ?>">
								<?php echo esc_html( $sjpta_primary_label ); ?>
							</a>
						<?php endif; ?>

						<?php if ( '' !== $sjpta_secondary_label ) : ?>
							<a class="sjpta-btn sjpta-btn--ghost sjpta-teens__btn" href="<?php echo esc_url( $sjpta_secondary_url ); ?>">
								<?php echo esc_html( $sjpta_secondary_label ); ?>
								<?php echo sjpta_icon( 'arrow-right', 14, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php if ( array() !== $sjpta_routes ) : ?>
				<ul class="sjpta-teens__routes">
					<?php foreach ( $sjpta_routes as $sjpta_route ) : ?>
						<?php
						$sjpta_title = isset( $sjpta_route['title'] ) ? (string) $sjpta_route['title'] : '';

						if ( '' === $sjpta_title ) {
							continue;
						}

						$sjpta_url   = isset( $sjpta_route['url'] ) ? (string) $sjpta_route['url'] : '';
						$sjpta_thumb = isset( $sjpta_route['image'] ) ? (int) $sjpta_route['image'] : 0;
						?>
						<li>
							<a class="sjpta-route" href="<?php echo esc_url( '' !== $sjpta_url ? $sjpta_url : $sjpta_secondary_url ); ?>">
								<?php if ( $sjpta_thumb ) : ?>
									<span class="sjpta-route__thumb">
										<?php
										echo wp_get_attachment_image(
											$sjpta_thumb,
											'thumbnail',
											false,
											array(
												'loading' => 'lazy',
												'alt'     => '',
												'sizes'   => '56px',
											)
										);
										?>
									</span>
								<?php endif; ?>

								<span class="sjpta-route__text">
									<span class="sjpta-route__title"><?php echo esc_html( $sjpta_title ); ?></span>
									<?php if ( ! empty( $sjpta_route['text'] ) ) : ?>
										<span class="sjpta-route__note"><?php echo esc_html( (string) $sjpta_route['text'] ); ?></span>
									<?php endif; ?>
								</span>

								<span class="sjpta-route__arrow" aria-hidden="true">
									<?php echo sjpta_icon( 'arrow-up-right', 14, '#FF9A3D' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
