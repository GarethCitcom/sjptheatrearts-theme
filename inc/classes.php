<?php
/**
 * The class post type and its taxonomies.
 *
 * Classes are content, not pages: they are listed, filtered, counted and related
 * to each other, and the count drives copy on three other templates. Modelling
 * them as posts is what lets the homepage stat, the "All N classes" button and
 * the age-route counts follow the data instead of being typed in.
 *
 * URLs: the Classes page stays a real editable page at /classes/, and single
 * classes sit beneath it at /classes/<slug>/. WordPress resolves the bare path
 * to the page and the deeper one to the post, so the client keeps a page they can
 * edit while the grid on it stays dynamic.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SJPTA_CLASS_POST_TYPE = 'sjp-class';

/**
 * Register the class post type.
 *
 * @return void
 */
function sjpta_register_class_post_type(): void {
	register_post_type(
		SJPTA_CLASS_POST_TYPE,
		array(
			'labels'          => array(
				'name'               => __( 'Classes', 'sjptheatrearts' ),
				'singular_name'      => __( 'Class', 'sjptheatrearts' ),
				'menu_name'          => __( 'Classes', 'sjptheatrearts' ),
				'add_new_item'       => __( 'Add class', 'sjptheatrearts' ),
				'edit_item'          => __( 'Edit class', 'sjptheatrearts' ),
				'search_items'       => __( 'Search classes', 'sjptheatrearts' ),
				'not_found'          => __( 'No classes yet.', 'sjptheatrearts' ),
				'featured_image'     => __( 'Class photograph', 'sjptheatrearts' ),
				'set_featured_image' => __( 'Set class photograph', 'sjptheatrearts' ),
			),
			'public'          => true,
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-groups',
			'menu_position'   => 25,

			/*
			 * No 'editor' on purpose. A class has no free-form content: every
			 * word of it lives in the field group. Supporting the editor gave
			 * this post type a full-height empty block canvas with the fifty
			 * fields that actually matter squashed into a collapsed meta-box
			 * drawer at the bottom of the screen, which reads as a blank page.
			 *
			 * Without it WordPress uses the classic edit screen and ACF renders
			 * the fields full width, which is the right shape for content that
			 * is entirely structured.
			 */
			'supports'        => array( 'title', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'has_archive'     => false,
			'rewrite'         => array(
				'slug'       => 'classes',
				'with_front' => false,
			),
			'capability_type' => 'page',
			'map_meta_cap'    => true,
		)
	);

	register_taxonomy(
		'discipline',
		SJPTA_CLASS_POST_TYPE,
		array(
			'labels'            => array(
				'name'          => __( 'Disciplines', 'sjptheatrearts' ),
				'singular_name' => __( 'Discipline', 'sjptheatrearts' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'discipline' ),
		)
	);

	register_taxonomy(
		'age-group',
		SJPTA_CLASS_POST_TYPE,
		array(
			'labels'            => array(
				'name'          => __( 'Age groups', 'sjptheatrearts' ),
				'singular_name' => __( 'Age group', 'sjptheatrearts' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'ages' ),
		)
	);
}
add_action( 'init', 'sjpta_register_class_post_type' );

/**
 * The four age routes, in the order the design shows them.
 *
 * Seeded on first run so the taxonomy is never an empty box an editor has to
 * guess at, and so the route cards have something to count.
 *
 * @return array<string,array<string,string>>
 */
function sjpta_age_routes(): array {
	return array(
		'first-steps' => array(
			'name'  => __( 'First Steps', 'sjptheatrearts' ),
			'range' => __( 'Ages 2 to 4', 'sjptheatrearts' ),
			'short' => __( '2 to 4', 'sjptheatrearts' ),
			'tone'  => 'orange',
			'icon'  => 'toddler',
		),
		'children'    => array(
			'name'  => __( 'Children', 'sjptheatrearts' ),
			'range' => __( 'Ages 4 to 10', 'sjptheatrearts' ),
			'short' => __( '4 to 10', 'sjptheatrearts' ),
			'tone'  => 'magenta',
			'icon'  => 'mic',
		),
		'teens'       => array(
			'name'  => __( 'Teens', 'sjptheatrearts' ),
			'range' => __( 'Ages 11 to 18', 'sjptheatrearts' ),
			'short' => __( '11 to 18', 'sjptheatrearts' ),
			'tone'  => 'purple',
			'icon'  => 'dance',
		),
		'adults'      => array(
			'name'  => __( 'Adults', 'sjptheatrearts' ),
			'range' => __( '18 and over', 'sjptheatrearts' ),
			'short' => __( 'Adults', 'sjptheatrearts' ),
			'tone'  => 'green',
			'icon'  => 'people',
		),
	);
}

/**
 * How many classes are published.
 *
 * The single source for every "N classes" on the site. Nothing hard-codes the
 * number: the design says thirteen, the client's answers describe fifteen, and
 * either could change again.
 *
 * @param string $taxonomy Optional taxonomy to count within.
 * @param string $term     Optional term slug.
 *
 * @return int
 */
function sjpta_class_count( string $taxonomy = '', string $term = '' ): int {
	$args = array(
		'post_type'      => SJPTA_CLASS_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => false,
	);

	if ( '' !== $taxonomy && '' !== $term ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- a taxonomy count is the point of the query.
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term,
			),
		);
	}

	$query = new WP_Query( $args );

	return (int) $query->post_count;
}

/**
 * Read the filter state from the URL.
 *
 * One reader, used by the filter bar, the list and the REST endpoint, so the
 * count in the bar can never disagree with the cards below it.
 *
 * @return array<string,string>
 */
function sjpta_class_filters(): array {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- reading public filter state from the URL, not acting on it.
	return array(
		'age'   => isset( $_GET['age'] ) ? sanitize_key( wp_unslash( $_GET['age'] ) ) : '',
		'style' => isset( $_GET['style'] ) ? sanitize_key( wp_unslash( $_GET['style'] ) ) : '',
		'day'   => isset( $_GET['day'] ) ? sanitize_text_field( wp_unslash( $_GET['day'] ) ) : '',
	);
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
}

/**
 * The classes matching a set of filters, and every day any class runs on.
 *
 * Day is matched in PHP rather than in SQL. Days live in an ACF repeater, so
 * matching them in the query would mean a meta query per row index against a
 * serialised key. Fifteen classes do not justify that; revisit it at hundreds.
 *
 * @param array<string,string> $filters age, style and day.
 *
 * @return array{posts:array<int,WP_Post>,days:array<int,string>}
 */
function sjpta_query_classes( array $filters ): array {
	$tax = array();

	if ( ! empty( $filters['age'] ) ) {
		$tax[] = array(
			'taxonomy' => 'age-group',
			'field'    => 'slug',
			'terms'    => $filters['age'],
		);
	}

	if ( ! empty( $filters['style'] ) ) {
		$tax[] = array(
			'taxonomy' => 'discipline',
			'field'    => 'slug',
			'terms'    => $filters['style'],
		);
	}

	$args = array(
		'post_type'      => SJPTA_CLASS_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => 60,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	);

	if ( ! empty( $tax ) ) {
		$args['tax_query'] = $tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- filtering the list is the point.
	}

	$query = new WP_Query( $args );
	$posts = array();
	$days  = array();

	foreach ( $query->posts as $post ) {
		$rows = sjpta_get_field( 'days', $post->ID );
		$list = array();

		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$value = trim( (string) ( $row['text'] ?? '' ) );

			if ( '' !== $value ) {
				$list[]         = $value;
				$days[ $value ] = true;
			}
		}

		if ( ! empty( $filters['day'] ) && ! in_array( $filters['day'], $list, true ) ) {
			continue;
		}

		$posts[] = $post;
	}

	ksort( $days );

	return array(
		'posts' => $posts,
		'days'  => array_keys( $days ),
	);
}

/**
 * The inside of the card grid: every card, the empty state, and the help card.
 *
 * Its own function because the REST endpoint returns exactly this markup, so the
 * filtered view a visitor sees is rendered by the same code as the first load.
 * Two renderers would drift.
 *
 * @param array<int,WP_Post>   $posts The classes to show.
 * @param array<string,string> $help  Help-card copy: help_title, help_text,
 *                                    cta_label, cta_url.
 *
 * @return void
 */
function sjpta_render_class_cards( array $posts, array $help = array() ): void {
	if ( empty( $posts ) ) {
		printf(
			'<p class="sjpta-classlist__empty">%s</p>',
			esc_html__( 'No classes match that combination. Try clearing a filter, or ask us and we will point you at the right one.', 'sjptheatrearts' )
		);
	}

	foreach ( $posts as $post ) {
		sjpta_class_card( $post );
	}

	$title = trim( (string) ( $help['help_title'] ?? '' ) );

	if ( '' === $title ) {
		return;
	}
	?>
	<article class="sjpta-helpcard">
		<span class="sjpta-helpcard__icon" aria-hidden="true">
			<?php echo sjpta_icon( 'question', 24, 'var(--wp--preset--color--magenta)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
		</span>

		<h3 class="sjpta-helpcard__title"><?php echo esc_html( $title ); ?></h3>

		<?php if ( '' !== trim( (string) ( $help['help_text'] ?? '' ) ) ) : ?>
			<p class="sjpta-helpcard__text"><?php echo esc_html( (string) $help['help_text'] ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== trim( (string) ( $help['cta_label'] ?? '' ) ) ) : ?>
			<a class="sjpta-btn sjpta-btn--plum sjpta-helpcard__cta" href="<?php echo esc_url( (string) ( $help['cta_url'] ?? '' ) ); ?>">
				<?php echo esc_html( (string) $help['cta_label'] ); ?>
			</a>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * One class card.
 *
 * Shared by the class list and by the related-classes row on a class page, so
 * the two can never drift apart.
 *
 * Fees are the honest placeholder rather than a number: none of the fifteen
 * classes has a confirmed fee, and inventing one on a page a parent budgets from
 * would be the worst kind of guess.
 *
 * @param WP_Post $post The class.
 *
 * @return void
 */
function sjpta_class_card( WP_Post $post ): void {
	$blurb = (string) sjpta_get_field( 'blurb', $post->ID );
	$tags  = sjpta_get_field( 'tags', $post->ID );
	$tags  = is_array( $tags ) ? $tags : array();
	$fees  = trim( (string) sjpta_get_field( 'fees', $post->ID ) );

	// The first tag is the age label, which the design shows over the photograph.
	$badge = isset( $tags[0]['text'] ) ? (string) $tags[0]['text'] : '';
	$chips = array_slice( $tags, 1, 2 );
	?>
	<article class="sjpta-classitem" data-lift>
		<div class="sjpta-classitem__media">
			<?php if ( has_post_thumbnail( $post ) ) : ?>
				<?php
				echo get_the_post_thumbnail(
					$post,
					'sjpta-640',
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 640px) 92vw, (max-width: 1023px) 45vw, 400px',
					)
				);
				?>
			<?php endif; ?>

			<?php if ( '' !== $badge ) : ?>
				<span class="sjpta-classitem__badge"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
		</div>

		<div class="sjpta-classitem__body">
			<h3 class="sjpta-classitem__title">
				<a href="<?php echo esc_url( (string) get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
			</h3>

			<?php if ( '' !== $blurb ) : ?>
				<p class="sjpta-classitem__text"><?php echo esc_html( $blurb ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $chips ) ) : ?>
				<ul class="sjpta-classitem__chips">
					<?php foreach ( $chips as $chip ) : ?>
						<?php $text = trim( (string) ( $chip['text'] ?? '' ) ); ?>
						<?php if ( '' !== $text ) : ?>
							<li><?php echo esc_html( $text ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="sjpta-classitem__foot">
				<span class="<?php echo '' === $fees ? 'sjpta-toconfirm' : 'sjpta-classitem__fees'; ?>">
					<?php echo esc_html( '' === $fees ? __( 'Ask us about fees', 'sjptheatrearts' ) : $fees ); ?>
				</span>

				<span class="sjpta-classitem__more" aria-hidden="true">
					<?php esc_html_e( 'Details', 'sjptheatrearts' ); ?>
					<span class="sjpta-classitem__arrow">
						<?php echo sjpta_icon( 'arrow-right', 12, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
					</span>
				</span>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Spell a small number, so copy reads as writing rather than as data.
 *
 * "Fifteen class styles" is a sentence; "15 class styles" is a readout. Falls
 * back to the numeral above twenty, where words start to get in the way.
 *
 * @param int $number Number to spell.
 *
 * @return string
 */
function sjpta_spell_number( int $number ): string {
	$words = array(
		1  => __( 'One', 'sjptheatrearts' ),
		2  => __( 'Two', 'sjptheatrearts' ),
		3  => __( 'Three', 'sjptheatrearts' ),
		4  => __( 'Four', 'sjptheatrearts' ),
		5  => __( 'Five', 'sjptheatrearts' ),
		6  => __( 'Six', 'sjptheatrearts' ),
		7  => __( 'Seven', 'sjptheatrearts' ),
		8  => __( 'Eight', 'sjptheatrearts' ),
		9  => __( 'Nine', 'sjptheatrearts' ),
		10 => __( 'Ten', 'sjptheatrearts' ),
		11 => __( 'Eleven', 'sjptheatrearts' ),
		12 => __( 'Twelve', 'sjptheatrearts' ),
		13 => __( 'Thirteen', 'sjptheatrearts' ),
		14 => __( 'Fourteen', 'sjptheatrearts' ),
		15 => __( 'Fifteen', 'sjptheatrearts' ),
		16 => __( 'Sixteen', 'sjptheatrearts' ),
		17 => __( 'Seventeen', 'sjptheatrearts' ),
		18 => __( 'Eighteen', 'sjptheatrearts' ),
		19 => __( 'Nineteen', 'sjptheatrearts' ),
		20 => __( 'Twenty', 'sjptheatrearts' ),
	);

	return $words[ $number ] ?? (string) $number;
}
