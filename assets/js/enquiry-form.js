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
 *
 * Two spam layers live here as well, both invisible to a person. On the
 * first real key press or click inside a form its `data-h` token is copied
 * into the hidden `sjpta_h` field: a script that sets values without touching
 * the page never gets it, and the server then asks more of the timing check.
 * And when the form carries a Turnstile site key (`data-turnstile`),
 * Cloudflare's widget is loaded at that same first interaction, never on page
 * load, and rendered into the form's mount; the token it produces travels
 * with the post. Both fail safe: with scripting off, neither field is filled
 * and the server falls back to its other checks (and, with Turnstile on,
 * quarantines the submission for a person to release).
 */
( function () {
	'use strict';

	var SELECTOR = 'form[data-sjpta-form][data-endpoint]';
	var TURNSTILE_SRC = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
	var TURNSTILE_FIELD = 'sjpta_ts';
	var turnstileLoading = null;

	/* ---------- Interaction token ---------- */

	function markInteracted( form ) {
		var token = form.getAttribute( 'data-h' );
		var field = form.querySelector( 'input[name="sjpta_h"]' );
		if ( token && field && ! field.value ) {
			field.value = token;
		}
		if ( form.getAttribute( 'data-turnstile' ) ) {
			mountTurnstile( form );
		}
	}

	/* ---------- Turnstile ---------- */

	function loadTurnstile() {
		if ( window.turnstile ) {
			return Promise.resolve( window.turnstile );
		}
		if ( turnstileLoading ) {
			return turnstileLoading;
		}
		turnstileLoading = new Promise( function ( resolve, reject ) {
			var script = document.createElement( 'script' );
			script.src = TURNSTILE_SRC;
			script.async = true;
			script.defer = true;
			script.onload = function () {
				if ( window.turnstile ) {
					resolve( window.turnstile );
				} else {
					reject( new Error( 'turnstile missing' ) );
				}
			};
			script.onerror = function () {
				turnstileLoading = null;
				reject( new Error( 'turnstile failed to load' ) );
			};
			document.head.appendChild( script );
		} );
		return turnstileLoading;
	}

	function mountTurnstile( form ) {
		var mount = form.querySelector( '[data-turnstile-mount]' );
		var key = form.getAttribute( 'data-turnstile' );
		if ( ! mount || ! key || mount.getAttribute( 'data-turnstile-id' ) ) {
			return;
		}
		mount.setAttribute( 'data-turnstile-id', 'pending' );
		loadTurnstile()
			.then( function ( turnstile ) {
				var id = turnstile.render( mount, {
					sitekey: key,
					appearance: 'interaction-only',
					'response-field-name': TURNSTILE_FIELD,
					'refresh-expired': 'auto',
					retry: 'auto',
					size: 'flexible'
				} );
				mount.setAttribute( 'data-turnstile-id', id || 'pending' );
			} )
			.catch( function () {
				/* Blocked or unreachable: the form still posts, and the server decides. */
				mount.removeAttribute( 'data-turnstile-id' );
			} );
	}

	function turnstileToken( form ) {
		var field = form.querySelector( 'input[name="' + TURNSTILE_FIELD + '"]' );
		return field && field.value ? field.value : '';
	}

	/* Wait briefly for a token that is on its way; never block a person for long. */
	function awaitTurnstile( form ) {
		if ( ! form.getAttribute( 'data-turnstile' ) ) {
			return Promise.resolve();
		}
		mountTurnstile( form );
		return new Promise( function ( resolve ) {
			var waited = 0;
			var tick = function () {
				if ( turnstileToken( form ) || waited >= 8000 ) {
					resolve();
					return;
				}
				waited += 200;
				window.setTimeout( tick, 200 );
			};
			tick();
		} );
	}

	/* A token is single-use: after a rejected post, ask for a fresh one. */
	function resetTurnstile( form ) {
		var mount = form.querySelector( '[data-turnstile-mount]' );
		var id = mount && mount.getAttribute( 'data-turnstile-id' );
		if ( window.turnstile && id && id !== 'pending' ) {
			try {
				window.turnstile.reset( id );
			} catch ( e ) {
				/* Nothing to do: the next submit simply goes without a token. */
			}
		}
	}

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

		awaitTurnstile( form )
			.then( function () {
				return fetch( form.getAttribute( 'data-endpoint' ), {
					method: 'POST',
					body: new FormData( form ),
					headers: { Accept: 'application/json' },
					credentials: 'same-origin'
				} );
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
				resetTurnstile( form );
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
			var once = function () {
				markInteracted( form );
				[ 'keydown', 'pointerdown', 'touchstart', 'focusin' ].forEach( function ( type ) {
					form.removeEventListener( type, once, true );
				} );
			};
			[ 'keydown', 'pointerdown', 'touchstart', 'focusin' ].forEach( function ( type ) {
				form.addEventListener( type, once, true );
			} );

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
