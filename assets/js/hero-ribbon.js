/**
 * Hero ribbon — the animated upgrade.
 *
 * A pointer-trailing spring chain, ported from the homepage prototype. The
 * still SVG is already in the HTML; this replaces it with a canvas and animates
 * only where that is worth paying for.
 *
 * Gates, all of which must pass:
 *   - a fine pointer (the ribbon follows a cursor; there isn't one on touch)
 *   - motion is welcome (prefers-reduced-motion not set)
 *   - the hero is on screen (the loop stops the moment it scrolls away)
 *
 * Loaded only on the front page, deferred, and separate from motion.js so it
 * cannot eat that file's budget.
 */
( function () {
	'use strict';

	var finePointer = window.matchMedia( '(pointer: fine)' ).matches;
	var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	if ( ! finePointer || reduced ) {
		return;
	}

	var hero = document.querySelector( '[data-sjpta-hero]' );

	// The canvas belongs on the decoration layer, not the section: that layer
	// extends below the hero and behind the age cards, and clips its own
	// overflow, so the ribbon spans the same band as in the design.
	var decor = hero ? hero.querySelector( '.sjpta-hero__decor' ) : null;

	if ( ! hero || ! decor || ! window.requestAnimationFrame ) {
		return;
	}

	var canvas = document.createElement( 'canvas' );
	canvas.className = 'sjpta-hero__canvas';
	canvas.setAttribute( 'aria-hidden', 'true' );

	var ctx = canvas.getContext( '2d' );

	if ( ! ctx ) {
		return;
	}

	decor.appendChild( canvas );
	hero.classList.add( 'has-canvas' );

	// --- Geometry, as the design specifies ---
	var N = 18;
	var SEG = 19;
	var MAX_W = 40;

	var W = 0;
	var H = 0;
	var dpr = Math.min( window.devicePixelRatio || 1, 2 );

	var pts = [];
	var mouse = { x: null, y: null };
	var s1 = { x: 0, y: 0, vx: 0, vy: 0 };
	var s2 = { x: 0, y: 0, vx: 0, vy: 0 };
	var head = { vx: 0, vy: 0 };
	var lenF = 0.55;
	var clock = 0;
	var raf = null;
	var last = 0;

	function resize() {
		var rect = decor.getBoundingClientRect();
		W = rect.width;
		H = rect.height;
		canvas.width = Math.round( W * dpr );
		canvas.height = Math.round( H * dpr );
		ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
	}

	function seed() {
		pts.length = 0;
		for ( var i = 0; i < N; i++ ) {
			pts.push( { x: W * 0.5 - i * SEG, y: H * 0.32, vx: 0, vy: 0 } );
		}
		s1.x = W * 0.5;
		s1.y = H * 0.3;
		s2.x = W * 0.5;
		s2.y = H * 0.3;
	}

	resize();
	seed();

	if ( window.ResizeObserver ) {
		new window.ResizeObserver( resize ).observe( decor );
	} else {
		window.addEventListener( 'resize', resize );
	}

	/*
	 * Track the pointer across the whole painted band, not just the hero.
	 *
	 * In the prototype the hero copy and the age cards live in one <section>
	 * with the canvas at inset:0, so the ribbon follows the cursor over the
	 * cards too. Here they are separate blocks, and the decor layer is what
	 * spans both — so hit-test against that rather than binding to either
	 * section. The layer is pointer-events:none, so this listens on the document
	 * and tests the rectangle instead of relying on the event target.
	 */
	document.addEventListener(
		'pointermove',
		function ( event ) {
			var rect = decor.getBoundingClientRect();
			var inside =
				event.clientX >= rect.left &&
				event.clientX <= rect.right &&
				event.clientY >= rect.top &&
				event.clientY <= rect.bottom;

			if ( inside ) {
				mouse.x = event.clientX - rect.left;
				mouse.y = event.clientY - rect.top;
			} else {
				mouse.x = null;
				mouse.y = null;
			}
		},
		{ passive: true }
	);

	function spring( p, tx, ty, k, damp, dt ) {
		p.vx += ( tx - p.x ) * k * dt;
		p.vy += ( ty - p.y ) * k * dt;
		var f = Math.max( 0, 1 - damp * dt );
		p.vx *= f;
		p.vy *= f;
		p.x += p.vx * dt;
		p.y += p.vy * dt;
	}

	function draw() {
		ctx.clearRect( 0, 0, W, H );

		var M = Math.max( 8, Math.round( N * lenF ) );
		var L = [];
		var R = [];
		var i;

		for ( i = 0; i < M; i++ ) {
			var t = i / ( M - 1 );
			var a = pts[ Math.max( i - 1, 0 ) ];
			var b = pts[ Math.min( i + 1, M - 1 ) ];
			var dx = b.x - a.x;
			var dy = b.y - a.y;
			var dl = Math.hypot( dx, dy ) || 1;
			dx /= dl;
			dy /= dl;

			var px = -dy;
			var py = dx;
			var und = Math.sin( t * 9.42 - clock * 2.5 ) * t * t * 14;
			var cx = pts[ i ].x + px * und;
			var cy = pts[ i ].y + py * und;
			var w = MAX_W * Math.min( 1, t / 0.22 ) * Math.pow( 1 - t, 0.85 ) * 0.5 + 0.4;

			L.push( [ cx + px * w, cy + py * w ] );
			R.push( [ cx - px * w, cy - py * w ] );
		}

		var g = ctx.createLinearGradient( pts[ 0 ].x, pts[ 0 ].y, pts[ M - 1 ].x, pts[ M - 1 ].y );
		g.addColorStop( 0, 'rgba(254,115,0,.55)' );
		g.addColorStop( 0.5, 'rgba(197,41,155,.42)' );
		g.addColorStop( 1, 'rgba(106,58,160,.28)' );

		function smooth( arr ) {
			for ( var j = 1; j < arr.length - 1; j++ ) {
				var mx = ( arr[ j ][ 0 ] + arr[ j + 1 ][ 0 ] ) / 2;
				var my = ( arr[ j ][ 1 ] + arr[ j + 1 ][ 1 ] ) / 2;
				ctx.quadraticCurveTo( arr[ j ][ 0 ], arr[ j ][ 1 ], mx, my );
			}
			ctx.lineTo( arr[ arr.length - 1 ][ 0 ], arr[ arr.length - 1 ][ 1 ] );
		}

		ctx.beginPath();
		ctx.moveTo( L[ 0 ][ 0 ], L[ 0 ][ 1 ] );
		smooth( L );
		var Rr = R.slice().reverse();
		ctx.lineTo( Rr[ 0 ][ 0 ], Rr[ 0 ][ 1 ] );
		smooth( Rr );
		ctx.closePath();

		ctx.fillStyle = g;
		ctx.strokeStyle = g;
		ctx.lineWidth = 1.5;
		ctx.lineJoin = 'round';
		ctx.fill();
		ctx.stroke();
	}

	function loop( now ) {
		raf = window.requestAnimationFrame( loop );

		var dt = Math.min( ( now - last ) / 1000, 0.05 );
		last = now;
		clock += dt;

		var targetLen = null !== mouse.x ? 1 : 0.55;
		lenF += ( targetLen - lenF ) * Math.min( 1, dt * 2.5 );

		var tx;
		var ty;

		if ( null !== mouse.x ) {
			tx = mouse.x;
			ty = mouse.y;
		} else {
			tx = W * 0.5 + Math.sin( clock * 0.5 ) * W * 0.32;
			ty = H * 0.3 + Math.sin( clock * 0.83 ) * H * 0.16;
		}

		spring( s1, tx, ty, 8, 3, dt );
		spring( s2, s1.x + Math.sin( clock ) * 60, s1.y + Math.sin( 2 * clock ) * 30, 20, 4, dt );

		var h = pts[ 0 ];
		var ex = s2.x - h.x;
		var ey = s2.y - h.y;
		head.vx = ( head.vx + ex * 0.044 ) * 0.8;
		head.vy = ( head.vy + ey * 0.044 ) * 0.8;

		var dist = Math.hypot( ex, ey );
		var fr = Math.max( 0, 1 - Math.max( 0, 1 - dist / 40 ) );
		h.x += head.vx * fr;
		h.y += head.vy * fr;

		for ( var i = 1; i < N; i++ ) {
			var p = pts[ i ];
			var par = pts[ i - 1 ];
			var dx = p.x - par.x;
			var dy = p.y - par.y;
			var dl = Math.hypot( dx, dy ) || 1;
			var gx = par.x + ( dx / dl ) * SEG;
			var gy = par.y + ( dy / dl ) * SEG;
			var t = i / ( N - 1 );
			var k = 120 * ( 1 - t * 0.6 );
			var damp = Math.max( 0, 1 - 8 * dt );

			p.vx = ( p.vx + ( gx - p.x ) * k * dt ) * damp;
			p.vy = ( p.vy + ( gy - p.y ) * k * dt ) * damp;
			p.x += p.vx * dt;
			p.y += p.vy * dt;
		}

		draw();
	}

	function start() {
		if ( raf ) {
			return;
		}
		last = window.performance.now();
		raf = window.requestAnimationFrame( loop );
	}

	function stop() {
		if ( ! raf ) {
			return;
		}
		window.cancelAnimationFrame( raf );
		raf = null;
	}

	/*
	 * Only run while the hero is actually visible. The prototype runs its loop
	 * for the lifetime of the page, which burns main-thread time all the way
	 * down a long homepage for something nobody can see.
	 */
	if ( window.IntersectionObserver ) {
		new window.IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						start();
					} else {
						stop();
					}
				} );
			},
			{ threshold: 0 }
		).observe( hero );
	} else {
		start();
	}

	document.addEventListener( 'visibilitychange', function () {
		if ( document.hidden ) {
			stop();
		}
	} );
}() );
