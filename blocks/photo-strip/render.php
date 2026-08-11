<?php
/**
 * Photo strip above the footer.
 *
 * A CSS marquee of the school's own photography. Decorative: the images repeat,
 * carry no information the page does not already give, and are duplicated to
 * make the loop seamless — so the strip is marked presentational, the duplicate
 * run is hidden from assistive technology, and the photographs take empty alt
 * text rather than describing the same picture twice.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx    = sjpta_block_context( $block ?? null );
$sjpta_photos = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'photos', $sjpta_ctx ) : array();
$sjpta_handle = sjpta_field( 'handle', '', $sjpta_ctx );
$sjpta_insta  = sjpta_setting( 'instagram_url', 'https://www.instagram.com/sjp_theatre_arts/' );

if ( ! is_array( $sjpta_photos ) || array() === $sjpta_photos ) {
	return;
}

/**
 * One run of photographs.
 *
 * @param array<int,mixed> $photos   Attachment ids.
 * @param string           $handle   Optional handle badge on the first tile.
 * @param bool             $is_clone Whether this run is the duplicate.
 *
 * @return void
 */
$sjpta_run = static function ( array $photos, string $handle, bool $is_clone ) {
	printf( '<div class="sjpta-strip__run"%s>', $is_clone ? ' aria-hidden="true"' : '' );

	foreach ( $photos as $sjpta_i => $sjpta_photo ) {
		$sjpta_id = is_array( $sjpta_photo ) && isset( $sjpta_photo['photo'] ) ? (int) $sjpta_photo['photo'] : (int) $sjpta_photo;

		if ( ! $sjpta_id ) {
			continue;
		}

		echo '<div class="sjpta-strip__tile">';
		echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes its own output.
			$sjpta_id,
			'medium_large',
			false,
			array(
				'loading' => 'lazy',
				'alt'     => '',
				'sizes'   => '200px',
			)
		);

		if ( 0 === $sjpta_i && '' !== $handle && ! $is_clone ) {
			printf( '<span class="sjpta-strip__handle">%s</span>', esc_html( $handle ) );
		}

		echo '</div>';
	}

	echo '</div>';
};
?>
<section class="sjpta-strip alignfull" role="presentation">
	<?php if ( '' !== $sjpta_insta ) : ?>
		<a class="sjpta-strip__link" href="<?php echo esc_url( $sjpta_insta ); ?>"<?php echo sjpta_external_attr( $sjpta_insta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
			<span class="screen-reader-text"><?php esc_html_e( 'See more on Instagram', 'sjptheatrearts' ); ?></span>
			<?php echo sjpta_new_tab_note( $sjpta_insta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
	<?php endif; ?>

	<div class="sjpta-strip__track">
		<?php
		$sjpta_run( $sjpta_photos, $sjpta_handle, false );
		$sjpta_run( $sjpta_photos, $sjpta_handle, true );
		?>
	</div>

	<?php if ( '' !== $sjpta_insta ) : ?>
		</a>
	<?php endif; ?>
</section>
