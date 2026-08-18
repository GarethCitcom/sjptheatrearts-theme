<?php
/**
 * Gallery mosaic.
 *
 * Four columns on a fixed row height, with tiles that can span two columns, two
 * rows, or both. Built generic rather than for this page: the Performances
 * design uses the identical grid.
 *
 * The photographs show real students, so alt text comes from the media library
 * and the section carries the consent note the design puts under it. That note
 * is a field, not hard-coded, because it is a statement about how the school
 * handles permissions.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_eyebrow = sjpta_field( 'eyebrow', '', $sjpta_ctx );
$sjpta_heading = sjpta_field( 'heading', '', $sjpta_ctx );
$sjpta_label   = sjpta_field( 'link_label', '', $sjpta_ctx );
$sjpta_url     = sjpta_field( 'link_url', '', $sjpta_ctx );
$sjpta_note    = sjpta_field( 'note', '', $sjpta_ctx );

if ( '' === $sjpta_heading ) {
	return;
}

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_photos = $sjpta_has_acf ? get_field( 'photos', $sjpta_ctx ) : array();
$sjpta_photos = is_array( $sjpta_photos ) ? $sjpta_photos : array();

/*
 * A wide or large tile is drawn at roughly twice the width of a plain one, so it
 * gets its own `sizes` rather than every tile downloading for the worst case.
 */
$sjpta_sizes = array(
	'normal' => '(max-width: 640px) 45vw, (max-width: 1023px) 30vw, 310px',
	'tall'   => '(max-width: 640px) 45vw, (max-width: 1023px) 30vw, 310px',
	'wide'   => '(max-width: 640px) 92vw, (max-width: 1023px) 62vw, 636px',
	'large'  => '(max-width: 640px) 92vw, (max-width: 1023px) 62vw, 636px',
);

/*
 * Born To Be heads this with an eyebrow and a heading; Performances puts it in a
 * section bar with a row of category filters. Same tiles either way.
 */
$sjpta_bar = ( 'bar' === sjpta_field( 'layout', 'heading', $sjpta_ctx ) );

/*
 * Categories are optional. A tile with no category is in every view, which is
 * what an editor expects of a photograph they never got round to labelling.
 */
$sjpta_tags = $sjpta_has_acf ? get_field( 'filters', $sjpta_ctx ) : array();
$sjpta_tags = is_array( $sjpta_tags ) ? $sjpta_tags : array();

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a public filter, not acting on it.
$sjpta_current = isset( $_GET['gallery'] ) ? sanitize_key( wp_unslash( $_GET['gallery'] ) ) : '';

$sjpta_slugs = array();

foreach ( $sjpta_tags as $sjpta_tag ) {
	$sjpta_slugs[] = sanitize_title( (string) ( $sjpta_tag['label'] ?? '' ) );
}

if ( '' !== $sjpta_current && ! in_array( $sjpta_current, $sjpta_slugs, true ) ) {
	$sjpta_current = '';
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-mosaic alignfull<?php echo $sjpta_bar ? ' sjpta-mosaic--bar' : ''; ?>">
	<div class="sjpta-inner sjpta-mosaic__inner">
		<?php if ( $sjpta_bar ) : ?>
			<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_heading ); ?></h2>

			<div class="sjpta-sectionbar sjpta-mosaic__bar">
				<div class="sjpta-sectionbar__text">
					<span class="sjpta-sectionbar__label">
						<span class="sjpta-accent" aria-hidden="true">+</span> <?php echo esc_html( $sjpta_heading ); ?>
					</span>
					<?php if ( '' !== $sjpta_eyebrow ) : ?>
						<span class="sjpta-sectionbar__note"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $sjpta_tags ) ) : ?>
					<?php
					/*
					 * Real links to a filtered view of this page, so the filter
					 * works with no JavaScript at all. The script below turns them
					 * into an in-place filter; without it they simply reload.
					 */
					?>
					<?php
					/*
					 * The pills are built once and printed twice: a row for
					 * desktop and a <details> dropdown for phones, where the
					 * row wraps to three lines. Only one is ever displayed
					 * (primitives.css), so only one is in the accessibility
					 * tree — the same deliberate duplication as the header's
					 * two nav lists. The summary shows the server's idea of
					 * the current category, so a filtered page reload reads
					 * correctly with no JavaScript at all.
					 */
					$sjpta_current_label = __( 'All', 'sjptheatrearts' );

					ob_start();
					?>
					<a class="sjpta-daypill<?php echo '' === $sjpta_current ? ' is-current' : ''; ?>" href="<?php echo esc_url( remove_query_arg( 'gallery' ) ); ?>#gallery" data-tag="all" data-label="<?php esc_attr_e( 'All', 'sjptheatrearts' ); ?>">
						<?php esc_html_e( 'All', 'sjptheatrearts' ); ?>
					</a>
					<?php foreach ( $sjpta_tags as $sjpta_tag ) : ?>
						<?php
						$sjpta_name = trim( (string) ( $sjpta_tag['label'] ?? '' ) );

						if ( '' === $sjpta_name ) {
							continue;
						}

						$sjpta_slug = sanitize_title( $sjpta_name );

						if ( $sjpta_current === $sjpta_slug ) {
							$sjpta_current_label = $sjpta_name;
						}
						?>
						<a
							class="sjpta-daypill<?php echo $sjpta_current === $sjpta_slug ? ' is-current' : ''; ?>"
							href="<?php echo esc_url( add_query_arg( 'gallery', $sjpta_slug ) ); ?>#gallery"
							data-tag="<?php echo esc_attr( $sjpta_slug ); ?>"
							data-label="<?php echo esc_attr( $sjpta_name ); ?>"
						><?php echo esc_html( $sjpta_name ); ?></a>
					<?php endforeach; ?>
					<?php $sjpta_pills = (string) ob_get_clean(); ?>
					<nav class="sjpta-mosaic__filters" aria-label="<?php esc_attr_e( 'Filter the gallery', 'sjptheatrearts' ); ?>" data-gallery-filter>
						<div class="sjpta-filterrow">
							<?php echo $sjpta_pills; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped where built above. ?>
						</div>

						<details class="sjpta-filterdrop">
							<summary class="sjpta-filterdrop__toggle">
								<span data-filter-label><?php echo esc_html( $sjpta_current_label ); ?></span>
								<span class="sjpta-filterdrop__chevron" aria-hidden="true">
									<?php echo sjpta_icon( 'chevron-down', 14, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								</span>
							</summary>
							<div class="sjpta-filterdrop__panel">
								<?php echo $sjpta_pills; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped where built above. ?>
							</div>
						</details>
					</nav>
				<?php endif; ?>
			</div>
		<?php else : ?>
		<div class="sjpta-mosaic__head">
			<div>
				<?php if ( '' !== $sjpta_eyebrow ) : ?>
					<span class="sjpta-eyebrow sjpta-mosaic__eyebrow"><?php echo esc_html( $sjpta_eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="sjpta-mosaic__heading"><?php echo esc_html( $sjpta_heading ); ?></h2>
			</div>

			<?php if ( '' !== $sjpta_label && '' !== $sjpta_url ) : ?>
				<a class="sjpta-mosaic__link" href="<?php echo esc_url( $sjpta_url ); ?>"<?php echo sjpta_external_attr( $sjpta_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
					<?php echo esc_html( $sjpta_label ); ?>
					<?php echo sjpta_new_tab_note( $sjpta_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
					<?php echo sjpta_icon( 'chevron-right', 12, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
				</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $sjpta_photos ) ) : ?>
			<div class="sjpta-mosaic__grid" data-reveal>
				<div class="sjpta-mosaic__tiles" data-stagger data-lightbox>
					<?php foreach ( $sjpta_photos as $sjpta_photo ) : ?>
						<?php
						$sjpta_id = isset( $sjpta_photo['photo'] ) ? (int) $sjpta_photo['photo'] : 0;

						if ( ! $sjpta_id ) {
							continue;
						}

						$sjpta_span = isset( $sjpta_photo['span'] ) ? (string) $sjpta_photo['span'] : 'normal';
						$sjpta_size = 'normal' === $sjpta_span || 'tall' === $sjpta_span ? 'sjpta-480' : 'sjpta-640';
						$sjpta_tag  = sanitize_title( (string) ( $sjpta_photo['tag'] ?? '' ) );

						/*
						 * Filtered here, not hidden with CSS: a tile that is not in
						 * the chosen view is not in the page at all, so it cannot be
						 * reached by tabbing or announced by a screen reader.
						 */
						if ( '' !== $sjpta_current && '' !== $sjpta_tag && $sjpta_current !== $sjpta_tag ) {
							continue;
						}
						?>
						<figure class="sjpta-mosaic__tile sjpta-mosaic__tile--<?php echo esc_attr( $sjpta_span ); ?>"<?php echo '' !== $sjpta_tag ? ' data-tag="' . esc_attr( $sjpta_tag ) . '"' : ''; ?>>
							<?php
							sjpta_gallery_link(
								$sjpta_id,
								$sjpta_size,
								$sjpta_sizes[ $sjpta_span ] ?? $sjpta_sizes['normal']
							);
							?>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $sjpta_note ) : ?>
			<p class="sjpta-mosaic__note"><?php echo esc_html( $sjpta_note ); ?></p>
		<?php endif; ?>
	</div>
</section>
