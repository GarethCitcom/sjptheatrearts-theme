<?php
/**
 * One piece of film, behind its poster.
 *
 * The brief leaves the video slot open: the show footage is still being cut, and
 * nothing here invents a file that does not exist. Until a video is attached the
 * block renders the poster with its caption and **no play button**, because a
 * play control that does nothing is worse than a photograph.
 *
 * Nothing autoplays and nothing preloads. `preload="none"` means the poster is
 * all a visitor downloads until they ask for the film, which is the difference
 * between a page that costs 200KB and one that costs 20MB on a phone.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx     = sjpta_block_context( $block ?? null );
$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_poster = $sjpta_has_acf ? (int) get_field( 'poster', $sjpta_ctx ) : 0;

if ( ! $sjpta_poster ) {
	return;
}

$sjpta_title = sjpta_field( 'title', '', $sjpta_ctx );
$sjpta_text  = sjpta_field( 'text', '', $sjpta_ctx );

/*
 * An attachment id, so the client picks a file from the media library rather
 * than pasting a URL that may or may not still resolve.
 */
$sjpta_video = $sjpta_has_acf ? (int) get_field( 'video', $sjpta_ctx ) : 0;
$sjpta_src   = $sjpta_video ? (string) wp_get_attachment_url( $sjpta_video ) : '';
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-video alignfull">
	<div class="sjpta-inner">
		<figure class="sjpta-video__frame" data-reveal>
			<?php if ( '' !== $sjpta_src ) : ?>
				<video
					class="sjpta-video__player"
					controls
					preload="none"
					playsinline
					poster="<?php echo esc_url( (string) wp_get_attachment_image_url( $sjpta_poster, 'full' ) ); ?>"
				>
					<source src="<?php echo esc_url( $sjpta_src ); ?>" type="<?php echo esc_attr( (string) get_post_mime_type( $sjpta_video ) ); ?>">
					<?php esc_html_e( 'Your browser cannot play this video.', 'sjptheatrearts' ); ?>
					<a href="<?php echo esc_url( $sjpta_src ); ?>"><?php esc_html_e( 'Download it instead.', 'sjptheatrearts' ); ?></a>
				</video>
			<?php else : ?>
				<?php
				echo wp_get_attachment_image(
					$sjpta_poster,
					'full',
					false,
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 1023px) 100vw, 1256px',
						'class'   => 'sjpta-video__poster',
					)
				);
				?>
			<?php endif; ?>

			<span class="sjpta-video__scrim" aria-hidden="true"></span>

			<?php if ( '' !== $sjpta_title || '' !== $sjpta_text ) : ?>
				<figcaption class="sjpta-video__caption">
					<?php if ( '' !== $sjpta_title ) : ?>
						<span class="sjpta-video__title"><?php echo esc_html( $sjpta_title ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $sjpta_text ) : ?>
						<p class="sjpta-video__text"><?php echo esc_html( $sjpta_text ); ?></p>
					<?php endif; ?>

					<?php if ( '' === $sjpta_src ) : ?>
						<?php
						/*
						 * Said plainly rather than left as a poster that looks like
						 * a broken video. This is the designed "to confirm" state
						 * for a piece of film nobody has finished editing.
						 */
						?>
						<p class="sjpta-video__soon sjpta-toconfirm">
							<?php esc_html_e( 'Film from the last show is being edited.', 'sjptheatrearts' ); ?>
						</p>
					<?php endif; ?>
				</figcaption>
			<?php endif; ?>
		</figure>
	</div>
</section>
