/**
 * Convert the design photography to correctly sized WebP, ready for import.
 *
 * The brief requires photography to be converted and sized *before* it reaches
 * the media library, so WordPress generates its srcset from an already-optimised
 * original rather than a camera-sized JPEG.
 *
 * Output: build/media/*.webp  (then imported with `wp media import`)
 *
 * Usage: npm run media
 */

import sharp from 'sharp';
import { mkdir, readdir, stat } from 'node:fs/promises';
import path from 'node:path';

const SRC = 'design-reference/assets';
const OUT = 'build/media';

/*
 * Max width per role. Nothing is upscaled — sharp's `withoutEnlargement` keeps
 * a smaller original at its own size.
 *
 * 1600px covers the widest a photo is ever displayed (the teens band's
 * background on a 1320px container at 2x is the outlier); cards and avatars are
 * far smaller and are capped accordingly.
 */
const ROLES = {
	hero: 1600,
	card: 900,
	avatar: 240,
	logo: 320,
	brand: 400,
};

/** file => role. Only what the homepage actually uses in phase 3a. */
const FILES = {
	'new-media/web/IMG_8003.jpg': 'card',
	'new-media/web/IMG_0310.jpg': 'card',
	'new-media/web/IMG_8006.jpg': 'card',
	'new-media/web/IMG_8007.jpg': 'avatar',
	'new-media/web/IMG_0300.jpg': 'avatar',
	'new-media/web/IMG_0316.jpg': 'avatar',
	'new-media/web/xmas-3.jpg': 'hero',
	'new-media/web/full-cast.jpg': 'hero',
	'new-media/web/IMG_0305.jpg': 'card',
	'new-media/web/IMG_0318.jpg': 'card',
	'new-media/web/IMG_8011.jpg': 'card',
	'new-media/web/xmas-2.jpg': 'card',
	'web-pic.jpg': 'card',
	'sj.jpg': 'avatar',
	'singing.jpg': 'avatar',

	/*
	 * Born To Be (phase 3c). `hero` is reserved for the three that span two
	 * gallery columns or fill the dark band, roughly 640px wide and so 1280px at
	 * 2x; every other placement on that page is a single 302px column or a
	 * collage panel, well inside `card`.
	 *
	 * logo-square.jpg is deliberately absent: the design never uses it, and an
	 * unused file in the media library is one more thing for an editor to pick
	 * by mistake.
	 */
	'born-to-be/cast-seussical.jpg': 'hero',
	'born-to-be/cast-group.jpg': 'hero',
	'born-to-be/rehearsal-group.jpg': 'hero',
	'born-to-be/costume-closeup.jpg': 'card',
	'born-to-be/rehearsal-alice.jpg': 'card',
	'born-to-be/rehearsal-costumes.jpg': 'card',
	'born-to-be/rehearsal-jump.jpg': 'card',
	'born-to-be/seussical-scene.jpg': 'card',
	'born-to-be/seussical-solo.jpg': 'card',
	'born-to-be/solo-stage.jpg': 'card',
	'born-to-be/logo.png': 'brand',

	/*
	 * About (phase 6). SJ's portrait fills a 500px column at 2x, so it is `hero`;
	 * the teaching team photo sits in a 620px panel and is `card`.
	 */
	'sj-headshot.jpg': 'hero',
	'team.jpg': 'hero',

	/*
	 * Performances (phase 6). The two gallery tiles that span two columns are
	 * `hero`; the year cards sit in a 400px column and are `card`.
	 */
	'new-media/web/IMG_8884.jpg': 'card',
	'new-media/web/IMG_8010.jpg': 'card',
	'ballet-feet.png': 'card',
};

/** Accreditation marks keep their alpha, so they stay PNG-sourced but convert too. */
const LOGOS = [ 'idta.png', 'lamda.png', 'uka.png', 'adfp.jpg', 'safeguarding-white.png' ];

await mkdir( OUT, { recursive: true } );

let totalIn = 0;
let totalOut = 0;
const rows = [];

async function convert( relative, role ) {
	const src = path.join( SRC, relative );

	let info;
	try {
		info = await stat( src );
	} catch {
		console.warn( `  missing: ${ relative }` );
		return;
	}

	/*
	 * Output names are flat, so anything under design-reference/assets/born-to-be
	 * carries the folder as a prefix. Without it `born-to-be/logo.png` would land
	 * as a bare `logo.webp` next to the SJP marks, which is exactly the kind of
	 * ambiguity an editor picks the wrong side of.
	 */
	const folder = path.dirname( relative ) === 'born-to-be' ? 'born-to-be-' : '';
	const name   = folder + path.basename( relative ).replace( /\.(jpe?g|png)$/i, '' ).toLowerCase();
	const dest = path.join( OUT, `${ name }.webp` );

	const image = sharp( src ).rotate();
	const meta = await image.metadata();

	await image
		.resize( { width: ROLES[ role ], withoutEnlargement: true } )
		.webp( { quality: 78, effort: 5 } )
		.toFile( dest );

	const after = await stat( dest );

	totalIn += info.size;
	totalOut += after.size;

	rows.push( {
		name: `${ name }.webp`,
		role,
		from: `${ meta.width }×${ meta.height }`,
		before: Math.round( info.size / 1024 ),
		after: Math.round( after.size / 1024 ),
	} );
}

for ( const [ file, role ] of Object.entries( FILES ) ) {
	await convert( file, role );
}

for ( const file of LOGOS ) {
	await convert( file, 'logo' );
}

const pad = ( s, n ) => String( s ).padEnd( n );
console.log( `\n${ pad( 'file', 26 ) } ${ pad( 'role', 8 ) } ${ pad( 'source', 12 ) } ${ 'KB in'.padStart( 7 ) } ${ 'KB out'.padStart( 7 ) }` );
console.log( '-'.repeat( 68 ) );
for ( const r of rows ) {
	console.log( `${ pad( r.name, 26 ) } ${ pad( r.role, 8 ) } ${ pad( r.from, 12 ) } ${ String( r.before ).padStart( 7 ) } ${ String( r.after ).padStart( 7 ) }` );
}

console.log(
	`\n${ rows.length } file(s): ${ Math.round( totalIn / 1024 ) }KB → ${ Math.round( totalOut / 1024 ) }KB ` +
		`(${ Math.round( ( 1 - totalOut / totalIn ) * 100 ) }% smaller)`
);
console.log( `\nOutput: ${ OUT }/` );
