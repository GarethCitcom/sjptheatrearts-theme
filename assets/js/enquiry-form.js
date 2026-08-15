/**
 * Enquiry and sign-up forms: send without leaving the page.
 *
 * Enhancement only. Every form this touches is a working POST with scripting
 * off; here the submit is intercepted, the button shows it is sending, and the
 * reply either swaps in the thank-you the server already printed inside a
 * <template>, or writes the server's field errors under the fields exactly as
 * PHP would have. If the endpoint cannot be reached at all the form is
 * submitted the ordinary way, so a flaky connection degrades to a page load
 * rather than to a lost message.
 *
 * Also carries typed values across the "Ready to enrol?" link, so a parent
 * who starts an enquiry and decides to enrol does not type their name twice.
 */
( function () {
	'use strict';

	var SELECTOR = 'form[data-sjpta-form][data-endpoint]';

	function fieldFor( form, name ) {
		return (
			form.querySelector( '[data-field="' + name + '"]' ) ||
			form.querySelector( '[name="' + name + '"]' ) ||
			form.querySelector( '[name="' + name + '[]"]' )
		);
	}

	function clearErrors( form ) {
		form.querySelectorAll( '.sjpta-form__errors, .sjpta-form__error, .sjpta-footer__signuperror' ).forEach( function ( el ) {
			el.remove();
		} );
		form.querySelectorAll( '.has-error' ).forEach( function ( el ) {
			el.classList.remove( 'has-error' );
		} );
		form.querySelectorAll( '[aria-invalid]' ).forEach( function ( el ) {
			el.removeAttribute( 'aria-invalid' );
			el.removeAttribute( 'aria-describedby' );
		} );
	}

	function showErrors( form, errors, summary ) {
		clearErrors( form );

		var names = Object.keys( errors );
		if ( ! names.length ) {
			return;
		}

		var isSignup = form.getAttribute( 'data-sjpta-form' ) === 'newsletter';

		names.forEach( function ( name ) {
			var input = fieldFor( form, name );
			if ( ! input ) {
				return;
			}
			var id = ( input.id || 'sjpta-' + name ) + '-error';
			var msg = document.createElement( 'span' );
			msg.className = isSignup ? 'sjpta-footer__signuperror' : 'sjpta-form__error';
			msg.id = id;
			msg.textContent = errors[ name ];
			input.setAttribute( 'aria-invalid', 'true' );
			input.setAttribute( 'aria-describedby', id );

			var wrap = input.closest( '.sjpta-form__field' ) || form;
			wrap.classList.add( 'has-error' );
			if ( wrap === form ) {
				form.appendChild( msg );
			} else {
				wrap.appendChild( msg );
			}
		} );

		if ( isSignup ) {
			var first = fieldFor( form, names[ 0 ] );
			if ( first ) {
				first.focus();
			}
			return;
		}

		/* The summary first, with a link to each field, as the PHP renders it. */
		var box = document.createElement( 'div' );
		box.className = 'sjpta-form__errors';
		box.setAttribute( 'role', 'alert' );
		box.setAttribute( 'tabindex', '-1' );

		var head = document.createElement( 'p' );
		head.className = 'sjpta-form__errorhead';
		head.textContent = summary || form.getAttribute( 'data-summary' ) || '';
		box.appendChild( head );

		var list = document.createElement( 'ul' );
		names.forEach( function ( name ) {
			var input = fieldFor( form, name );
			var li = document.createElement( 'li' );
			var a = document.createElement( 'a' );
			a.href = '#' + ( input && input.id ? input.id : 'sjpta-' + name );
			a.textContent = errors[ name ];
			li.appendChild( a );
			list.appendChild( li );
		} );
		box.appendChild( list );

		var anchor = form.querySelector( '.sjpta-form__intro, .sjpta-form__heading' );
		if ( anchor ) {
			anchor.insertAdjacentElement( 'afterend', box );
		} else {
			form.insertBefore( box, form.firstChild );
		}
		box.focus();
	}

	function setBusy( form, busy ) {
		var button = form.querySelector( 'button[type="submit"]' );
		var label = form.querySelector( '.sjpta-form__submitlabel' );
		var status = form.querySelector( '.sjpta-form__status' );

		form.classList.toggle( 'is-submitting', busy );
		form.setAttribute( 'aria-busy', busy ? 'true' : 'false' );

		if ( button ) {
			button.disabled = busy;
		}
		if ( label ) {
			if ( busy ) {
				label.setAttribute( 'data-label', label.textContent );
				label.textContent = ( form.getAttribute( 'data-sending' ) || 'Sending' ) + '…';
			} else if ( label.getAttribute( 'data-label' ) ) {
				label.textContent = label.getAttribute( 'data-label' );
			}
		}
		if ( status ) {
			status.textContent = busy ? ( form.getAttribute( 'data-sending' ) || 'Sending' ) : '';
		}
	}

	function showSent( form ) {
		var tpl = form.querySelector( 'template[data-sjpta-sent]' );
		if ( ! tpl ) {
			form.submit();
			return;
		}
		var sent = tpl.content.firstElementChild.cloneNode( true );
		form.replaceWith( sent );

		/* Bring it into view and announce it: the form the visitor was looking at has just vanished. */
		if ( typeof sent.scrollIntoView === 'function' ) {
			sent.scrollIntoView( { block: 'nearest', behavior: 'smooth' } );
		}
		sent.focus( { preventScroll: true } );
	}

	function onSubmit( event ) {
		var form = event.currentTarget;

		if ( form.classList.contains( 'is-submitting' ) ) {
			event.preventDefault();
			return;
		}
		if ( typeof window.fetch !== 'function' || typeof window.FormData !== 'function' ) {
			return;
		}

		event.preventDefault();
		setBusy( form, true );

		fetch( form.getAttribute( 'data-endpoint' ), {
			method: 'POST',
			body: new FormData( form ),
			headers: { Accept: 'application/json' },
			credentials: 'same-origin'
		} )
			.then( function ( response ) {
				if ( ! response.ok ) {
					throw new Error( 'HTTP ' + response.status );
				}
				return response.json();
			} )
			.then( function ( data ) {
				if ( data && data.ok ) {
					showSent( form );
					return;
				}
				setBusy( form, false );
				showErrors( form, ( data && data.errors ) || {}, data && data.summary );
			} )
			.catch( function () {
				/* The endpoint is unreachable or answered nonsense: let the browser do the POST. */
				setBusy( form, false );
				form.removeEventListener( 'submit', onSubmit );
				form.submit();
			} );
	}

	/* "Ready to enrol?": carry the typed values across in the URL. */
	function onPrefill( event ) {
		var link = event.currentTarget;
		var form = link.closest( 'form' );
		if ( ! form || ! window.URL ) {
			return;
		}
		var url;
		try {
			url = new URL( link.href, window.location.href );
		} catch ( e ) {
			return;
		}
		[ 'parent_name', 'child_name', 'child_age', 'email', 'phone', 'class_want' ].forEach( function ( name ) {
			var input = fieldFor( form, name );
			if ( input && input.value && input.value.trim() ) {
				url.searchParams.set( 'pf_' + name, input.value.trim() );
			}
		} );
		link.href = url.toString();
	}

	function init() {
		document.querySelectorAll( SELECTOR ).forEach( function ( form ) {
			form.addEventListener( 'submit', onSubmit );
			form.querySelectorAll( 'a[data-prefill]' ).forEach( function ( link ) {
				link.addEventListener( 'click', onPrefill );
				link.addEventListener( 'auxclick', onPrefill );
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
