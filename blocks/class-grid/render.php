<?php
/**
 * The class list.
 *
 * Renders whatever the filters in the URL select. The filter bar is a separate
 * block, because the design puts it under the hero and this under the age
 * routes; they meet through the URL, and through `data-class-list` once the
 * enhancement script is running.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label   = sjpta_field( 'label', __( 'All classes', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_cta     = sjpta_field( 'cta_label', '', $sjpta_ctx );
$sjpta_cta_url = sjpta_link_or_page( sjpta_field( 'cta_url', '', $sjpta_ctx ), 'join' );

$sjpta_help_title = sjpta_field( 'help_title', '', $sjpta_ctx );
$sjpta_help_text  = sjpta_field( 'help_text', '', $sjpta_ctx );

$sjpta_posts = sjpta_query_classes( sjpta_class_filters() )['posts'];
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-classlist alignfull">
	<div class="sjpta-inner sjpta-classlist__inner">

		<?php
		/*
		 * Heading only, no visible section bar. The bar repeated what the filter
		 * bar directly above it already says, and its button repeated the help
		 * card at the end of the grid. It stays as a screen-reader heading so the
		 * card H3s have a parent in the outline rather than hanging off the
		 * filters.
		 */
		?>
		<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_label ); ?></h2>

		<div class="sjpta-classlist__grid" data-reveal>
			<div class="sjpta-classlist__cards" data-stagger data-class-list>
				<?php
				sjpta_render_class_cards(
					$sjpta_posts,
					array(
						'help_title' => $sjpta_help_title,
						'help_text'  => $sjpta_help_text,
						'cta_label'  => $sjpta_cta,
						'cta_url'    => $sjpta_cta_url,
					)
				);
				?>
			</div>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
