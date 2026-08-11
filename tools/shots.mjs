/**
 * Phase-review screenshot capture.
 *
 * Renders the local dev site at the two widths the acceptance checklist names --
 * 1440px (desktop, matching design-reference/screenshots/*.png) and 390px (the
 * mobile design set) -- so each phase can be compared against the design before
 * sign-off.
 *
 * Usage:
 *   npm run shots                 # every route below
 *   npm run shots -- home classes # named routes only
 *
 * Output: build/shots/<route>-<width>.png (gitignored).
 */

import { chromium } from 'playwright';
import { mkdir } from 'node:fs/promises';
import path from 'node:path';

const BASE = process.env.SJP_BASE_URL ?? 'https://sjp-main.test';
const OUT = 'build/shots';

/** Route key -> path. Keys match the design-reference screenshot numbering. */
const ROUTES = {
	home: '/',
	'born-to-be': '/born-to-be/',
	classes: '/classes/',
	'class-detail': '/classes/jazz-commercial/',
	timetable: '/timetable-and-fees/',
	about: '/about/',
	performances: '/performances/',
	join: '/join/',
	contact: '/contact/',
	'token-proof': '/token-proof/',
};

const WIDTHS = [
	{ label: '1440', width: 1440, height: 900, mobile: false },
	{ label: '390', width: 390, height: 844, mobile: true },
];

const requested = process.argv.slice( 2 );
const selected = requested.length
	? Object.fromEntries( requested.map( ( key ) => [ key, ROUTES[ key ] ] ) )
	: ROUTES;

const missing = Object.entries( selected ).filter( ( [ , value ] ) => ! value );
if ( missing.length ) {
	console.error(
		`Unknown route(s): ${ missing.map( ( [ key ] ) => key ).join( ', ' ) }\n` +
			`Known routes: ${ Object.keys( ROUTES ).join( ', ' ) }`
	);
	process.exit( 1 );
}

await mkdir( OUT, { recursive: true } );

// The dev site uses a Laragon self-signed certificate.
const browser = await chromium.launch();
const context = await browser.newContext( { ignoreHTTPSErrors: true } );

let failures = 0;

for ( const [ key, route ] of Object.entries( selected ) ) {
	for ( const size of WIDTHS ) {
		const page = await context.newPage();
		await page.setViewportSize( { width: size.width, height: size.height } );

		// Screenshots are the design record: freeze motion so reveal animations
		// cannot capture a half-faded section, and keep the result deterministic.
		await page.emulateMedia( { reducedMotion: 'reduce' } );

		const url = new URL( route, BASE ).href;
		const file = path.join( OUT, `${ key }-${ size.label }.png` );

		try {
			const response = await page.goto( url, {
				waitUntil: 'networkidle',
				timeout: 30000,
			} );

			/*
			 * Scroll the whole page before capturing.
			 *
			 * Below-the-fold images are loading="lazy", and a fullPage
			 * screenshot does not scroll — so without this the capture shows
			 * empty boxes where those images belong and every review looks
			 * like a rendering bug that is not there.
			 */
			await page.evaluate( async () => {
				/*
				 * Half a viewport at a time, so every lazy image passes through
				 * the loading root margin rather than being skipped over.
				 *
				 * Deliberately does not set `loading = "eager"` to force the
				 * issue: WordPress emits `sizes="auto"`, which is only valid on
				 * a lazy image, so flipping the attribute makes the browser
				 * re-resolve every srcset against 100vw and start a second
				 * round of requests — which is worse than the problem.
				 */
				const step = Math.round( window.innerHeight / 2 );

				for ( let y = 0; y < document.body.scrollHeight; y += step ) {
					window.scrollTo( 0, y );
					await new Promise( ( r ) => setTimeout( r, 150 ) );
				}

				window.scrollTo( 0, 0 );
			} );

			/*
			 * Grow the viewport to the whole document before the shutter.
			 *
			 * `fullPage` captures beyond the viewport, but Chromium composites
			 * that pass separately and intermittently drops a lazily loaded
			 * image that never shared a frame with the visible area. Making the
			 * viewport tall enough that everything IS visible removes the race
			 * rather than waiting it out.
			 *
			 * Done before the image wait, not after: resizing changes which
			 * srcset candidate each image resolves to, so anything that had
			 * settled beforehand starts loading all over again.
			 */
			const documentHeight = await page.evaluate( () => document.body.scrollHeight );

			await page.setViewportSize( {
				width: size.width,
				height: Math.min( Math.ceil( documentHeight ), 16000 ),
			} );

			await page.waitForLoadState( 'networkidle' ).catch( () => {} );

			/*
			 * `complete` flips true a frame before the bitmap is ready, so wait
			 * for decode() as well. Without it a review shot can show an empty
			 * box under a photograph that is in fact on the page.
			 */
			await page
				.waitForFunction(
					async () => {
						const images = Array.from( document.images );

						if ( ! images.every( ( img ) => img.complete && 0 !== img.naturalWidth ) ) {
							return false;
						}

						await Promise.all( images.map( ( img ) => img.decode().catch( () => {} ) ) );

						return true;
					},
					{ timeout: 20000 }
				)
				.catch( () => console.warn( '    (some images did not finish loading)' ) );

			await page.waitForTimeout( 400 );

			if ( ! response || ! response.ok() ) {
				console.warn(
					`  skip ${ key } @${ size.label }: HTTP ${ response ? response.status() : 'no response' } ${ url }`
				);
				failures++;
				await page.close();
				continue;
			}

			await page.screenshot( { path: file, fullPage: true } );
			console.log( `  ok   ${ file }` );
		} catch ( error ) {
			console.warn( `  fail ${ key } @${ size.label }: ${ error.message }` );
			failures++;
		} finally {
			await page.close();
		}
	}
}

await context.close();
await browser.close();

console.log(
	failures
		? `\nDone with ${ failures } route(s) skipped -- expected until the matching phase is built.`
		: '\nDone.'
);
