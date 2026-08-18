/**
 * Video lightbox.
 *
 * Enhancement only. The play button is already a real link to the first video
 * file, so with this script blocked, failed or disabled a click still opens the
 * film. This intercepts that click and plays it in a pop-up instead.
 *
 * Attaches to any element carrying data-video-lightbox, whose value is the
 * playlist as JSON: [ { src, type, title, thumb } ]. The first video starts as
 * soon as the dialog opens (the click is the user gesture the browser wants),
 * each video runs on into the next when it ends, and a thumbnail rail switches
 * between them directly.
 */
( function () {
	'use strict';

	var openers = document.querySelectorAll( '[data-video-lightbox]' );

	if ( ! openers.length ) {
		return;
	}

	var items = [];
	var index = 0;
	var opener = null;
	var root = null;
	var video = null;
	var title = null;
	var rail = null;
	var position = null;

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
		root.className = 'sjpta-vlb';
		root.setAttribute( 'role', 'dialog' );
		root.setAttribute( 'aria-modal', 'true' );
		root.setAttribute( 'aria-label', 'Video player' );
		root.hidden = true;

		root.innerHTML =
			'<div class="sjpta-vlb__backdrop" data-close></div>' +
			'<div class="sjpta-vlb__inner">' +
			'<button class="sjpta-vlb__close" type="button" data-close aria-label="Close">' +
			'<svg width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m4 4 8 8m0-8-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>' +
			'</button>' +
			'<div class="sjpta-vlb__stage">' +
			'<video class="sjpta-vlb__video" controls playsinline preload="metadata"></video>' +
			'</div>' +
			'<p class="sjpta-vlb__meta"><span class="sjpta-vlb__title"></span>' +
			'<span class="sjpta-vlb__position" role="status" aria-live="polite"></span></p>' +
			'<div class="sjpta-vlb__rail" role="list" aria-label="More videos"></div>' +
			'</div>';

		document.body.appendChild( root );

		video = root.querySelector( '.sjpta-vlb__video' );
		title = root.querySelector( '.sjpta-vlb__title' );
		rail = root.querySelector( '.sjpta-vlb__rail' );
		position = root.querySelector( '.sjpta-vlb__position' );

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-close]' ) ) {
				close();
				return;
			}

			var thumb = event.target.closest( '[data-vlb-index]' );

			if ( thumb ) {
				show( parseInt( thumb.getAttribute( 'data-vlb-index' ), 10 ) );
			}
		} );

		// The playlist: when one video ends the next starts by itself.
		video.addEventListener( 'ended', function () {
			if ( index < items.length - 1 ) {
				show( index + 1 );
			}
		} );
	}

	/** Fill the thumbnail rail for the current playlist. */
	function buildRail() {
		rail.innerHTML = '';
		rail.hidden = items.length < 2;

		items.forEach( function ( item, i ) {
			var name = item.title || 'Video ' + ( i + 1 );
			var button = document.createElement( 'button' );

			button.type = 'button';
			button.className = 'sjpta-vlb__thumb';
			button.setAttribute( 'data-vlb-index', String( i ) );
			button.setAttribute( 'aria-label', 'Play: ' + name );

			var tile = document.createElement( 'span' );
			tile.className = 'sjpta-vlb__thumb-tile';

			if ( item.thumb ) {
				var img = document.createElement( 'img' );
				img.src = item.thumb;
				img.alt = '';
				img.loading = 'lazy';
				img.decoding = 'async';
				tile.appendChild( img );
			}

			tile.insertAdjacentHTML(
				'beforeend',
				'<svg viewBox="0 0 34 34" fill="none" aria-hidden="true" focusable="false"><path d="M12 8.5 25 17l-13 8.5V8.5Z" fill="currentColor"/></svg>'
			);

			var label = document.createElement( 'span' );
			label.className = 'sjpta-vlb__thumb-label';
			label.textContent = name;

			button.appendChild( tile );
			button.appendChild( label );

			var wrap = document.createElement( 'div' );
			wrap.setAttribute( 'role', 'listitem' );
			wrap.appendChild( button );

			rail.appendChild( wrap );
		} );
	}

	/** Show the video at i and start it playing. */
	function show( i ) {
		if ( i < 0 || i >= items.length ) {
			return;
		}

		index = i;

		var item = items[ index ];
		var name = item.title || 'Video ' + ( index + 1 );

		video.poster = item.thumb || '';
		video.src = item.src;

		title.textContent = name;
		position.textContent = items.length > 1 ? index + 1 + ' of ' + items.length : '';

		Array.prototype.forEach.call( rail.querySelectorAll( '[data-vlb-index]' ), function ( button, b ) {
			button.setAttribute( 'aria-current', b === index ? 'true' : 'false' );
		} );

		/*
		 * The opening click is the user gesture browsers require for playback
		 * with sound; if one still refuses, the visitor has the controls and the
		 * poster rather than a broken dialog.
		 */
		var playing = video.play();

		if ( playing && playing.catch ) {
			playing.catch( function () {} );
		}
	}

	/** Open the player on the clicked playlist. */
	function open( link, playlist ) {
		build();

		items = playlist;
		opener = link;

		buildRail();

		root.hidden = false;
		document.documentElement.classList.add( 'sjpta-vlb-open' );

		show( 0 );

		// The close button, so the first Tab stays inside the dialog.
		root.querySelector( '.sjpta-vlb__close' ).focus();
	}

	/** Close, stop the film, and put focus back where it came from. */
	function close() {
		if ( ! root || root.hidden ) {
			return;
		}

		// Stop playback and the download behind it, not just the sound.
		video.pause();
		video.removeAttribute( 'src' );
		video.load();

		root.hidden = true;
		document.documentElement.classList.remove( 'sjpta-vlb-open' );

		if ( opener ) {
			opener.focus();
			opener = null;
		}
	}

	Array.prototype.forEach.call( openers, function ( link ) {
		link.addEventListener( 'click', function ( event ) {
			// Let modified clicks do what the visitor asked: new tab, download.
			if ( event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || 0 !== event.button ) {
				return;
			}

			var playlist;

			try {
				playlist = JSON.parse( link.getAttribute( 'data-video-lightbox' ) );
			} catch ( e ) {
				return; // Unreadable playlist: the link opens the file, as without JS.
			}

			if ( ! Array.isArray( playlist ) || ! playlist.length ) {
				return;
			}

			event.preventDefault();
			open( link, playlist );
		} );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( ! root || root.hidden ) {
			return;
		}

		if ( 'Escape' === event.key ) {
			close();
		} else if ( 'Tab' === event.key ) {
			/*
			 * Keep Tab inside the dialog. The video element is focusable too (it
			 * carries the controls), so the trap walks everything focusable, not
			 * just the buttons.
			 */
			var focusable = Array.prototype.filter.call(
				root.querySelectorAll( 'button, video' ),
				function ( el ) {
					// The rail is [hidden] when there is only one video.
					return ! el.closest( '[hidden]' );
				}
			);

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
