/**
 * SJP Theatre Arts motion layer.
 *
 * Everything here is enhancement. The site is fully usable with this file
 * blocked, failing, or disabled -- nothing is hidden by default and no
 * navigation depends on script.
 *
 * Phase 2 implements the condensing header and the mobile menu's polish.
 * Reveal, stagger, lift and count-up arrive with the homepage in phase 3.
 */
( function () {
	'use strict';

	var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ---------- Condensing header ---------- */

	/**
	 * The design condenses the sticky bar past 24px of scroll, deepening its
	 * shadow. Toggled by a class so all the actual styling lives in CSS.
	 */
	function condensingHeader() {
		var header = document.querySelector( '[data-sjpta-header]' );

		if ( ! header ) {
			return;
		}

		var THRESHOLD = 24;
		var ticking = false;

		function apply() {
			ticking = false;
			var stuck = window.scrollY > THRESHOLD;
			header.classList.toggle( 'is-stuck', stuck );
		}

		function onScroll() {
			if ( ticking ) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame( apply );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		apply();
	}

	/* ---------- Mobile menu ---------- */

	/**
	 * The menu is a native <details>, so it already opens, closes and is
	 * keyboard operable without script. This only adds the two conveniences a
	 * <details> cannot express on its own: Escape to close, and closing when a
	 * link is followed to an in-page anchor.
	 */
	function mobileMenu() {
		var menu = document.querySelector( '.sjpta-menu' );

		if ( ! menu ) {
			return;
		}

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && menu.hasAttribute( 'open' ) ) {
				menu.removeAttribute( 'open' );

				var toggle = menu.querySelector( 'summary' );
				if ( toggle ) {
					toggle.focus();
				}
			}
		} );

		menu.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( 'a' );

			if ( link && menu.contains( link ) ) {
				menu.removeAttribute( 'open' );
			}
		} );
	}

	/* ---------- Reveal on scroll ---------- */

	/**
	 * Sections fade and rise into place.
	 *
	 * Critically, the hidden state is applied *by this script*, never by the
	 * stylesheet. If the script fails, is blocked, or never runs, the content is
	 * simply visible — it can never be left invisible. A safety timer forces
	 * everything visible after 4 seconds regardless.
	 */
	function reveal() {
		/*
		 * The homepage sections are named individually because they predate the
		 * attribute; everything built since opts in with `data-reveal`, so a new
		 * block never has to come back and edit this list.
		 */
		var targets = document.querySelectorAll( '.sjpta-hero__copy, .sjpta-agecards, .sjpta-accred, .sjpta-about__bar, .sjpta-about__grid, .sjpta-teens__panel, .sjpta-cta__panel, [data-reveal]' );

		if ( ! targets.length || ! window.IntersectionObserver ) {
			return;
		}

		/**
		 * Reveal an element and any staggered children.
		 *
		 * `.sjpta-reveal` deliberately stays on the element: it defines the
		 * transition as well as the hidden state, so removing it would make the
		 * element appear instantly instead of fading.
		 */
		var shown = function ( el ) {
			el.classList.add( 'is-revealed' );

			var group = el.querySelector( '[data-stagger]' );

			if ( ! group ) {
				return;
			}

			/*
			 * Every child is revealed; only the DELAY is capped.
			 *
			 * This used to slice the list at twelve, which silently left the
			 * thirteenth child onwards at opacity 0 forever. No homepage grid has
			 * more than twelve items so it never showed there, but the class list
			 * has sixteen and four of them were invisible on the front end.
			 * A stagger cap must never decide how much content exists.
			 */
			Array.prototype.forEach.call( group.children, function ( kid, i ) {
				kid.style.transitionDelay = Math.min( i, 11 ) * 70 + 'ms';
				kid.classList.add( 'is-revealed' );
			} );
		};

		Array.prototype.forEach.call( targets, function ( el ) {
			el.classList.add( 'sjpta-reveal' );
		} );

		var observer = new window.IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}

					observer.unobserve( entry.target );
					shown( entry.target );
				} );
			},
			{ threshold: 0.04, rootMargin: '0px 0px -8% 0px' }
		);

		Array.prototype.forEach.call( targets, function ( el ) {
			observer.observe( el );
		} );

		// Safety net: nothing stays hidden, whatever happens above.
		window.setTimeout( function () {
			Array.prototype.forEach.call( targets, shown );
		}, 4000 );
	}

	/* ---------- Counting numbers ---------- */

	/**
	 * Count from zero to the number already in the element.
	 *
	 * The final value is rendered server-side, so this only ever animates
	 * towards something that is already correct. On any failure the true number
	 * is restored rather than a partial count being left on screen.
	 */
	function counters() {
		var nodes = document.querySelectorAll( '[data-count]' );

		if ( ! nodes.length || ! window.IntersectionObserver ) {
			return;
		}

		var observer = new window.IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}

					var el = entry.target;
					observer.unobserve( el );

					var target = parseInt( el.getAttribute( 'data-count' ), 10 );

					if ( isNaN( target ) ) {
						return;
					}

					var started = window.performance.now();
					var format = new Intl.NumberFormat( 'en-GB' );

					function step( now ) {
						var p = Math.min( ( now - started ) / 900, 1 );
						var eased = 1 - Math.pow( 1 - p, 3 );
						el.textContent = format.format( Math.round( target * eased ) );

						if ( p < 1 ) {
							window.requestAnimationFrame( step );
						}
					}

					window.requestAnimationFrame( step );

					// Guarantee the true value even if the loop is interrupted.
					window.setTimeout( function () {
						el.textContent = format.format( target );
					}, 1400 );
				} );
			},
			{ threshold: 0.6 }
		);

		Array.prototype.forEach.call( nodes, function ( el ) {
			observer.observe( el );
		} );
	}

	/* ---------- Boot ---------- */

	function init() {
		// The condensing header is a transition; honour reduced motion by
		// simply not running it. The header stays at its resting size.
		if ( ! reduced ) {
			condensingHeader();
			reveal();
			counters();
		}

		mobileMenu();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
