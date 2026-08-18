<?php
/**
 * Block registration.
 *
 * Every folder in blocks/ that contains a block.json is registered. Blocks are
 * PHP-rendered; none of them ship a frontend script unless the design genuinely
 * needs one.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register every block in blocks/.
 *
 * @return void
 */
function sjpta_register_blocks(): void {
	$dir = SJPTA_DIR . '/blocks';

	if ( ! is_dir( $dir ) ) {
		return;
	}

	$folders = glob( $dir . '/*', GLOB_ONLYDIR );

	if ( empty( $folders ) ) {
		return;
	}

	foreach ( $folders as $folder ) {
		if ( ! file_exists( $folder . '/block.json' ) ) {
			continue;
		}

		register_block_type( $folder );
	}
}
add_action( 'init', 'sjpta_register_blocks' );

/**
 * Render an inline SVG icon from the theme's small icon set.
 *
 * Icons are inlined rather than sprited: there are few of them, they are tiny,
 * and inlining avoids a request and a flash of missing chrome. Output is fixed
 * markup defined here, never user input.
 *
 * @param string $name   Icon name.
 * @param int    $size   Pixel size for width and height.
 * @param string $colour Stroke colour (CSS colour or var()).
 * @param string $accent Second colour, for the few icons drawn in two. Falls
 *                       back to $colour, so a caller can ignore it.
 *
 * @return string Safe SVG markup, or an empty string for an unknown icon.
 */
function sjpta_icon( string $name, int $size = 16, string $colour = 'currentColor', string $accent = '' ): string {
	$paths = array(
		'arrow-right'    => '<path d="M3 8h10m-4-4 4 4-4 4" stroke="%1$s" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'arrow-up'       => '<path d="M8 13V3m-4 4 4-4 4 4" stroke="%1$s" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'arrow-up-right' => '<path d="M4.5 11.5 11.5 4.5M6 4.5h5.5V10" stroke="%1$s" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'lock'           => '<rect x="2.5" y="7" width="11" height="7" rx="1.8" stroke="%1$s" stroke-width="1.6"/><path d="M5.2 7V5.2a2.8 2.8 0 0 1 5.6 0V7" stroke="%1$s" stroke-width="1.6"/>',
		'close'          => '<path d="m4 4 8 8m0-8-8 8" stroke="%1$s" stroke-width="2" stroke-linecap="round"/>',

		// Added for Born To Be. Same 16-unit grid and stroke weight as above.
		'check'          => '<path d="m3.5 8.5 3 3 6-6.5" stroke="%1$s" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'chevron-right'  => '<path d="m6 3.5 5 4.5-5 4.5" stroke="%1$s" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'chevron-down'   => '<path d="m3.5 6 4.5 5 4.5-5" stroke="%1$s" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		// %2$s is the optional second colour; it falls back to %1$s when unset.
		'shield-tick'    => '<path d="M8 1.6 13.2 3.6v4.1c0 3.2-2.1 5.6-5.2 6.7-3.1-1.1-5.2-3.5-5.2-6.7V3.6L8 1.6Z" stroke="%1$s" stroke-width="1.4" stroke-linejoin="round"/><path d="m5.8 7.9 1.6 1.6 3-3.3" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
		'mic'            => '<rect x="6" y="1.6" width="4" height="7.6" rx="2" stroke="%1$s" stroke-width="1.4"/><path d="M3.6 7.6a4.4 4.4 0 0 0 8.8 0M8 12v2.4" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		'dance'          => '<circle cx="9.4" cy="2.9" r="1.5" stroke="%1$s" stroke-width="1.3"/><path d="M9.6 5.6 7.2 8.2l2 2 .6 3.9M7.2 8.2 4 7.3m5.2 2.9-2.9 3.9m5.9-8.1-2.6-.4" stroke="%1$s" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
		'script'         => '<path d="M4 2.2h6.2l2.3 2.3v9.3H4V2.2Z" stroke="%1$s" stroke-width="1.4" stroke-linejoin="round"/><path d="M6.2 6.4h4M6.2 9h4M6.2 11.4h2.4" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		'backstage'      => '<path d="M2.4 3.2h11.2M4.2 3.2v8.4M11.8 3.2v8.4" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/><path d="M4.2 11.6c1.6-1.2 2.6-3.4 2.6-5.6M11.8 11.6c-1.6-1.2-2.6-3.4-2.6-5.6" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		'instagram'      => '<rect x="2.4" y="2.4" width="11.2" height="11.2" rx="3.4" stroke="%1$s" stroke-width="1.4"/><circle cx="8" cy="8" r="2.8" stroke="%1$s" stroke-width="1.4"/><circle cx="11.3" cy="4.7" r="0.9" fill="%1$s"/>',
		'facebook'       => '<path d="M9.6 14.4V8.7h1.9l.3-2.2H9.6V5.1c0-.6.2-1.1 1.1-1.1h1.2V2.1C11.6 2 10.9 2 10.1 2 8.4 2 7.3 3 7.3 4.9v1.6H5.4v2.2h1.9v5.7h2.3Z" fill="%1$s"/>',
		'mail'           => '<rect x="2.2" y="3.6" width="11.6" height="8.8" rx="1.6" stroke="%1$s" stroke-width="1.4"/><path d="m2.8 4.6 5.2 4 5.2-4" stroke="%1$s" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',

		// Added for the age routes on the Classes page.
		'toddler'        => '<circle cx="8" cy="4.3" r="2" fill="%1$s"/><path d="M4.3 13.6c0-2.4 1.7-4 3.7-4s3.7 1.6 3.7 4" stroke="%1$s" stroke-width="1.5" stroke-linecap="round"/>',
		'question'       => '<circle cx="8" cy="8" r="5.7" stroke="%1$s" stroke-width="1.4"/><path d="M6.4 6.3a1.7 1.7 0 0 1 3.2.6c0 1.1-1.6 1.3-1.6 2.3" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/><circle cx="8" cy="11.4" r="0.8" fill="%1$s"/>',
		// About: what the school says it will not compromise on.
		'heart'          => '<path d="M8 13.6s-4.7-3-4.7-6.3A2.6 2.6 0 0 1 8 5.4a2.6 2.6 0 0 1 4.7 1.9c0 3.2-4.7 6.3-4.7 6.3Z" fill="%1$s"/>',
		'cap'            => '<path d="M8 2.4 14.4 5.4 8 8.4 1.6 5.4 8 2.4Z" stroke="%1$s" stroke-width="1.4" stroke-linejoin="round"/><path d="M4.3 6.9v3c0 1.1 1.7 2.1 3.7 2.1s3.7-1 3.7-2.1v-3" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		'trophy'         => '<path d="M4.4 2.7h7.2v4a3.6 3.6 0 0 1-7.2 0v-4Z" stroke="%1$s" stroke-width="1.4" stroke-linejoin="round"/><path d="M8 10.7v2.6M5.7 13.3h4.6" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		// Footer: the contact list and the third social account.
		'pin'            => '<path d="M8 1.5c-2.5 0-4.5 2-4.5 4.5C3.5 9.5 8 14.5 8 14.5S12.5 9.5 12.5 6c0-2.5-2-4.5-4.5-4.5Z" stroke="%1$s" stroke-width="1.4"/><circle cx="8" cy="6" r="1.6" fill="%1$s"/>',
		'tiktok'         => '<path d="M9.8 2.1h2c.2 1.5 1.1 2.5 2.6 2.6v2c-1 0-1.8-.3-2.6-.8v4.2a3.9 3.9 0 1 1-3.9-3.9c.2 0 .4 0 .6.1v2.1a1.8 1.8 0 1 0 1.3 1.7V2.1Z" fill="%1$s"/>',
		// Timetable and fees: term dates, half term, the fee cards.
		'calendar'       => '<rect x="2.3" y="3.3" width="11.4" height="10" rx="2" stroke="%1$s" stroke-width="1.4"/><path d="M2.3 6.3h11.4M5.3 2.3v2M10.7 2.3v2" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		'clock'          => '<circle cx="8" cy="8" r="5.7" stroke="%1$s" stroke-width="1.4"/><path d="M8 5v3l2 1.3" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		'coins'          => '<circle cx="8" cy="8" r="5.7" stroke="%1$s" stroke-width="1.4"/><path d="M9.6 6.1a1.9 1.9 0 0 0-3.2 1.3c0 1.7 3.2 1 3.2 2.7a1.9 1.9 0 0 1-3.2 1.1M8 4.3v7.4" stroke="%1$s" stroke-width="1.3" stroke-linecap="round"/>',
		'card'           => '<rect x="2.3" y="3.6" width="11.4" height="8.8" rx="2" stroke="%1$s" stroke-width="1.4"/><path d="M2.3 6.6h11.4" stroke="%1$s" stroke-width="1.4"/><path d="M4.9 9.6h2.6" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
		'people'         => '<circle cx="5.7" cy="4.7" r="1.8" fill="%1$s"/><circle cx="10.4" cy="4.7" r="1.8" fill="%1$s"/><path d="M2.6 13c0-2 1.4-3.4 3.1-3.4S8.8 11 8.8 13M8.8 13c0-2 1.4-3.4 3.1-3.4s2.5 1.4 2.5 3.4" stroke="%1$s" stroke-width="1.4" stroke-linecap="round"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="sjpta-icon" width="%1$d" height="%1$d" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">%2$s</svg>',
		$size,
		sprintf( $paths[ $name ], esc_attr( $colour ), esc_attr( '' === $accent ? $colour : $accent ) )
	);
}

/**
 * Read an ACF field in the current context, falling back to a default.
 *
 * The fallback is the design's own copy, so a section renders correctly before
 * anyone has touched it. Fallbacks are only ever used for wording taken from the
 * approved design — never for a fact awaiting client confirmation, which must
 * render its empty state instead.
 *
 * The context must be passed explicitly — for an ACF block that is `$block['id']`.
 * Calling get_field() with no context inside a block render raises a PHP 8.3
 * deprecation inside ACF whenever the block carries no saved data, which is the
 * normal state for a block inserted programmatically rather than in the editor.
 *
 * @param string      $name     Field name.
 * @param string      $fallback Used when ACF is absent, the context is missing,
 *                              or the field is empty.
 * @param string|null $context  ACF context: a block id, post id, or 'option'.
 *
 * @return string
 */
function sjpta_field( string $name, string $fallback = '', ?string $context = null ): string {
	if ( ! function_exists( 'get_field' ) || null === $context || '' === $context ) {
		return $fallback;
	}

	$value = get_field( $name, $context );

	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return $fallback;
	}

	return trim( $value );
}

/**
 * The ACF context for the block currently rendering.
 *
 * @param mixed $block The $block array ACF hands to a render template.
 *
 * @return string|null Block id, or null when the block carries no ACF data.
 */
function sjpta_block_context( $block ): ?string {
	if ( is_array( $block ) && isset( $block['id'] ) && is_string( $block['id'] ) && '' !== $block['id'] ) {
		return $block['id'];
	}

	return null;
}

/**
 * Escape a heading and wrap one phrase in the accent colour.
 *
 * The designs set a single word or phrase of most headings in orange — "to
 * shine.", "Train your way.", "Find the right class". Rather than ask the client
 * to write HTML, they type the heading and, separately, the phrase to accent.
 *
 * Everything is escaped first; the only markup introduced is our own <span> and
 * the <br> for an author's line break.
 *
 * @param string $text      Heading text, newlines allowed.
 * @param string $highlight Phrase within it to accent. Case-sensitive.
 *
 * @return string Safe HTML.
 */
function sjpta_highlight_words( string $text, string $highlight ): string {
	$safe = esc_html( $text );

	$highlight = trim( $highlight );

	if ( '' !== $highlight ) {
		$needle = esc_html( $highlight );

		// Replace the last occurrence only: the accent always closes the line,
		// and a word like "way" may legitimately appear earlier in the sentence.
		$position = strrpos( $safe, $needle );

		if ( false !== $position ) {
			$safe = substr( $safe, 0, $position )
				. '<span class="sjpta-accent">' . $needle . '</span>'
				. substr( $safe, $position + strlen( $needle ) );
		}
	}

	return nl2br( $safe, false );
}

/**
 * The white bar that introduces a homepage section.
 *
 * "+ About SJP · Professional training, family-school warmth · [Our story →]".
 * Used by About, The SJP experience and The team, so it lives here rather than
 * being written out three times.
 *
 * @param string $label      Section name, shown after an orange plus.
 * @param string $note       Muted supporting line.
 * @param string $link_label Pill button label. Omitted when empty.
 * @param string $link_url   Pill button destination.
 *
 * @return void
 */
function sjpta_section_bar( string $label, string $note, string $link_label, string $link_url ): void {
	if ( '' === $label && '' === $link_label ) {
		return;
	}
	?>
	<div class="sjpta-sectionbar">
		<div class="sjpta-sectionbar__text">
			<span class="sjpta-sectionbar__label">
				<span class="sjpta-accent" aria-hidden="true">+</span> <?php echo esc_html( $label ); ?>
			</span>
			<?php if ( '' !== $note ) : ?>
				<span class="sjpta-sectionbar__note"><?php echo esc_html( $note ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( '' !== $link_label ) : ?>
			<a class="sjpta-pillbtn" href="<?php echo esc_url( $link_url ); ?>">
				<?php echo esc_html( $link_label ); ?>
				<span class="sjpta-pillbtn__icon" aria-hidden="true">
					<?php echo sjpta_icon( 'arrow-right', 12, '#381064' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
				</span>
			</a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * The overlapping two-photo collage.
 *
 * A large photo with a smaller one overlapping its opposite corner, the smaller
 * carrying a page-coloured border so it reads as a separate print. Used by the
 * Born To Be hero and the Madison feature, which are mirror images of each
 * other, hence the $flip argument rather than two copies.
 *
 * Both photographs are decorative in the sense that the surrounding copy already
 * carries the meaning, but they show real students, so alt text comes from the
 * media library rather than being forced empty.
 *
 * @param int         $main     Attachment id for the large photo.
 * @param int         $inset    Attachment id for the smaller overlapping photo.
 * @param bool        $flip     Large photo on the left instead of the right.
 * @param string|null $card     Optional floating card markup, already escaped.
 * @param bool        $priority Above the fold: the large photo loads eagerly and
 *                              at high priority, because it is the LCP element.
 *
 * @return void
 */
function sjpta_photo_collage( int $main, int $inset, bool $flip = false, ?string $card = null, bool $priority = false ): void {
	if ( ! $main && ! $inset ) {
		return;
	}

	$classes = 'sjpta-collage' . ( $flip ? ' sjpta-collage--flip' : '' );
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<?php if ( $main ) : ?>
			<span class="sjpta-collage__main">
				<?php
				/*
				 * The large photo is 76% of its column, not the full viewport.
				 * Saying 100vw made the browser pick a candidate roughly four
				 * times wider than it draws, which cost the page its LCP.
				 */
				$sjpta_main_attr = array(
					'loading' => $priority ? 'eager' : 'lazy',
					'sizes'   => '(max-width: 1023px) 76vw, 460px',
				);

				if ( $priority ) {
					$sjpta_main_attr['fetchpriority'] = 'high';
				}

				echo wp_get_attachment_image( $main, 'large', false, $sjpta_main_attr );
				?>
			</span>
		<?php endif; ?>

		<?php if ( $inset ) : ?>
			<span class="sjpta-collage__inset">
				<?php
				echo wp_get_attachment_image(
					$inset,
					'medium_large',
					false,
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 1023px) 56vw, 330px',
					)
				);
				?>
			</span>
		<?php endif; ?>

		<?php if ( null !== $card && '' !== $card ) : ?>
			<span class="sjpta-collage__card">
				<?php echo wp_kses_post( $card ); ?>
			</span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * A gallery thumbnail, wrapped in a link to its full-size file.
 *
 * The link is the point. assets/js/lightbox.js intercepts the click and shows
 * the photograph in place, but with the script blocked, failed or disabled the
 * link still does the useful thing: it opens the photograph at full size. A
 * lightbox built on a click handler alone would simply do nothing there.
 *
 * @param int    $id      Attachment id.
 * @param string $size    Registered image size for the thumbnail.
 * @param string $sizes   The `sizes` attribute for the thumbnail.
 * @param string $classes Extra classes for the link.
 *
 * @return void
 */
function sjpta_gallery_link( int $id, string $size, string $sizes, string $classes = '' ): void {
	if ( ! $id ) {
		return;
	}

	$full = wp_get_attachment_image_src( $id, 'full' );

	if ( ! $full ) {
		return;
	}

	$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );

	printf(
		'<a class="sjpta-gallery__link %1$s" href="%2$s" data-lightbox-item aria-label="%3$s">',
		esc_attr( $classes ),
		esc_url( $full[0] ),
		esc_attr(
			'' !== $alt
				/* translators: %s: the photograph's alt text. */
				? sprintf( __( 'View larger: %s', 'sjptheatrearts' ), $alt )
				: __( 'View this photograph larger', 'sjptheatrearts' )
		)
	);

	echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core markup, already escaped.
		$id,
		$size,
		false,
		array(
			'loading' => 'lazy',
			'sizes'   => $sizes,
		)
	);

	echo '</a>';
}

/**
 * A headline number the site can work out for itself.
 *
 * Shared by the homepage's about-stats and the About page's stat row, because
 * they make the same claims and must never disagree. The prototypes hard-coded
 * "13 class styles" and "12 awarding bodies"; the first was out of date before
 * the class list was finalised and the second sat directly above a row of four
 * logos. Anything derivable is counted, and a stat nobody can derive, such as
 * the number of troupes, stays a typed field.
 *
 * @param string $source Source key: `classes`, `logos`, or anything else for a
 *                       value the editor typed.
 *
 * @return string Empty when the value is typed rather than counted.
 */
function sjpta_stat_value( string $source ): string {
	if ( 'classes' === $source && function_exists( 'sjpta_class_count' ) ) {
		return (string) sjpta_class_count();
	}

	if ( 'logos' !== $source ) {
		return '';
	}

	$page = get_post();

	if ( ! $page instanceof WP_Post ) {
		return '';
	}

	/*
	 * Counted from the page's own content rather than a setting: the
	 * accreditation block is where the marks live, and this is the number a
	 * visitor can see on the screen in front of them.
	 */
	$count = 0;

	foreach ( parse_blocks( $page->post_content ) as $block ) {
		if ( 'sjptheatrearts/accreditation' !== ( $block['blockName'] ?? '' ) ) {
			continue;
		}

		$data  = $block['attrs']['data'] ?? array();
		$count = isset( $data['logos'] ) ? (int) $data['logos'] : 0;
	}

	return $count ? (string) $count : '';
}

/**
 * Is this URL somewhere other than this site?
 *
 * A link with no host at all is a path or a fragment, so it is ours. Anything
 * whose host differs from the site's belongs to somebody else, whatever it is:
 * the booking portal, Instagram, Facebook, a venue's box office.
 *
 * @param string $url The link's destination.
 *
 * @return bool
 */
function sjpta_is_external_url( string $url ): bool {
	$host = wp_parse_url( $url, PHP_URL_HOST );

	if ( ! is_string( $host ) || '' === $host ) {
		return false;
	}

	$home = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

	return strtolower( $host ) !== strtolower( is_string( $home ) ? $home : '' );
}

/**
 * Attributes for a link that leaves the site, opening it in a new tab.
 *
 * Decided from the URL's host rather than written into each link by hand, so a
 * link added later cannot quietly miss it. Internal links are untouched: a new
 * tab per link is a habit worth avoiding, and it is wanted only where the
 * destination is somebody else's site and losing this one behind it is the
 * annoying part.
 *
 * This began as a portal-only rule (Gaz, phase 5). It covers every outbound link
 * from phase 9: the social accounts were all opening in place, so a parent
 * tapping Instagram in the footer lost the site.
 *
 * `rel="noopener"` because a page opened with target="_blank" can otherwise
 * reach back at this one through window.opener.
 *
 * @param string $url The link's destination.
 *
 * @return string An escaped attribute fragment, or an empty string.
 */
function sjpta_external_attr( string $url ): string {
	return sjpta_is_external_url( $url ) ? ' target="_blank" rel="noopener"' : '';
}

/**
 * The warning that a link opens a new tab, for people who cannot see it happen.
 *
 * A new tab with no warning is disorienting for a screen reader user, whose back
 * button suddenly does nothing. Paired with sjpta_external_attr() at every call
 * site rather than left to chance.
 *
 * @param string $url The link's destination.
 *
 * @return string Escaped markup, or an empty string.
 */
function sjpta_new_tab_note( string $url ): string {
	if ( ! sjpta_is_external_url( $url ) ) {
		return '';
	}

	return '<span class="screen-reader-text">' . esc_html__( '(opens in a new tab)', 'sjptheatrearts' ) . '</span>';
}

/**
 * The `id` attribute for a block whose editor has set an anchor.
 *
 * Blocks here render their own outer element rather than calling
 * get_block_wrapper_attributes(), so core's anchor support has nothing to attach
 * to. Without this, in-page links such as #classes and #enquire have no target.
 *
 * @param array<string,mixed>|null $block The ACF block array.
 *
 * @return string An escaped ` id="…"` fragment, or an empty string.
 */
function sjpta_anchor_attr( $block ): string {
	if ( ! is_array( $block ) || empty( $block['anchor'] ) || ! is_string( $block['anchor'] ) ) {
		return '';
	}

	return ' id="' . esc_attr( $block['anchor'] ) . '"';
}

/**
 * Resolve a link, falling back to a page by slug.
 *
 * @param string $url  Explicit URL, may be empty.
 * @param string $slug Page slug to fall back to.
 *
 * @return string
 */
function sjpta_link_or_page( string $url, string $slug ): string {
	if ( '' !== trim( $url ) ) {
		return trim( $url );
	}

	$page = get_page_by_path( $slug );

	return $page ? (string) get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * The navigation items for the floating nav and the mobile menu.
 *
 * Prefers the "primary" menu location so the client can reorder and rename in
 * Appearance → Menus. Falls back to the five pages the design specifies, matched
 * by slug, so the chrome renders correctly before any menu has been assigned.
 *
 * @return array<int,array{url:string,label:string,current:bool}>
 */
function sjpta_nav_items(): array {
	$items = array();

	$locations = get_nav_menu_locations();
	$menu_id   = $locations['primary'] ?? 0;

	if ( $menu_id ) {
		$menu_items = wp_get_nav_menu_items( $menu_id );

		if ( is_array( $menu_items ) ) {
			foreach ( $menu_items as $item ) {
				// Top level only: the design's pill has no dropdowns.
				if ( 0 !== (int) $item->menu_item_parent ) {
					continue;
				}

				$items[] = array(
					'url'     => (string) $item->url,
					'label'   => (string) $item->title,
					'current' => sjpta_is_current_url( (string) $item->url ),
				);
			}
		}
	}

	if ( ! empty( $items ) ) {
		return $items;
	}

	$fallback = array(
		'classes'            => __( 'Classes', 'sjptheatrearts' ),
		'born-to-be'         => __( 'Born To Be', 'sjptheatrearts' ),
		'timetable-and-fees' => __( 'Timetable & fees', 'sjptheatrearts' ),
		'about'              => __( 'About', 'sjptheatrearts' ),
		'performances'       => __( 'Performances', 'sjptheatrearts' ),
		'contact'            => __( 'Contact', 'sjptheatrearts' ),
	);

	foreach ( $fallback as $slug => $label ) {
		$page = get_page_by_path( $slug );
		$url  = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );

		$items[] = array(
			'url'     => (string) $url,
			'label'   => $label,
			'current' => sjpta_is_current_url( (string) $url ),
		);
	}

	return $items;
}

/**
 * Is this URL the page currently being viewed?
 *
 * Compared on path alone so a scheme or host difference between the stored menu
 * URL and the request does not lose the current-page state.
 *
 * @param string $url URL to test.
 *
 * @return bool
 */
function sjpta_is_current_url( string $url ): bool {
	$path = untrailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );

	if ( '' === $path ) {
		return is_front_page();
	}

	$current = untrailingslashit(
		(string) wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH )
	);

	return $path === $current;
}
