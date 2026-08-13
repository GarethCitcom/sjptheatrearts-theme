<?php
/**
 * The class filter bar.
 *
 * Its own block, because the design puts it directly under the hero while the
 * list it controls sits below the age routes. The two find each other through
 * the URL and, once JavaScript is running, through `data-class-filters`.
 *
 * A real GET form underneath: each filter is a native <details> menu holding
 * real checkboxes, so with scripts off a visitor opens a menu, ticks what they
 * want and presses Apply, and every filtered view is a URL that can be shared,
 * bookmarked and indexed. assets/js/class-filters.js then intercepts all of
 * that and swaps the grid in place. The enhancement is not the mechanism; it
 * sits on top of one.
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

$sjpta_style_options = array();

foreach ( $sjpta_styles as $sjpta_term ) {
	$sjpta_style_options[ $sjpta_term->slug ] = $sjpta_term->name;
}

$sjpta_age_options = array();

foreach ( sjpta_age_routes() as $sjpta_slug => $sjpta_route ) {
	if ( sjpta_class_count( 'age-group', $sjpta_slug ) ) {
		$sjpta_age_options[ $sjpta_slug ] = $sjpta_route['short'];
	}
}

// Days and tags come from the classes themselves, so a menu can never offer a
// value that returns nothing.
$sjpta_all = sjpta_query_classes( array() );

$sjpta_menus = array(
	'age'   => array(
		'label'   => __( 'Age', 'sjptheatrearts' ),
		'empty'   => __( 'Any age', 'sjptheatrearts' ),
		'options' => $sjpta_age_options,
	),
	'style' => array(
		'label'   => __( 'Style', 'sjptheatrearts' ),
		'empty'   => __( 'Any style', 'sjptheatrearts' ),
		'options' => $sjpta_style_options,
	),
	'day'   => array(
		'label'   => __( 'Day', 'sjptheatrearts' ),
		'empty'   => __( 'Any day', 'sjptheatrearts' ),
		'options' => $sjpta_all['days'],
	),
	'tag'   => array(
		'label'   => __( 'Tags', 'sjptheatrearts' ),
		'empty'   => __( 'Any tag', 'sjptheatrearts' ),
		'options' => $sjpta_all['tags'],
	),
);

$sjpta_on = ! empty( $sjpta_filters['age'] ) || ! empty( $sjpta_filters['style'] ) || ! empty( $sjpta_filters['day'] ) || ! empty( $sjpta_filters['tag'] );
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

			<?php foreach ( $sjpta_menus as $sjpta_key => $sjpta_menu ) : ?>
				<?php
				if ( empty( $sjpta_menu['options'] ) ) {
					continue;
				}

				$sjpta_picked = $sjpta_filters[ $sjpta_key ];
				$sjpta_names  = array();

				foreach ( $sjpta_picked as $sjpta_slug ) {
					if ( isset( $sjpta_menu['options'][ $sjpta_slug ] ) ) {
						$sjpta_names[] = $sjpta_menu['options'][ $sjpta_slug ];
					}
				}
				?>
				<div class="sjpta-filters__field">
					<span class="sjpta-filters__legend" id="sjpta-filter-<?php echo esc_attr( $sjpta_key ); ?>">
						<?php echo esc_html( $sjpta_menu['label'] ); ?>
					</span>

					<details class="sjpta-filters__menu" data-filter-menu="<?php echo esc_attr( $sjpta_key ); ?>">
						<summary
							class="sjpta-filters__summary"
							aria-labelledby="sjpta-filter-<?php echo esc_attr( $sjpta_key ); ?> sjpta-filter-<?php echo esc_attr( $sjpta_key ); ?>-value"
						>
							<span
								class="sjpta-filters__value"
								id="sjpta-filter-<?php echo esc_attr( $sjpta_key ); ?>-value"
								data-menu-label
								data-empty="<?php echo esc_attr( $sjpta_menu['empty'] ); ?>"
							><?php echo esc_html( empty( $sjpta_names ) ? $sjpta_menu['empty'] : implode( ', ', $sjpta_names ) ); ?></span>

							<span class="sjpta-filters__chevron" aria-hidden="true"></span>
						</summary>

						<fieldset class="sjpta-filters__panel">
							<legend class="screen-reader-text">
								<?php
								/* translators: %s: filter name, e.g. "Age". */
								printf( esc_html__( 'Filter by %s', 'sjptheatrearts' ), esc_html( $sjpta_menu['label'] ) );
								?>
							</legend>

							<?php foreach ( $sjpta_menu['options'] as $sjpta_slug => $sjpta_name ) : ?>
								<label class="sjpta-filters__option">
									<input
										type="checkbox"
										name="<?php echo esc_attr( $sjpta_key ); ?>[]"
										value="<?php echo esc_attr( $sjpta_slug ); ?>"
										data-filter="<?php echo esc_attr( $sjpta_key ); ?>"
										data-label="<?php echo esc_attr( $sjpta_name ); ?>"
										<?php checked( in_array( $sjpta_slug, $sjpta_picked, true ) ); ?>
									>
									<span><?php echo esc_html( $sjpta_name ); ?></span>
								</label>
							<?php endforeach; ?>
						</fieldset>
					</details>
				</div>
			<?php endforeach; ?>

			<?php /* Hidden once the script takes over, since changing a filter then applies itself. */ ?>
			<button class="sjpta-filters__go" type="submit"><?php esc_html_e( 'Apply', 'sjptheatrearts' ); ?></button>

			<p class="sjpta-filters__count">
				<?php /* Its own span: the script rewrites the count, and the Clear link must survive that. */ ?>
				<span data-filter-count role="status">
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
				</span>

				<?php /* Always in the markup so the script can show it the moment a filter is ticked; hidden while nothing is. */ ?>
				<a class="sjpta-filters__clear" href="<?php echo esc_url( $sjpta_base ); ?>" data-filter-clear<?php echo $sjpta_on ? '' : ' hidden'; ?>><?php esc_html_e( 'Clear filters', 'sjptheatrearts' ); ?></a>
			</p>
		</form>
	</div>
</section>
