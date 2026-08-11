<?php
/**
 * Other classes students of this one also take.
 *
 * Chosen by shared discipline first, then shared age route, so "Jazz &
 * Commercial" suggests other dance classes for the same ages rather than three
 * arbitrary posts. Falls back to filling from the same age route if there are
 * not enough matches, and shows nothing at all rather than padding with
 * unrelated classes.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_id = get_the_ID();

if ( ! $sjpta_id || SJPTA_CLASS_POST_TYPE !== get_post_type( $sjpta_id ) ) {
	return;
}

$sjpta_styles = wp_get_object_terms( $sjpta_id, 'discipline', array( 'fields' => 'ids' ) );
$sjpta_ages   = wp_get_object_terms( $sjpta_id, 'age-group', array( 'fields' => 'ids' ) );

/**
 * Classes sharing terms with this one.
 *
 * @param string         $taxonomy Taxonomy to match on.
 * @param array<int,int> $terms    Term ids.
 * @param array<int,int> $exclude  Ids already shown.
 * @param int            $limit    How many to return.
 * @param mixed          $ages     Age terms to narrow by as well, when given.
 *
 * @return array<int,WP_Post>
 */
$sjpta_similar = static function ( string $taxonomy, $terms, array $exclude, int $limit, $ages = null ): array {
	if ( ! is_array( $terms ) || empty( $terms ) || $limit < 1 ) {
		return array();
	}

	$tax = array(
		array(
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => $terms,
		),
	);

	if ( is_array( $ages ) && ! empty( $ages ) ) {
		$tax[] = array(
			'taxonomy' => 'age-group',
			'field'    => 'term_id',
			'terms'    => $ages,
		);
	}

	$query = new WP_Query(
		array(
			'post_type'      => SJPTA_CLASS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'post__not_in'   => $exclude,
			'orderby'        => 'rand',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- matching on shared terms is the point of the block, over a set of fifteen posts.
			'tax_query'      => $tax,
		)
	);

	return $query->posts;
};

/*
 * Closest first: same discipline AND same age route. Jazz has eleven other dance
 * classes, so matching on discipline alone suggested Baby Yoga to a teenager.
 */
$sjpta_exclude = array( $sjpta_id );
$sjpta_related = $sjpta_similar( 'discipline', $sjpta_styles, $sjpta_exclude, 3, $sjpta_ages );

/*
 * Then the same ages, before the same discipline. Age is the stronger signal
 * when the exact match runs out: Baby Yoga shares "fitness" with Acro & Cheer,
 * and suggesting a teenagers' tumbling class to the parent of a baby is worse
 * than suggesting another class for babies.
 */
if ( count( $sjpta_related ) < 3 ) {
	$sjpta_exclude = array_merge( $sjpta_exclude, wp_list_pluck( $sjpta_related, 'ID' ) );
	$sjpta_related = array_merge(
		$sjpta_related,
		$sjpta_similar( 'age-group', $sjpta_ages, $sjpta_exclude, 3 - count( $sjpta_related ) )
	);
}

if ( count( $sjpta_related ) < 3 ) {
	$sjpta_exclude = array_merge( $sjpta_exclude, wp_list_pluck( $sjpta_related, 'ID' ) );
	$sjpta_related = array_merge(
		$sjpta_related,
		$sjpta_similar( 'discipline', $sjpta_styles, $sjpta_exclude, 3 - count( $sjpta_related ) )
	);
}

if ( empty( $sjpta_related ) ) {
	return;
}

$sjpta_classes_page = get_page_by_path( 'classes' );
$sjpta_classes_url  = $sjpta_classes_page ? (string) get_permalink( $sjpta_classes_page ) : home_url( '/classes/' );
?>
<section<?php echo sjpta_anchor_attr( $attributes ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-related alignfull">
	<div class="sjpta-inner">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Students who take this also take', 'sjptheatrearts' ); ?></h2>

		<?php
		sjpta_section_bar(
			__( 'Students who take this also take', 'sjptheatrearts' ),
			'',
			__( 'All classes', 'sjptheatrearts' ),
			$sjpta_classes_url
		);
		?>

		<div class="sjpta-related__grid" data-reveal>
			<div class="sjpta-related__cards" data-stagger>
				<?php
				foreach ( $sjpta_related as $sjpta_post ) {
					sjpta_class_card( $sjpta_post );
				}
				?>
			</div>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
