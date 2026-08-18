/**
 * Gallery lightbox.
 *
 * Enhancement only. Every thumbnail is already a link to its full-size file, so
 * with this script blocked, failed or disabled a click still shows the photograph
 * at full size. This intercepts that click and shows it in place instead.
 *
 * Attaches to any element carrying data-lightbox, so a new gallery block gets it
 * by adding one attribute.
 */
( function () {
	'use strict';

	var galleries = document.querySelectorAll( '[data-lightbox]' );

	if ( ! galleries.length ) {
		return;
	}

	var items = [];
	var index = 0;
	var opener = null;
	var root = null;
	var figure = null;
	var image = null;
	var caption = null;
	var position = null;
	var prev = null;
	var next = null;

	/**
	 * Build the dialog once, on first use.
	 *
	 * Not built at load: most visitors never open it, and an empty dialog in the
	 * DOM is one more thing for a screen reader to walk past.
	 */
	function build() {
		if ( root ) {
			return;
		}

		root = document.createElement( 'div' );
		root.className = 'sjpta-lightbox';
		root.setAttribute( 'role', 'dialog' );
		root.setAttribute( 'aria-modal', 'true' );
		root.setAttribute( 'aria-label', 'Photograph viewer' );
		root.hidden = true;

		root.innerHTML =
			'<div class="sjpta-lightbox__backdrop" data-close></div>' +
			'<div class="sjpta-lightbox__inner">' +
			'<button class="sjpta-lightbox__btn sjpta-lightbox__btn--close" type="button" data-close aria-label="Close">' +
			'<svg width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m4 4 8 8m0-8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>' +
			'</button>' +
			'<button class="sjpta-lightbox__btn sjpta-lightbox__btn--prev" type="button" data-prev aria-label="Previous photograph">' +
			'<svg width="20" height="20" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M10 3.5 5 8l5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
			'</button>' +
			'<figure class="sjpta-lightbox__figure">' +
			'<img class="sjpta-lightbox__img" alt="" decoding="async">' +
			'<figcaption class="sjpta-lightbox__caption"></figcaption>' +
			'</figure>' +
			'<button class="sjpta-lightbox__btn sjpta-lightbox__btn--next" type="button" data-next aria-label="Next photograph">' +
			'<svg width="20" height="20" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m6 3.5 5 4.5-5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
			'</button>' +
			'<p class="sjpta-lightbox__position" role="status" aria-live="polite"></p>' +
			'</div>';

		document.body.appendChild( root );

		figure = root.querySelector( '.sjpta-lightbox__figure' );
		image = root.querySelector( '.sjpta-lightbox__img' );
		caption = root.querySelector( '.sjpta-lightbox__caption' );
		position = root.querySelector( '.sjpta-lightbox__position' );
		prev = root.querySelector( '[data-prev]' );
		next = root.querySelector( '[data-next]' );

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-close]' ) ) {
				close();
			} else if ( event.target.closest( '[data-prev]' ) ) {
				show( index - 1 );
			} else if ( event.target.closest( '[data-next]' ) ) {
				show( index + 1 );
			}
		} );
	}

	/** Show the item at i, wrapping around the ends. */
	function show( i ) {
		var count = items.length;
		index = ( ( i % count ) + count ) % count;

		var item = items[ index ];

		image.src = item.href;
		image.alt = item.alt;
		caption.textContent = item.alt;
		caption.hidden = '' === item.alt;
		position.textContent = index + 1 + ' of ' + count;

		// One photograph needs no arrows.
		prev.hidden = count < 2;
		next.hidden = count < 2;
	}

	/** Open the viewer on the clicked thumbnail. */
	function open( gallery, link ) {
		build();

		items = Array.prototype.map.call(
			gallery.querySelectorAll( '[data-lightbox-item]' ),
			function ( el ) {
				var img = el.querySelector( 'img' );

				return { href: el.getAttribute( 'href' ), alt: img ? img.alt : '' };
			}
		);

		opener = link;
		show( Array.prototype.indexOf.call( gallery.querySelectorAll( '[data-lightbox-item]' ), link ) );

		root.hidden = false;
		document.documentElement.classList.add( 'sjpta-lightbox-open' );

		/*
		 * The close BUTTON, not `[data-close]`: the backdrop carries that
		 * attribute too and comes first in the DOM, and calling focus() on a
		 * plain div does nothing at all. Focus then stayed on the thumbnail
		 * behind the dialog, so the first Tab walked straight out of it.
		 */
		root.querySelector( '.sjpta-lightbox__btn--close' ).focus();
	}

	/** Close, and put focus back where it came from. */
	function close() {
		if ( ! root || root.hidden ) {
			return;
		}

		root.hidden = true;
		document.documentElement.classList.remove( 'sjpta-lightbox-open' );

		if ( opener ) {
			opener.focus();
			opener = null;
		}
	}

	Array.prototype.forEach.call( galleries, function ( gallery ) {
		gallery.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( '[data-lightbox-item]' );

			if ( ! link || ! gallery.contains( link ) ) {
				return;
			}

			// Let modified clicks do what the visitor asked: new tab, download.
			if ( event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || 0 !== event.button ) {
				return;
			}

			event.preventDefault();
			open( gallery, link );
		} );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( ! root || root.hidden ) {
			return;
		}

		if ( 'Escape' === event.key ) {
			close();
		} else if ( 'ArrowLeft' === event.key ) {
			show( index - 1 );
		} else if ( 'ArrowRight' === event.key ) {
			show( index + 1 );
		} else if ( 'Tab' === event.key ) {
			/*
			 * Keep Tab inside the dialog. Without this the next Tab lands on the
			 * page behind, which is covered and unreadable, and the visitor has
			 * no idea where their focus went.
			 */
			var focusable = root.querySelectorAll( 'button:not([hidden])' );

			if ( ! focusable.length ) {
				return;
			}

			var first = focusable[ 0 ];
			var last = focusable[ focusable.length - 1 ];

			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}
	} );
}() );
