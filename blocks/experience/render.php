<?php
/**
 * The SJP experience: rehearsal video slot plus two supporting cards.
 *
 * The play button appears only when a video has actually been set. The
 * performance videos are still to be compressed (brief §15), and a play button
 * over a still image with nothing behind it is a promise the site cannot keep,
 * so until a video exists the card is simply a photo with its caption, and the
 * editor sees a note saying why.
 *
 * With videos set, the play button is a real link to the first file, and the
 * video lightbox (assets/js/video-lightbox.js) intercepts the click to show a
 * pop-up player instead: the first video starts on open, each runs on into the
 * next, and a thumbnail rail switches between them. With JavaScript off the
 * link still opens the film, which is the same rule the photo galleries follow.
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
$sjpta_link_url   = sjpta_link_or_page( sjpta_field( 'link_url', '', $sjpta_ctx ), 'performances' );

$sjpta_video_title = sjpta_field( 'video_title', '', $sjpta_ctx );
$sjpta_video_text  = sjpta_field( 'video_text', '', $sjpta_ctx );
$sjpta_poster      = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? (int) get_field( 'poster', $sjpta_ctx ) : 0;

/*
 * The playlist for the pop-up player. A row without a video file is skipped:
 * a title with nothing behind it is the same broken promise as a bare play
 * button. Thumb may legitimately be absent — the player draws a purple tile.
 */
$sjpta_rows     = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'videos', $sjpta_ctx ) : array();
$sjpta_playlist = array();

if ( is_array( $sjpta_rows ) ) {
	foreach ( $sjpta_rows as $sjpta_row ) {
		$sjpta_file = isset( $sjpta_row['video'] ) ? (int) $sjpta_row['video'] : 0;
		$sjpta_src  = $sjpta_file ? (string) wp_get_attachment_url( $sjpta_file ) : '';

		if ( '' === $sjpta_src ) {
			continue;
		}

		$sjpta_thumb = isset( $sjpta_row['thumb'] ) ? (int) $sjpta_row['thumb'] : 0;

		$sjpta_playlist[] = array(
			'src'   => $sjpta_src,
			'type'  => (string) get_post_mime_type( $sjpta_file ),
			'title' => isset( $sjpta_row['title'] ) ? (string) $sjpta_row['title'] : '',
			'thumb' => $sjpta_thumb ? (string) wp_get_attachment_image_url( $sjpta_thumb, 'sjpta-480' ) : '',
		);
	}
}

$sjpta_cards = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'cards', $sjpta_ctx ) : array();

if ( ! is_array( $sjpta_cards ) ) {
	$sjpta_cards = array();
}

if ( '' === $sjpta_label && 0 === $sjpta_poster && array() === $sjpta_cards ) {
	return;
}
?>
<section class="sjpta-exp alignfull">
	<div class="sjpta-exp__inner">
		<?php sjpta_section_bar( $sjpta_label, $sjpta_note, $sjpta_link_label, $sjpta_link_url ); ?>

		<div class="sjpta-exp__grid">
			<div class="sjpta-exp__feature">
				<div class="sjpta-exp__media">
					<?php if ( $sjpta_poster ) : ?>
						<?php
						echo wp_get_attachment_image(
							$sjpta_poster,
							'large',
							false,
							array(
								'class'   => 'sjpta-exp__poster',
								'loading' => 'lazy',
								'sizes'   => '(max-width: 1023px) 100vw, 800px',
							)
						);
						?>
					<?php endif; ?>

					<span class="sjpta-exp__scrim" aria-hidden="true"></span>

					<?php if ( array() !== $sjpta_playlist ) : ?>
						<a
							class="sjpta-play sjpta-exp__play"
							href="<?php echo esc_url( $sjpta_playlist[0]['src'] ); ?>"
							data-pulse
							data-video-lightbox="<?php echo esc_attr( (string) wp_json_encode( $sjpta_playlist ) ); ?>"
						>
							<span class="screen-reader-text">
								<?php
								printf(
									/* translators: %s: video title. */
									esc_html__( 'Watch: %s', 'sjptheatrearts' ),
									esc_html( '' !== $sjpta_video_title ? $sjpta_video_title : __( 'rehearsal video', 'sjptheatrearts' ) )
								);
								?>
							</span>
							<svg width="28" height="28" viewBox="0 0 34 34" fill="none" aria-hidden="true" focusable="false"><path d="M12 8.5 25 17l-13 8.5V8.5Z" fill="#381064"/></svg>
						</a>
					<?php endif; ?>

					<div class="sjpta-exp__caption">
						<?php if ( '' !== $sjpta_video_title ) : ?>
							<p class="sjpta-exp__title"><?php echo esc_html( $sjpta_video_title ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== $sjpta_video_text ) : ?>
							<p class="sjpta-exp__text"><?php echo esc_html( $sjpta_video_text ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php if ( array() !== $sjpta_cards ) : ?>
				<ul class="sjpta-exp__cards">
					<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
						<?php
						$sjpta_title = isset( $sjpta_card['title'] ) ? (string) $sjpta_card['title'] : '';

						if ( '' === $sjpta_title ) {
							continue;
						}

						$sjpta_tone = isset( $sjpta_card['tone'] ) ? (string) $sjpta_card['tone'] : 'orange';
						?>
						<li class="sjpta-exp__card">
							<?php if ( ! empty( $sjpta_card['eyebrow'] ) ) : ?>
								<span class="sjpta-badge sjpta-badge--<?php echo esc_attr( $sjpta_tone ); ?> sjpta-exp__eyebrow">
									<?php echo esc_html( (string) $sjpta_card['eyebrow'] ); ?>
								</span>
							<?php endif; ?>

							<h3 class="sjpta-exp__card-title"><?php echo esc_html( $sjpta_title ); ?></h3>

							<?php if ( ! empty( $sjpta_card['text'] ) ) : ?>
								<p class="sjpta-exp__card-text"><?php echo esc_html( (string) $sjpta_card['text'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
