/**
 * Filter the class list in place.
 *
 * Enhancement only. The bar is a working GET form and the age-route cards are
 * real links, so with this script blocked, failed or disabled every filter still
 * works by reloading. This intercepts that, fetches the same markup from the
 * REST route, and swaps the grid without a reload.
 *
 * The URL is kept in step with history.pushState, so a filtered view is still
 * something a parent can bookmark, share, or reach with the back button.
 */
( function () {
	'use strict';

	var form = document.querySelector( '[data-class-filters]' );
	var list = document.querySelector( '[data-class-list]' );

	if ( ! form || ! list || ! window.fetch || ! window.history.pushState ) {
		return;
	}

	var endpoint = form.getAttribute( 'data-endpoint' );
	var counter = form.querySelector( '[data-filter-count]' );
	var submit = form.querySelector( '.sjpta-filters__go' );

	if ( ! endpoint ) {
		return;
	}

	/*
	 * The Apply button only exists for the no-script path. Once we are running,
	 * changing a filter applies itself, so leaving it would be a control that
	 * repeats what just happened.
	 */
	if ( submit ) {
		submit.hidden = true;
	}

	form.classList.add( 'is-live' );

	/*
	 * Copy of the help card's copy, read from the card the server already
	 * rendered, so the endpoint can return it again without the page having to
	 * duplicate the text into a data attribute.
	 */
	var help = ( function () {
		var card = list.querySelector( '.sjpta-helpcard' );

		if ( ! card ) {
			return {};
		}

		var title = card.querySelector( '.sjpta-helpcard__title' );
		var text = card.querySelector( '.sjpta-helpcard__text' );
		var cta = card.querySelector( '.sjpta-helpcard__cta' );

		return {
			help_title: title ? title.textContent.trim() : '',
			help_text: text ? text.textContent.trim() : '',
			cta_label: cta ? cta.textContent.trim() : '',
			cta_url: cta ? cta.getAttribute( 'href' ) : '',
		};
	}() );

	var FILTERS = [ 'age', 'style', 'day', 'tag' ];

	/** The ticked values of one filter. */
	function checked( name ) {
		return Array.prototype.map.call(
			form.querySelectorAll( '[data-filter="' + name + '"]:checked' ),
			function ( box ) {
				return box.value;
			}
		);
	}

	/**
	 * The current state of every filter, each as a comma-separated list.
	 *
	 * The comma form is what goes into the address bar and the endpoint; the
	 * checkboxes themselves submit as arrays on the no-script path, and the
	 * server reads both.
	 */
	function state() {
		var filters = {};

		FILTERS.forEach( function ( name ) {
			filters[ name ] = checked( name ).join( ',' );
		} );

		return filters;
	}

	/** Rewrite a menu's summary to say what is ticked, or its resting label. */
	function relabel( name ) {
		var menu = form.querySelector( '[data-filter-menu="' + name + '"]' );
		var label = menu ? menu.querySelector( '[data-menu-label]' ) : null;

		if ( ! label ) {
			return;
		}

		var names = Array.prototype.map.call(
			form.querySelectorAll( '[data-filter="' + name + '"]:checked' ),
			function ( box ) {
				return box.getAttribute( 'data-label' ) || box.value;
			}
		);

		label.textContent = names.length ? names.join( ', ' ) : label.getAttribute( 'data-empty' );
	}

	/** Tick exactly the given values in one filter's menu. */
	function setFilter( name, values ) {
		form.querySelectorAll( '[data-filter="' + name + '"]' ).forEach( function ( box ) {
			box.checked = -1 !== values.indexOf( box.value );
		} );

		relabel( name );
	}

	/** Build a query string from the filters that are actually set. */
	function queryFrom( filters ) {
		var params = new URLSearchParams();

		Object.keys( filters ).forEach( function ( key ) {
			if ( filters[ key ] ) {
				params.set( key, filters[ key ] );
			}
		} );

		return params.toString();
	}

	/**
	 * Show cards that arrived after motion.js had already run.
	 *
	 * motion.js hides staggered children with `.sjpta-reveal [data-stagger] > *
	 * { opacity: 0 }` and reveals them by adding `.is-revealed` as they scroll
	 * into view. It does that once, to the children present at the time. Anything
	 * swapped in later inherits the hidden state and nothing ever comes back to
	 * reveal it, so the new cards are in the DOM, correct, and invisible.
	 *
	 * The class is added on the next frame rather than immediately, so the
	 * browser has painted the hidden state once and the change actually
	 * transitions instead of snapping.
	 */
	function reveal() {
		window.requestAnimationFrame( function () {
			Array.prototype.forEach.call( list.children, function ( child, i ) {
				// Short and capped: this follows a click, so a long cascade reads
				// as lag rather than as polish.
				child.style.transitionDelay = Math.min( i, 6 ) * 40 + 'ms';
				child.classList.add( 'is-revealed' );
			} );
		} );
	}

	var request = 0;

	/** The Clear link only earns its place while something is ticked. */
	function toggleClear( filters ) {
		var link = form.querySelector( '[data-filter-clear]' );

		if ( link ) {
			link.hidden = ! FILTERS.some( function ( name ) {
				return '' !== filters[ name ];
			} );
		}
	}

	/** Fetch and swap. */
	function apply( filters, push, scrollToList ) {
		toggleClear( filters );
		var mine = ++request;
		var query = queryFrom( filters );
		var url = endpoint + ( query ? '?' + query : '' );

		list.setAttribute( 'aria-busy', 'true' );

		var body = new URLSearchParams();
		Object.keys( help ).forEach( function ( key ) {
			body.set( 'help[' + key + ']', help[ key ] );
		} );

		fetch( url + ( query ? '&' : '?' ) + body.toString(), {
			headers: { Accept: 'application/json' },
		} )
			.then( function ( response ) {
				return response.ok ? response.json() : Promise.reject( response );
			} )
			.then( function ( data ) {
				// A slower earlier request must not overwrite a newer answer.
				if ( mine !== request ) {
					return;
				}

				list.innerHTML = data.html;
				list.removeAttribute( 'aria-busy' );
				reveal();

				if ( counter ) {
					counter.innerHTML =
						'<strong>' + data.label + '</strong> ' + counter.getAttribute( 'data-shown' );
				}

				if ( push ) {
					var page = form.getAttribute( 'action' ).split( '?' )[ 0 ];
					window.history.pushState( filters, '', query ? page + '?' + query : page );
				}

				if ( scrollToList ) {
					list.scrollIntoView( { behavior: 'smooth', block: 'start' } );
				}
			} )
			.catch( function () {
				/*
				 * Fall back to what works: let the form submit properly. A failed
				 * fetch must not leave the visitor looking at a stale list that
				 * disagrees with the filters above it.
				 */
				list.removeAttribute( 'aria-busy' );
				form.submit();
			} );
	}

	if ( counter ) {
		// Remember the trailing word so the count can be rebuilt without it.
		var strong = counter.querySelector( 'strong' );
		var shown = counter.textContent.replace( strong ? strong.textContent : '', '' ).trim();
		counter.setAttribute( 'data-shown', shown.split( /\s{2,}|\n/ )[ 0 ] || '' );
	}

	/*
	 * Listening on the document, not the form: the age-route cards further up the
	 * page carry the same data-filter hooks and mean the same thing, so a route
	 * card filters in place rather than reloading. Anchors only, so the checkbox
	 * menus in the form (handled by the change listener) are left alone.
	 */
	document.addEventListener( 'click', function ( event ) {
		var chip = event.target.closest( 'a[data-filter="age"]' );
		var clear = event.target.closest( '[data-filter-clear]' );

		// An open menu closes when the visitor clicks anywhere outside it.
		form.querySelectorAll( '[data-filter-menu][open]' ).forEach( function ( menu ) {
			if ( ! menu.contains( event.target ) ) {
				menu.open = false;
			}
		} );

		if ( clear ) {
			event.preventDefault();
			FILTERS.forEach( function ( name ) {
				setFilter( name, [] );
			} );
			apply( state(), true );
			return;
		}

		if ( ! chip ) {
			return;
		}

		// Let a modified click open the filtered view in a new tab, as a link should.
		if ( event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || 0 !== event.button ) {
			return;
		}

		event.preventDefault();
		setFilter( 'age', [ chip.getAttribute( 'data-value' ) ] );

		/*
		 * A route card sits well above the list, so without this the page would
		 * appear to do nothing: the cards it changed are off screen.
		 */
		var scroll = ! form.contains( chip );

		apply( state(), true, scroll );
	} );

	// Escape closes the menu the visitor is in and puts focus back on it.
	form.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}

		var menu = event.target.closest( '[data-filter-menu][open]' );

		if ( menu ) {
			menu.open = false;
			menu.querySelector( 'summary' ).focus();
		}
	} );

	// The menus stay open on change: picking several in a row is the point.
	form.addEventListener( 'change', function ( event ) {
		if ( event.target.matches( '[data-filter]' ) ) {
			relabel( event.target.getAttribute( 'data-filter' ) );
			apply( state(), true );
		}
	} );

	// Submitting still works if something reaches the button.
	form.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		apply( state(), true );
	} );

	/**
	 * Read one filter from a URL, in either shape it can arrive in: the comma
	 * form this script writes, or the array form a no-script form submission
	 * leaves behind.
	 */
	function fromParams( params, name ) {
		var packed = params.get( name );
		var values = packed ? packed.split( ',' ) : params.getAll( name + '[]' );

		return values.filter( Boolean );
	}

	// Back and forward move between filtered views rather than leaving the page.
	window.addEventListener( 'popstate', function () {
		var params = new URLSearchParams( window.location.search );

		FILTERS.forEach( function ( name ) {
			setFilter( name, fromParams( params, name ) );
		} );

		apply( state(), false );
	} );
}() );
