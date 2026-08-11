<?php
/**
 * Age routes.
 *
 * Four cards, one per age route, each linking into the class list filtered to
 * that route. The counts are queried, never typed: the design's numbers describe
 * a thirteen-class site and the real one has fifteen.
 *
 * A route with no classes is not shown. An empty route is a dead end for a parent
 * and a lie about what the school offers.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label = sjpta_field( 'label', __( 'Start with an age route', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_note  = sjpta_field( 'note', '', $sjpta_ctx );

$sjpta_classes_page = get_page_by_path( 'classes' );
$sjpta_base         = $sjpta_classes_page ? (string) get_permalink( $sjpta_classes_page ) : home_url( '/classes/' );
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-routes alignfull">
	<div class="sjpta-inner sjpta-routes__inner">
		<?php sjpta_section_bar( $sjpta_label, $sjpta_note, '', '' ); ?>

		<div class="sjpta-routes__grid" data-reveal>
			<div class="sjpta-routes__cards" data-stagger>
				<?php foreach ( sjpta_age_routes() as $sjpta_slug => $sjpta_route ) : ?>
					<?php
					$sjpta_count = sjpta_class_count( 'age-group', $sjpta_slug );

					if ( 0 === $sjpta_count ) {
						continue;
					}

					$sjpta_url = add_query_arg( 'age', $sjpta_slug, $sjpta_base ) . '#all';
					?>
					<a
						class="sjpta-route sjpta-route--<?php echo esc_attr( $sjpta_route['tone'] ); ?>"
						href="<?php echo esc_url( $sjpta_url ); ?>"
						data-filter="age"
						data-value="<?php echo esc_attr( $sjpta_slug ); ?>"
						data-lift
					>
						<span class="sjpta-route__icon">
							<?php echo sjpta_icon( $sjpta_route['icon'], 22, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</span>

						<span class="sjpta-route__copy">
							<span class="sjpta-route__name"><?php echo esc_html( $sjpta_route['name'] ); ?></span>
							<span class="sjpta-route__meta">
								<?php
								printf(
									/* translators: 1: age range, 2: number of classes. */
									esc_html( _n( '%1$s · %2$d class', '%1$s · %2$d classes', $sjpta_count, 'sjptheatrearts' ) ),
									esc_html( $sjpta_route['range'] ),
									(int) $sjpta_count
								);
								?>
							</span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
