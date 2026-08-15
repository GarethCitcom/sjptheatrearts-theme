<?php
/**
 * Asset loading: font preloads, editor styles, per-block stylesheet registration.
 *
 * The theme's style.css is deliberately not enqueued — it carries the theme
 * header only. All design tokens come from theme.json, and all block CSS is
 * registered per block so a page loads only what it renders.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The font files eligible for preloading, in priority order.
 *
 * Budget rule: preload at most two files — the ones used above the fold. Both
 * families are used in every hero, so both entries are genuinely above the fold.
 *
 * @return array<int,string> Paths relative to the theme root.
 */
function sjpta_preload_fonts(): array {
	return array(
		'assets/fonts/montserrat-variable.woff2',
		'assets/fonts/poppins-700.woff2',
	);
}

/**
 * Preload the above-the-fold fonts.
 *
 * Emitted early in <head> so the request starts before the CSS that references
 * it has parsed. crossorigin is required on font preloads even same-origin, or
 * the browser fetches the file twice.
 *
 * @return void
 */
function sjpta_preload_font_files(): void {
	if ( is_admin() ) {
		return;
	}

	foreach ( sjpta_preload_fonts() as $relative ) {
		if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
			continue;
		}

		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( SJPTA_URI . '/' . $relative )
		);
	}
}
add_action( 'wp_head', 'sjpta_preload_font_files', 2 );

/**
 * Preload the page hero's large photograph, on the widths that show it early.
 *
 * On a wide screen the photograph sits beside the copy and is the LCP element,
 * and without a preload it is not discovered until the stylesheet has parsed and
 * the block's markup is reached.
 *
 * Below 1024px the hero stacks, the photograph drops almost entirely below the
 * fold, and the LCP element is the intro paragraph instead. Preloading a
 * 60KB photograph there does not help the LCP, it competes with the font the
 * LCP text is waiting on. Hence the media query: measured on Born To Be, an
 * unconditional preload left mobile LCP at 2.9s.
 *
 * Emitted from the parsed post content rather than by the block, because a block
 * renders long after <head> has been sent.
 *
 * @return void
 */
function sjpta_preload_hero_image(): void {
	if ( ! is_singular() ) {
		return;
	}

	$post = get_post();

	if ( ! $post instanceof WP_Post || ! has_blocks( $post->post_content ) ) {
		return;
	}

	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( 'sjptheatrearts/page-hero' !== ( $block['blockName'] ?? '' ) ) {
			continue;
		}

		$id = (int) ( $block['attrs']['data']['photo_main'] ?? 0 );

		if ( ! $id ) {
			return;
		}

		$image = wp_get_attachment_image_src( $id, 'large' );

		if ( ! $image ) {
			return;
		}

		$srcset = wp_get_attachment_image_srcset( $id, 'large' );

		printf(
			'<link rel="preload" as="image" href="%s"%s imagesizes="460px" media="(min-width: 1024px)" fetchpriority="high">' . "\n",
			esc_url( $image[0] ),
			$srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '"' : ''
		);

		// Only the first hero on a page is above the fold.
		return;
	}
}
add_action( 'wp_head', 'sjpta_preload_hero_image', 2 );

/**
 * Register the shared primitives stylesheet.
 *
 * Every block.json lists `sjpta-primitives` alongside its own stylesheet, so
 * WordPress loads this only on pages that render at least one of our blocks —
 * the same per-block rule as everything else.
 *
 * It has to exist before the blocks register on `init`, hence priority 5. The
 * `path` is set so core can inline it like any other block style.
 *
 * @return void
 */
function sjpta_register_primitives(): void {
	$relative = 'assets/css/primitives.css';
	$path     = SJPTA_DIR . '/' . $relative;

	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_register_style( 'sjpta-primitives', SJPTA_URI . '/' . $relative, array(), SJPTA_VERSION );
	wp_style_add_data( 'sjpta-primitives', 'path', $path );
}
add_action( 'init', 'sjpta_register_primitives', 5 );

/**
 * Register the lightbox stylesheet and script.
 *
 * Named handles rather than files inside the block, because more than one
 * gallery block will want them: the Born To Be mosaic today, Performances in
 * phase 6. Each block references these from its block.json, so they load only on
 * a page that actually renders a gallery.
 *
 * The script is deferred and does nothing until a thumbnail is clicked. Every
 * thumbnail is a real link to its full-size file, so the page works without it.
 *
 * @return void
 */
function sjpta_register_lightbox(): void {
	$css = 'assets/css/lightbox.css';
	$js  = 'assets/js/lightbox.js';

	if ( file_exists( SJPTA_DIR . '/' . $css ) ) {
		wp_register_style( 'sjpta-lightbox', SJPTA_URI . '/' . $css, array(), SJPTA_VERSION );
		wp_style_add_data( 'sjpta-lightbox', 'path', SJPTA_DIR . '/' . $css );
	}

	if ( file_exists( SJPTA_DIR . '/' . $js ) ) {
		wp_register_script(
			'sjpta-lightbox',
			SJPTA_URI . '/' . $js,
			array(),
			SJPTA_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
}
add_action( 'init', 'sjpta_register_lightbox', 5 );

/**
 * Register the video lightbox stylesheet and script.
 *
 * Named handles for the same reason as the photo lightbox: the homepage
 * experience block uses them today, and any block that grows a video playlist
 * later references the same pair from its block.json rather than shipping a
 * second player.
 *
 * The script is deferred and does nothing until the play button is clicked.
 * That button is a real link to the first video file, so the page works
 * without it.
 *
 * @return void
 */
function sjpta_register_video_lightbox(): void {
	$css = 'assets/css/video-lightbox.css';
	$js  = 'assets/js/video-lightbox.js';

	if ( file_exists( SJPTA_DIR . '/' . $css ) ) {
		wp_register_style( 'sjpta-video-lightbox', SJPTA_URI . '/' . $css, array(), SJPTA_VERSION );
		wp_style_add_data( 'sjpta-video-lightbox', 'path', SJPTA_DIR . '/' . $css );
	}

	if ( file_exists( SJPTA_DIR . '/' . $js ) ) {
		wp_register_script(
			'sjpta-video-lightbox',
			SJPTA_URI . '/' . $js,
			array(),
			SJPTA_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
}
add_action( 'init', 'sjpta_register_video_lightbox', 5 );

/**
 * Register the stylesheets shared by more than one block.
 *
 * The handle sjpta-form styles sjpta_enquiry_form(), and sjpta-class-card styles
 * sjpta_class_card(). Both are helpers that several blocks call, so their CSS
 * cannot live in whichever block happened to need it first. A class page
 * rendered the enquiry form as unstyled inputs with the honeypot in plain sight,
 * because those rules sat inside the enquiry panel's own stylesheet and that
 * block is not on the page.
 *
 * @return void
 */
function sjpta_register_shared_styles(): void {
	$shared = array(
		'sjpta-form'       => 'assets/css/form.css',
		'sjpta-class-card' => 'assets/css/class-card.css',

		/*
		 * Shared by exactly two blocks, so it is not in primitives.css: that file
		 * loads on every page that renders any block at all, and the collage was
		 * costing two kilobytes on seven pages that never draw one.
		 */
		'sjpta-collage'    => 'assets/css/collage.css',
	);

	foreach ( $shared as $handle => $relative ) {
		$path = SJPTA_DIR . '/' . $relative;

		if ( ! file_exists( $path ) ) {
			continue;
		}

		wp_register_style( $handle, SJPTA_URI . '/' . $relative, array(), SJPTA_VERSION );
		wp_style_add_data( $handle, 'path', $path );
	}
}
add_action( 'init', 'sjpta_register_shared_styles', 5 );

/**
 * Register the class filter enhancement.
 *
 * Referenced from blocks/class-filters/block.json as a viewScript, so it loads
 * only on a page that renders the filter bar.
 *
 * @return void
 */
function sjpta_register_class_filters(): void {
	$relative = 'assets/js/class-filters.js';

	if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
		return;
	}

	wp_register_script(
		'sjpta-class-filters',
		SJPTA_URI . '/' . $relative,
		array(),
		SJPTA_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'init', 'sjpta_register_class_filters', 5 );

/**
 * Register the timetable's day filter.
 *
 * Referenced from the timetable block's viewScript, so it loads on the one page
 * that renders a timetable and nowhere else. Pure enhancement: the day pills are
 * real links to each day's table without it.
 *
 * @return void
 */
function sjpta_register_timetable_filter(): void {
	$relative = 'assets/js/timetable-filter.js';

	if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
		return;
	}

	wp_register_script(
		'sjpta-timetable-filter',
		SJPTA_URI . '/' . $relative,
		array(),
		SJPTA_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'init', 'sjpta_register_timetable_filter', 5 );

/**
 * Register the gallery's category filter.
 *
 * Referenced from the gallery block's viewScript. Pure enhancement: the pills
 * are real links to a filtered view of the page, which the block honours
 * server-side, so the filter works with scripting off.
 *
 * @return void
 */
function sjpta_register_gallery_filter(): void {
	$relative = 'assets/js/gallery-filter.js';

	if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
		return;
	}

	wp_register_script(
		'sjpta-gallery-filter',
		SJPTA_URI . '/' . $relative,
		array(),
		SJPTA_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'init', 'sjpta_register_gallery_filter', 5 );

/**
 * Load the Born To Be accent override.
 *
 * Six custom properties, on pages whose template opts in by putting
 * `.sjpta-theme-btb` on <main>. Registered unconditionally as an editor style so
 * the block editor shows the right accent too.
 *
 * @return void
 */
function sjpta_enqueue_accent(): void {
	$relative = 'assets/css/accent-born-to-be.css';

	if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
		return;
	}

	wp_enqueue_style( 'sjpta-accent-born-to-be', SJPTA_URI . '/' . $relative, array( 'sjpta-primitives' ), SJPTA_VERSION );
}

/**
 * Load the accent override on the pages that use it.
 *
 * Tied to the same slug as templates/page-born-to-be.html, which is what puts
 * .sjpta-theme-btb on <main>. If the slug ever changes, both stop applying
 * together rather than one silently outliving the other.
 *
 * @return void
 */
function sjpta_maybe_enqueue_accent(): void {
	if ( is_page( 'born-to-be' ) ) {
		sjpta_enqueue_accent();
	}
}
add_action( 'wp_enqueue_scripts', 'sjpta_maybe_enqueue_accent' );

/**
 * Cache-bust a theme asset on its own modification time.
 *
 * Every theme asset was previously served as `?ver=0.1.0`, the theme version,
 * which does not change when a stylesheet or script does. A browser that had
 * loaded the site once then kept serving its cached copy of every file edited
 * afterwards, so a fix could be live on the server and invisible in the very
 * browser being used to review it. That is the sort of bug that gets chased in
 * the wrong place for an hour.
 *
 * Appending the file's mtime means a changed file is always a new URL, and an
 * unchanged one still caches for as long as the host allows.
 *
 * @param string $src Asset URL.
 *
 * @return string
 */
function sjpta_asset_cachebust( $src ): string {
	$src = (string) $src;

	if ( ! str_starts_with( $src, SJPTA_URI ) ) {
		return $src;
	}

	$relative = substr( (string) strtok( $src, '?' ), strlen( SJPTA_URI ) );
	$path     = SJPTA_DIR . $relative;

	if ( ! file_exists( $path ) ) {
		return $src;
	}

	return add_query_arg( 'ver', SJPTA_VERSION . '.' . filemtime( $path ), remove_query_arg( 'ver', $src ) );
}
add_filter( 'style_loader_src', 'sjpta_asset_cachebust', 20 );
add_filter( 'script_loader_src', 'sjpta_asset_cachebust', 20 );

/**
 * Serve the minified twin of any theme stylesheet that has one.
 *
 * Block stylesheets are registered by WordPress from each block.json, which
 * points at the readable source. Swapping the src here means block.json keeps
 * naming the file a developer edits, `npm run css` produces the file a visitor
 * downloads, and a checkout that has never run the build still works — just
 * heavier. Only ever swaps when the .min.css actually exists on disk.
 *
 * @param string|mixed $src    Stylesheet URL.
 * @param string|mixed $handle Style handle.
 *
 * @return string
 */
function sjpta_use_minified_css( $src, $handle ): string {
	$src = (string) $src;

	if ( ! str_starts_with( $src, SJPTA_URI ) || str_contains( $src, '.min.css' ) ) {
		return $src;
	}

	// Strip any cache-busting query before testing the path on disk.
	$clean    = strtok( $src, '?' );
	$relative = substr( (string) $clean, strlen( SJPTA_URI ) );

	if ( ! str_ends_with( $relative, '.css' ) ) {
		return $src;
	}

	$minified = substr( $relative, 0, -4 ) . '.min.css';

	if ( ! file_exists( SJPTA_DIR . $minified ) ) {
		return $src;
	}

	unset( $handle );

	return SJPTA_URI . $minified . '?ver=' . SJPTA_VERSION;
}
add_filter( 'style_loader_src', 'sjpta_use_minified_css', 10, 2 );

/**
 * Point WordPress's style inlining at the minified file too.
 *
 * Core inlines a block stylesheet by reading the `path` it recorded at
 * registration — which is the readable source. Without this the swap above only
 * helps the styles core leaves as <link>s, and the inlined ones stay verbose.
 *
 * @return void
 */
function sjpta_minify_inlined_block_styles(): void {
	$styles = wp_styles();

	foreach ( $styles->registered as $handle => $style ) {
		$path = $style->extra['path'] ?? '';

		if ( ! is_string( $path ) || '' === $path || ! str_ends_with( $path, '.css' ) || str_ends_with( $path, '.min.css' ) ) {
			continue;
		}

		$minified = substr( $path, 0, -4 ) . '.min.css';

		if ( file_exists( $minified ) ) {
			$styles->add_data( $handle, 'path', $minified );
		}
	}
}

/*
 * Priority 0 on wp_head: core inlines stylesheets from `path` in
 * wp_maybe_inline_styles() at priority 1, and block styles are not all
 * registered yet during wp_enqueue_scripts. Running immediately before core is
 * the only point where every path exists and none has been read yet.
 */
add_action( 'wp_head', 'sjpta_minify_inlined_block_styles', 0 );
add_action( 'wp_footer', 'sjpta_minify_inlined_block_styles', 0 );

/**
 * Enqueue the small global stylesheet.
 *
 * Holds only the rules theme.json and per-block CSS cannot express: the skip
 * link, the visually-hidden helper, and focus styling.
 *
 * @return void
 */
function sjpta_enqueue_global_css(): void {
	$path = SJPTA_DIR . '/assets/css/global.css';

	if ( ! file_exists( $path ) ) {
		return;
	}

	/*
	 * Inlined rather than linked. It is a couple of kilobytes, it is needed on
	 * every page, and it is render-blocking either way — so a separate request
	 * only adds a round trip to the critical path. Registered against a handle
	 * with no src so it still participates in the dependency graph.
	 */
	wp_register_style( 'sjpta-global', false, array(), SJPTA_VERSION );
	wp_enqueue_style( 'sjpta-global' );

	$minified = SJPTA_DIR . '/assets/css/global.min.css';

	if ( file_exists( $minified ) ) {
		$path = $minified;
	}

	$css = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file, not a remote request.

	if ( is_string( $css ) && '' !== $css ) {
		wp_add_inline_style( 'sjpta-global', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'sjpta_enqueue_global_css' );

/**
 * Enqueue the motion layer.
 *
 * Deferred and dependency-free. Everything it does is enhancement, so the page
 * is complete before it runs and remains usable if it never does.
 *
 * @return void
 */
function sjpta_enqueue_motion(): void {
	$relative = 'assets/js/motion.js';

	if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
		return;
	}

	wp_enqueue_script(
		'sjpta-motion',
		SJPTA_URI . '/' . $relative,
		array(),
		SJPTA_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'sjpta_enqueue_motion' );

/**
 * Enqueue the form enhancement.
 *
 * Sends the enquiry forms and the footer sign-up without a page load. On every
 * page, because the footer's sign-up box is on every page; small and deferred,
 * with no dependencies. Every form it touches works as a plain POST without
 * it, so nothing waits on this script.
 *
 * @return void
 */
function sjpta_enqueue_enquiry_form(): void {
	$relative = 'assets/js/enquiry-form.js';

	if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
		return;
	}

	wp_enqueue_script(
		'sjpta-enquiry-form',
		SJPTA_URI . '/' . $relative,
		array(),
		SJPTA_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'sjpta_enqueue_enquiry_form' );

/**
 * Enqueue the hero ribbon's animated upgrade.
 *
 * Front page only — it is the only template with a hero. The still SVG is
 * already in the HTML, so this file is pure enhancement and gates itself on
 * pointer type and motion preference before doing anything.
 *
 * @return void
 */
function sjpta_enqueue_hero_ribbon(): void {
	if ( ! is_front_page() ) {
		return;
	}

	$relative = 'assets/js/hero-ribbon.js';

	if ( ! file_exists( SJPTA_DIR . '/' . $relative ) ) {
		return;
	}

	wp_enqueue_script(
		'sjpta-hero-ribbon',
		SJPTA_URI . '/' . $relative,
		array(),
		SJPTA_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'sjpta_enqueue_hero_ribbon' );

/**
 * Output the back-to-top anchor target.
 *
 * Deliberately no skip link here: block themes already emit one
 * (`the_block_template_skip_link()`), and adding a second produced two
 * identical "Skip to content" tab stops. Core's is styled in global.css
 * instead.
 *
 * @return void
 */
function sjpta_top_anchor(): void {
	echo '<span id="sjpta-top"></span>';
}
add_action( 'wp_body_open', 'sjpta_top_anchor' );

/**
 * Register the editor stylesheet so the block editor matches the front end.
 *
 * @return void
 */
function sjpta_editor_styles(): void {
	if ( file_exists( SJPTA_DIR . '/assets/css/editor.css' ) ) {
		add_editor_style( 'assets/css/editor.css' );
	}
}
add_action( 'after_setup_theme', 'sjpta_editor_styles' );

/**
 * Register the theme's plain dynamic blocks in the editor.
 *
 * Registering a block in PHP renders it on the front end but does not put it in
 * the editor's registry. A block placed in *page content* rather than in a
 * template therefore shows "Your site doesn't include support for this block"
 * and cannot be edited: the closing CTA band was in that state on every page
 * from phase 3a until this was added. ACF's blocks are unaffected, because ACF
 * registers those in the editor itself.
 *
 * @return void
 */
function sjpta_editor_blocks(): void {
	$relative = 'assets/js/editor-blocks.js';
	$path     = SJPTA_DIR . '/' . $relative;

	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'sjpta-editor-blocks',
		SJPTA_URI . '/' . $relative,
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		SJPTA_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'sjpta_editor_blocks' );

/*
 * A previous revision widened the inspector sidebar to 480px whenever it held
 * ACF fields (`:has(.acf-fields)` on the skeleton sidebar). The inner panel
 * kept WordPress's fixed width, leaving a blank white band inside the sidebar
 * the moment a block was selected. Removed rather than patched: the wide
 * editing surface is ACF's edit mode in the canvas, and admin-chrome CSS
 * should not be written blind against a DOM we have not inspected.
 */

/**
 * Register per-block stylesheets.
 *
 * Any file at assets/css/blocks/<namespace>-<block>.css is attached to the
 * matching block, so WordPress only ships it on pages that render that block.
 * Filenames use the block name with the slash replaced by a hyphen, e.g.
 * core/button -> core-button.css.
 *
 * @return void
 */
function sjpta_register_block_styles(): void {
	$dir = SJPTA_DIR . '/assets/css/blocks';

	if ( ! is_dir( $dir ) ) {
		return;
	}

	$files = glob( $dir . '/*.css' );

	if ( empty( $files ) ) {
		return;
	}

	foreach ( $files as $file ) {
		$slug = basename( $file, '.css' );

		// core-button -> core/button. Only the first hyphen is the separator.
		$block_name = preg_replace( '/-/', '/', $slug, 1 );

		if ( null === $block_name ) {
			continue;
		}

		wp_enqueue_block_style(
			$block_name,
			array(
				'handle' => 'sjpta-' . $slug,
				'src'    => SJPTA_URI . '/assets/css/blocks/' . $slug . '.css',
				'path'   => $file,
				'ver'    => SJPTA_VERSION,
			)
		);
	}
}
add_action( 'init', 'sjpta_register_block_styles' );
