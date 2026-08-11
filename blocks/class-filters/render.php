<?php
/**
 * The class filter bar.
 *
 * Its own block, because the design puts it directly under the hero while the
 * list it controls sits below the age routes. The two find each other through
 * the URL and, once JavaScript is running, through `data-class-filters`.
 *
 * A real GET form underneath: with scripts off, the chips are links and the
 * selects submit, and every filtered view is a URL that can be shared, bookmarked
 * and indexed. assets/js/class-filters.js then intercepts all of that and swaps
 * the grid in place. The enhancement is not the mechanism; it sits on top of one.
 *
 * Registered as a plain dynamic block, not an ACF one: it has no settings to
 * edit, and an ACF block with no saved data trips a deprecation inside ACF on
 * PHP 8.1+.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_filters = sjpta_class_filters();
$sjpta_result  = sjpta_query_classes( $sjpta_filters );
$sjpta_count   = count( $sjpta_result['posts'] );

$sjpta_base = get_permalink();

$sjpta_styles = get_terms(
	array(
		'taxonomy'   => 'discipline',
		'hide_empty' => true,
	)
);
$sjpta_styles = is_array( $sjpta_styles ) ? $sjpta_styles : array();

$sjpta_on = '' !== $sjpta_filters['age'] || '' !== $sjpta_filters['style'] || '' !== $sjpta_filters['day'];
?>
<section<?php echo sjpta_anchor_attr( $attributes ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-filterbar alignfull">
	<div class="sjpta-inner">
		<form
			class="sjpta-filters"
			method="get"
			action="<?php echo esc_url( $sjpta_base ); ?>"
			data-class-filters
			data-endpoint="<?php echo esc_url( rest_url( 'sjptheatrearts/v1/classes' ) ); ?>"
		>
			<h2 class="screen-reader-text"><?php esc_html_e( 'Filter classes', 'sjptheatrearts' ); ?></h2>

			<div class="sjpta-filters__group">
				<span class="sjpta-filters__legend" id="sjpta-filter-age"><?php esc_html_e( 'Age', 'sjptheatrearts' ); ?></span>

				<div class="sjpta-filters__chips" role="group" aria-labelledby="sjpta-filter-age">
					<?php
					$sjpta_ages = array( '' => __( 'All', 'sjptheatrearts' ) );

					foreach ( sjpta_age_routes() as $sjpta_slug => $sjpta_route ) {
						if ( sjpta_class_count( 'age-group', $sjpta_slug ) ) {
							$sjpta_ages[ $sjpta_slug ] = $sjpta_route['short'];
						}
					}

					foreach ( $sjpta_ages as $sjpta_slug => $sjpta_name ) :
						$sjpta_is_on = $sjpta_slug === $sjpta_filters['age'];
						?>
						<a
							class="sjpta-chip<?php echo $sjpta_is_on ? ' is-on' : ''; ?>"
							href="<?php echo esc_url( '' === $sjpta_slug ? remove_query_arg( 'age', $sjpta_base ) : add_query_arg( 'age', $sjpta_slug, $sjpta_base ) ); ?>"
							data-filter="age"
							data-value="<?php echo esc_attr( $sjpta_slug ); ?>"
							<?php echo $sjpta_is_on ? ' aria-current="true"' : ''; ?>
						><?php echo esc_html( $sjpta_name ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* Carried through so choosing a style does not silently drop the age. */ ?>
			<input type="hidden" name="age" value="<?php echo esc_attr( $sjpta_filters['age'] ); ?>" data-filter-input="age">

			<span class="sjpta-filters__divider" aria-hidden="true"></span>

			<label class="sjpta-filters__select">
				<span class="sjpta-filters__legend"><?php esc_html_e( 'Style', 'sjptheatrearts' ); ?></span>
				<select name="style" data-filter="style">
					<option value=""><?php esc_html_e( 'Any style', 'sjptheatrearts' ); ?></option>
					<?php foreach ( $sjpta_styles as $sjpta_term ) : ?>
						<option value="<?php echo esc_attr( $sjpta_term->slug ); ?>" <?php selected( $sjpta_filters['style'], $sjpta_term->slug ); ?>>
							<?php echo esc_html( $sjpta_term->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>

			<label class="sjpta-filters__select">
				<span class="sjpta-filters__legend"><?php esc_html_e( 'Day', 'sjptheatrearts' ); ?></span>
				<select name="day" data-filter="day">
					<option value=""><?php esc_html_e( 'Any day', 'sjptheatrearts' ); ?></option>
					<?php
					// Every day any class runs on, not a fixed week, so the menu
					// can never offer a day that returns nothing.
					foreach ( sjpta_query_classes( array() )['days'] as $sjpta_day_name ) :
						?>
						<option value="<?php echo esc_attr( $sjpta_day_name ); ?>" <?php selected( $sjpta_filters['day'], $sjpta_day_name ); ?>>
							<?php echo esc_html( $sjpta_day_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>

			<?php /* Hidden once the script takes over, since changing a filter then applies itself. */ ?>
			<button class="sjpta-filters__go" type="submit"><?php esc_html_e( 'Apply', 'sjptheatrearts' ); ?></button>

			<p class="sjpta-filters__count" data-filter-count role="status">
				<?php
				printf(
					/* translators: %s: number of classes, in bold. */
					wp_kses_post( __( '<strong>%s</strong> shown', 'sjptheatrearts' ) ),
					esc_html(
						sprintf(
							/* translators: %d: number of classes. */
							_n( '%d class', '%d classes', $sjpta_count, 'sjptheatrearts' ),
							$sjpta_count
						)
					)
				);
				?>
				<?php if ( $sjpta_on ) : ?>
					<a class="sjpta-filters__clear" href="<?php echo esc_url( $sjpta_base ); ?>" data-filter-clear><?php esc_html_e( 'Clear', 'sjptheatrearts' ); ?></a>
				<?php endif; ?>
			</p>
		</form>
	</div>
</section>
