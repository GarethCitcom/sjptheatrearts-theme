/*
 * Timetable day filter.
 *
 * Enhancement only. Without this file the pills are ordinary links to
 * #timetable-day-N and jump to that day's table, which is a perfectly good way
 * to use the page. This turns them into a filter that hides the other days.
 *
 * Nothing here fetches anything: the whole week is already in the HTML.
 */
( function () {
	'use strict';

	var filter = document.querySelector( '[data-timetable-filter]' );

	if ( ! filter ) {
		return;
	}

	var pills = Array.prototype.slice.call( filter.querySelectorAll( '[data-day]' ) );
	var days = Array.prototype.slice.call( document.querySelectorAll( '.sjpta-timetable__day' ) );
	var none = document.querySelector( '[data-timetable-none]' );

	if ( ! days.length ) {
		return;
	}

	/*
	 * The pills stop being navigation once they filter in place, so they are
	 * relabelled as a group of toggles. Leaving them as links would tell a
	 * screen reader user they are about to be moved somewhere they are not.
	 */
	filter.setAttribute( 'role', 'group' );

	pills.forEach( function ( pill ) {
		pill.setAttribute( 'role', 'button' );
		pill.removeAttribute( 'aria-current' );
		pill.setAttribute( 'aria-pressed', pill.classList.contains( 'is-current' ) ? 'true' : 'false' );
	} );

	function show( day ) {
		var shown = 0;

		days.forEach( function ( group ) {
			var match = ( 'all' === day || group.getAttribute( 'data-day' ) === day );

			group.hidden = ! match;

			if ( match ) {
				shown++;
			}
		} );

		pills.forEach( function ( pill ) {
			var current = ( pill.getAttribute( 'data-day' ) === day );

			pill.classList.toggle( 'is-current', current );
			pill.setAttribute( 'aria-pressed', current ? 'true' : 'false' );
		} );

		if ( none ) {
			none.hidden = ( shown > 0 );
		}
	}

	filter.addEventListener( 'click', function ( event ) {
		var pill = event.target.closest( '[data-day]' );

		if ( ! pill ) {
			return;
		}

		event.preventDefault();
		show( pill.getAttribute( 'data-day' ) );
	} );

	/*
	 * Space and Enter, because these are buttons now. Links fire click on Enter
	 * by themselves but never on Space, and a control announced as a button is
	 * expected to answer both.
	 */
	filter.addEventListener( 'keydown', function ( event ) {
		if ( ' ' !== event.key && 'Spacebar' !== event.key ) {
			return;
		}

		var pill = event.target.closest( '[data-day]' );

		if ( ! pill ) {
			return;
		}

		event.preventDefault();
		show( pill.getAttribute( 'data-day' ) );
	} );
} )();
