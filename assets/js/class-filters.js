/**
 * Filter the class list in place.
 *
 * Enhancement only. The bar is a working GET form and the chips are real links,
 * so with this script blocked, failed or disabled every filter still works by
 * reloading. This intercepts that, fetches the same markup from the REST route,
 * and swaps the grid without a reload.
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

	/** The current state of every filter. */
	function state() {
		var age = form.querySelector( '[data-filter-input="age"]' );
		var style = form.querySelector( '[data-filter="style"]' );
		var day = form.querySelector( '[data-filter="day"]' );

		return {
			age: age ? age.value : '',
			style: style ? style.value : '',
			day: day ? day.value : '',
		};
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

	/** Fetch and swap. */
	function apply( filters, push, scrollToList ) {
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

	/** Reflect the chosen age in the chips and the hidden input. */
	function setAge( value ) {
		var input = form.querySelector( '[data-filter-input="age"]' );

		if ( input ) {
			input.value = value;
		}

		form.querySelectorAll( '[data-filter="age"]' ).forEach( function ( chip ) {
			var on = chip.getAttribute( 'data-value' ) === value;

			chip.classList.toggle( 'is-on', on );

			if ( on ) {
				chip.setAttribute( 'aria-current', 'true' );
			} else {
				chip.removeAttribute( 'aria-current' );
			}
		} );
	}

	/*
	 * Listening on the document, not the form: the age-route cards further up the
	 * page carry the same data-filter hooks and mean the same thing, so a route
	 * card filters in place rather than reloading.
	 */
	document.addEventListener( 'click', function ( event ) {
		var chip = event.target.closest( '[data-filter="age"]' );
		var clear = event.target.closest( '[data-filter-clear]' );

		if ( clear ) {
			event.preventDefault();
			setAge( '' );
			form.querySelector( '[data-filter="style"]' ).value = '';
			form.querySelector( '[data-filter="day"]' ).value = '';
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
		setAge( chip.getAttribute( 'data-value' ) );

		/*
		 * A route card sits well above the list, so without this the page would
		 * appear to do nothing: the cards it changed are off screen.
		 */
		var scroll = ! form.contains( chip );

		apply( state(), true, scroll );
	} );

	form.addEventListener( 'change', function ( event ) {
		if ( event.target.matches( '[data-filter="style"], [data-filter="day"]' ) ) {
			apply( state(), true );
		}
	} );

	// Submitting still works if something reaches the button.
	form.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		apply( state(), true );
	} );

	// Back and forward move between filtered views rather than leaving the page.
	window.addEventListener( 'popstate', function () {
		var params = new URLSearchParams( window.location.search );
		var filters = {
			age: params.get( 'age' ) || '',
			style: params.get( 'style' ) || '',
			day: params.get( 'day' ) || '',
		};

		setAge( filters.age );

		var style = form.querySelector( '[data-filter="style"]' );
		var day = form.querySelector( '[data-filter="day"]' );

		if ( style ) {
			style.value = filters.style;
		}

		if ( day ) {
			day.value = filters.day;
		}

		apply( filters, false );
	} );
}() );
