<?php
/**
 * About SJP, with the three headline numbers.
 *
 * The numbers count up on scroll where motion is welcome. The real value is in
 * the HTML from the start — the counter only ever animates towards a number
 * that is already there, so it is correct with JavaScript off and never
 * misreports if the animation is interrupted.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label      = sjpta_field( 'label', '', $sjpta_ctx );
$sjpta_label_note = sjpta_field( 'label_note', '', $sjpta_ctx );
$sjpta_link_label = sjpta_field( 'link_label', '', $sjpta_ctx );
$sjpta_link_url   = sjpta_field( 'link_url', '', $sjpta_ctx );
$sjpta_heading    = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_highlight  = sjpta_field( 'highlight', '', $sjpta_ctx );
$sjpta_body       = sjpta_field( 'body', '', $sjpta_ctx );
$sjpta_caption    = sjpta_field( 'image_caption', '', $sjpta_ctx );
$sjpta_by_strong  = sjpta_field( 'byline_strong', '', $sjpta_ctx );
$sjpta_by_text    = sjpta_field( 'byline_text', '', $sjpta_ctx );

$sjpta_stats  = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'stats', $sjpta_ctx ) : array();
$sjpta_image  = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? (int) get_field( 'image', $sjpta_ctx ) : 0;
$sjpta_avatar = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? (int) get_field( 'byline_image', $sjpta_ctx ) : 0;

if ( ! is_array( $sjpta_stats ) ) {
	$sjpta_stats = array();
}

if ( '' === $sjpta_heading && array() === $sjpta_stats ) {
	return;
}

if ( '' === $sjpta_link_url ) {
	$sjpta_about    = get_page_by_path( 'about' );
	$sjpta_link_url = $sjpta_about ? (string) get_permalink( $sjpta_about ) : home_url( '/about/' );
}

// Paragraphs are separated by a blank line.
$sjpta_split      = preg_split( '/\R{2,}/', $sjpta_body );
$sjpta_split      = is_array( $sjpta_split ) ? $sjpta_split : array();
$sjpta_paragraphs = array_values( array_filter( array_map( 'trim', $sjpta_split ) ) );
?>
<section class="sjpta-about alignfull">
	<div class="sjpta-about__inner">
		<?php sjpta_section_bar( $sjpta_label, $sjpta_label_note, $sjpta_link_label, $sjpta_link_url ); ?>

		<div class="sjpta-about__grid">
			<div class="sjpta-about__copy">
				<div>
					<?php if ( '' !== $sjpta_heading ) : ?>
						<h2 class="sjpta-about__heading">
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

					<?php foreach ( $sjpta_paragraphs as $sjpta_paragraph ) : ?>
						<p class="sjpta-about__para"><?php echo esc_html( $sjpta_paragraph ); ?></p>
					<?php endforeach; ?>
				</div>

				<?php if ( array() !== $sjpta_stats ) : ?>
					<dl class="sjpta-about__stats">
						<?php foreach ( $sjpta_stats as $sjpta_stat ) : ?>
							<?php
							/*
							 * Counted where the site can count it, so the homepage
							 * and the About page can never disagree about how many
							 * classes there are or how many awarding bodies the
							 * page shows. Falls back to whatever was typed.
							 */
							$sjpta_value = sjpta_stat_value( (string) ( $sjpta_stat['source'] ?? 'typed' ) );

							if ( '' === $sjpta_value ) {
								$sjpta_value = isset( $sjpta_stat['value'] ) ? trim( (string) $sjpta_stat['value'] ) : '';
							}

							if ( '' === $sjpta_value ) {
								continue;
							}

							$sjpta_tone = isset( $sjpta_stat['tone'] ) ? (string) $sjpta_stat['tone'] : 'orange';

							// Only a plain whole number can be counted up. "2–18+"
							// is a range, so it is left alone rather than animated
							// into something meaningless.
							$sjpta_countable = (bool) preg_match( '/^\d+$/', $sjpta_value );
							?>
							<div class="sjpta-about__stat">
								<dt class="sjpta-about__stat-value sjpta-about__stat-value--<?php echo esc_attr( $sjpta_tone ); ?>"<?php echo $sjpta_countable ? ' data-count="' . esc_attr( $sjpta_value ) . '"' : ''; ?>>
									<?php echo esc_html( $sjpta_value ); ?>
								</dt>
								<dd class="sjpta-about__stat-label"><?php echo esc_html( isset( $sjpta_stat['label'] ) ? (string) $sjpta_stat['label'] : '' ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<div class="sjpta-about__side">
				<?php if ( $sjpta_image ) : ?>
					<figure class="sjpta-about__figure">
						<?php
						echo wp_get_attachment_image(
							$sjpta_image,
							'large',
							false,
							array(
								'class'   => 'sjpta-about__img',
								'loading' => 'lazy',
								'sizes'   => '(max-width: 1023px) 100vw, 560px',
							)
						);
						?>
						<?php if ( '' !== $sjpta_caption ) : ?>
							<figcaption class="sjpta-about__caption"><?php echo esc_html( $sjpta_caption ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<?php if ( '' !== $sjpta_by_strong || '' !== $sjpta_by_text ) : ?>
					<div class="sjpta-about__byline">
						<?php if ( $sjpta_avatar ) : ?>
							<span class="sjpta-about__avatar">
								<?php
								echo wp_get_attachment_image(
									$sjpta_avatar,
									'thumbnail',
									false,
									array(
										'loading' => 'lazy',
										'sizes'   => '58px',
									)
								);
								?>
							</span>
						<?php endif; ?>
						<p class="sjpta-about__byline-text">
							<?php if ( '' !== $sjpta_by_strong ) : ?>
								<strong><?php echo esc_html( $sjpta_by_strong ); ?></strong>
							<?php endif; ?>
							<?php echo esc_html( $sjpta_by_text ); ?>
						</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
