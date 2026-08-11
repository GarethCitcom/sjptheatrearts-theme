/*
 * Gallery category filter.
 *
 * Enhancement only. Without this file the pills are ordinary links to
 * ?gallery=<category>, the block filters server-side, and the page works. This
 * turns them into an in-place filter so nobody waits for a reload to look at
 * photographs.
 *
 * The tiles are removed from the DOM rather than hidden, for the same reason the
 * server does it: a hidden tile is still a link in the tab order and still
 * announced, and the lightbox would happily step into photographs the visitor
 * has just filtered out.
 */
( function () {
	'use strict';

	var filter = document.querySelector( '[data-gallery-filter]' );
	var tiles = document.querySelector( '.sjpta-mosaic__tiles' );

	if ( ! filter || ! tiles ) {
		return;
	}

	var pills = Array.prototype.slice.call( filter.querySelectorAll( '[data-tag]' ) );

	/* Every tile, kept in order, so filtering never loses one permanently. */
	var all = Array.prototype.slice.call( tiles.children );

	filter.setAttribute( 'role', 'group' );

	pills.forEach( function ( pill ) {
		pill.setAttribute( 'role', 'button' );
		pill.setAttribute(
			'aria-pressed',
			pill.classList.contains( 'is-current' ) ? 'true' : 'false'
		);
	} );

	function show( tag ) {
		var shown = 0;

		all.forEach( function ( tile ) {
			var own = tile.getAttribute( 'data-tag' );

			/* No category means it belongs in every view. */
			var match = ( 'all' === tag || ! own || own === tag );

			if ( match ) {
				tiles.appendChild( tile );
				shown++;
			} else if ( tile.parentNode === tiles ) {
				tiles.removeChild( tile );
			}
		} );

		pills.forEach( function ( pill ) {
			var current = ( pill.getAttribute( 'data-tag' ) === tag );

			pill.classList.toggle( 'is-current', current );
			pill.setAttribute( 'aria-pressed', current ? 'true' : 'false' );
		} );

		/*
		 * Keep the address bar honest, so the filtered view can be shared and the
		 * back button steps through it. replaceState rather than pushState: the
		 * tiles are already on the page, and stacking history entries for a
		 * filter makes leaving the page a game of pressing back repeatedly.
		 */
		if ( window.history && window.history.replaceState ) {
			var url = new URL( window.location.href );

			if ( 'all' === tag ) {
				url.searchParams.delete( 'gallery' );
			} else {
				url.searchParams.set( 'gallery', tag );
			}

			window.history.replaceState( {}, '', url );
		}

		return shown;
	}

	function handle( event ) {
		var pill = event.target.closest( '[data-tag]' );

		if ( ! pill ) {
			return;
		}

		event.preventDefault();
		show( pill.getAttribute( 'data-tag' ) );
	}

	filter.addEventListener( 'click', handle );

	filter.addEventListener( 'keydown', function ( event ) {
		if ( ' ' === event.key || 'Spacebar' === event.key ) {
			handle( event );
		}
	} );
} )();
