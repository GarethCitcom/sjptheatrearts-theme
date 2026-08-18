<?php
/**
 * Popular classes.
 *
 * A short row on the homepage, using the same card as the class list so the two
 * can never drift apart.
 *
 * "Popular" is a judgement, not data: nothing in the site knows which classes
 * fill up, so the client picks them. If they have not, the section falls back to
 * the first few rather than showing an empty row, because a homepage section
 * that vanishes when a field is blank is worse than one that leads with
 * something reasonable.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label = sjpta_field( 'label', __( 'Popular classes', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_note  = sjpta_field( 'note', '', $sjpta_ctx );
$sjpta_cta   = sjpta_field( 'cta_label', __( 'All', 'sjptheatrearts' ), $sjpta_ctx );

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );
$sjpta_chosen  = $sjpta_has_acf ? get_field( 'classes', $sjpta_ctx ) : array();
$sjpta_chosen  = is_array( $sjpta_chosen ) ? array_map( 'intval', $sjpta_chosen ) : array();

if ( ! empty( $sjpta_chosen ) ) {
	$sjpta_query = new WP_Query(
		array(
			'post_type'      => SJPTA_CLASS_POST_TYPE,
			'post_status'    => 'publish',
			'post__in'       => $sjpta_chosen,
			'orderby'        => 'post__in',
			'posts_per_page' => 6,
		)
	);
} else {
	$sjpta_query = new WP_Query(
		array(
			'post_type'      => SJPTA_CLASS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

if ( empty( $sjpta_query->posts ) ) {
	return;
}

$sjpta_page = get_page_by_path( 'classes' );
$sjpta_url  = $sjpta_page ? (string) get_permalink( $sjpta_page ) : home_url( '/classes/' );

/*
 * "All 15 classes", counted rather than typed. The design says thirteen; the
 * client's answers describe fifteen; either could change again.
 */
$sjpta_total = sjpta_class_count();
$sjpta_cta   = '' === $sjpta_cta ? '' : sprintf(
	/* translators: 1: button label, 2: number of classes. */
	_n( '%1$s %2$d class', '%1$s %2$d classes', $sjpta_total, 'sjptheatrearts' ),
	$sjpta_cta,
	$sjpta_total
);
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-popular alignfull">
	<div class="sjpta-inner">
		<?php /* A real heading, so the card titles below have a parent in the outline. */ ?>
		<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_label ); ?></h2>

		<?php sjpta_section_bar( $sjpta_label, $sjpta_note, $sjpta_cta, $sjpta_url ); ?>

		<div class="sjpta-popular__grid" data-reveal>
			<div class="sjpta-popular__cards" data-stagger>
				<?php
				foreach ( $sjpta_query->posts as $sjpta_post ) {
					sjpta_class_card( $sjpta_post );
				}
				?>
			</div>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
