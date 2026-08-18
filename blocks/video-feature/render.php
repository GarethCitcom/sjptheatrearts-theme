<?php
/**
 * One piece of film, behind its poster.
 *
 * The brief leaves the video slot open: the show footage is still being cut, and
 * nothing here invents a file that does not exist. Until a video is attached the
 * block renders the poster with its caption and **no play button**, because a
 * play control that does nothing is worse than a photograph.
 *
 * With a video attached the poster stays exactly as designed and gains the play
 * button, which opens the same pop-up player the homepage uses (a one-item
 * playlist through `sjpta-video-lightbox`). The button is a real link to the
 * file, so with scripting off it still plays. Nothing autoplays and nothing
 * preloads: the poster is all a visitor downloads until they ask for the film,
 * which is the difference between a page that costs 200KB and one that costs
 * 20MB on a phone.
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

/*
 * The same playlist shape the homepage's experience block hands to the video
 * lightbox, with one entry. The poster doubles as the thumbnail, though the
 * player hides its rail for a single video anyway.
 */
$sjpta_playlist = array();

if ( '' !== $sjpta_src ) {
	$sjpta_playlist[] = array(
		'src'   => $sjpta_src,
		'type'  => (string) get_post_mime_type( $sjpta_video ),
		'title' => $sjpta_title,
		'thumb' => (string) wp_get_attachment_image_url( $sjpta_poster, 'sjpta-480' ),
	);
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-video alignfull">
	<div class="sjpta-inner">
		<figure class="sjpta-video__frame" data-reveal>
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

			<span class="sjpta-video__scrim" aria-hidden="true"></span>

			<?php if ( array() !== $sjpta_playlist ) : ?>
				<a
					class="sjpta-play sjpta-video__play"
					href="<?php echo esc_url( $sjpta_src ); ?>"
					data-pulse
					data-video-lightbox="<?php echo esc_attr( (string) wp_json_encode( $sjpta_playlist ) ); ?>"
				>
					<span class="screen-reader-text">
						<?php
						printf(
							/* translators: %s: video title. */
							esc_html__( 'Watch: %s', 'sjptheatrearts' ),
							esc_html( '' !== $sjpta_title ? $sjpta_title : __( 'show video', 'sjptheatrearts' ) )
						);
						?>
					</span>
					<svg width="30" height="30" viewBox="0 0 34 34" fill="none" aria-hidden="true" focusable="false"><path d="M12 8.5 25 17l-13 8.5V8.5Z" fill="#381064"/></svg>
				</a>
			<?php endif; ?>

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
