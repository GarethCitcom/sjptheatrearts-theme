<?php
/**
 * Discipline ticker.
 *
 * The strip is duplicated so a -50% translation loops seamlessly. The duplicate
 * is aria-hidden, so assistive technology reads the list once, and the whole
 * thing is marked presentational: it is a decorative marquee of terms that all
 * appear as real links elsewhere on the page.
 *
 * Animation is CSS only. With prefers-reduced-motion the strip simply sits
 * still — it never becomes unreadable.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx   = sjpta_block_context( $block ?? null );
$sjpta_raw   = sjpta_field( 'items', '', $sjpta_ctx );
$sjpta_lines = preg_split( '/\R/', $sjpta_raw );
$sjpta_lines = is_array( $sjpta_lines ) ? $sjpta_lines : array();
$sjpta_items = array_values( array_filter( array_map( 'trim', $sjpta_lines ) ) );

if ( array() === $sjpta_items ) {
	return;
}

// The design cycles the four badge pairs in order along the strip.
$sjpta_styles = array( 'purple', 'orange', 'magenta', 'green' );

/**
 * One run of pills.
 *
 * @param array<int,string> $items    Pill labels.
 * @param array<int,string> $styles   Badge style slugs to cycle.
 * @param bool              $is_clone Whether this run is the duplicate.
 *
 * @return void
 */
$sjpta_run = static function ( array $items, array $styles, bool $is_clone ) {
	printf( '<div class="sjpta-ticker__run"%s>', $is_clone ? ' aria-hidden="true"' : '' );

	foreach ( $items as $i => $label ) {
		printf(
			'<span class="sjpta-badge sjpta-badge--%1$s sjpta-ticker__pill">%2$s</span>',
			esc_attr( $styles[ $i % count( $styles ) ] ),
			esc_html( $label )
		);
	}

	echo '</div>';
};
?>
<div class="sjpta-ticker alignfull" role="presentation">
	<div class="sjpta-ticker__track">
		<?php
		$sjpta_run( $sjpta_items, $sjpta_styles, false );
		$sjpta_run( $sjpta_items, $sjpta_styles, true );
		?>
	</div>
</div>
