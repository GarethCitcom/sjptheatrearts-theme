/**
 * Minify the theme's own CSS.
 *
 * Our stylesheets are written to be read — generous comments explaining why a
 * rule exists, one declaration per line. That is right for the source and wrong
 * for the wire: on the homepage it costs roughly a third of every stylesheet.
 *
 * This writes a `.min.css` next to each source file. Nothing else changes:
 * inc/enqueue.php swaps in the minified file at request time when one exists,
 * so a fresh clone that has never run this still works, just heavier.
 *
 * Run after editing any CSS:  npm run css
 */

import CleanCSS from 'clean-css';
import { readFile, writeFile, readdir, stat } from 'node:fs/promises';
import path from 'node:path';

const minifier = new CleanCSS( {
	level: {
		1: { all: true },
		2: {
			// Merging adjacent rules is safe; reordering selectors is not, since
			// our cascade deliberately relies on source order in places.
			mergeAdjacentRules: true,
			removeDuplicateRules: true,
			restructureRules: false,
		},
	},
	returnPromise: false,
} );

/** Every stylesheet we author. Vendor CSS is never touched. */
async function sources() {
	const found = [];

	for ( const file of await readdir( 'assets/css' ) ) {
		if ( file.endsWith( '.css' ) && ! file.endsWith( '.min.css' ) ) {
			found.push( path.join( 'assets/css', file ) );
		}
	}

	for ( const dir of await readdir( 'blocks' ) ) {
		const candidate = path.join( 'blocks', dir, 'style.css' );
		try {
			await stat( candidate );
			found.push( candidate );
		} catch {
			// Block has no stylesheet.
		}
	}

	return found;
}

let totalIn = 0;
let totalOut = 0;
const rows = [];

for ( const file of await sources() ) {
	const css = await readFile( file, 'utf8' );
	const result = minifier.minify( css );

	if ( result.errors.length ) {
		console.error( `  ERROR ${ file }: ${ result.errors.join( '; ' ) }` );
		process.exitCode = 1;
		continue;
	}

	for ( const warning of result.warnings ) {
		console.warn( `  warn  ${ file }: ${ warning }` );
	}

	const dest = file.replace( /\.css$/, '.min.css' );
	await writeFile( dest, result.styles );

	totalIn += css.length;
	totalOut += result.styles.length;
	rows.push( { file, before: css.length, after: result.styles.length } );
}

rows.sort( ( a, b ) => b.before - a.before );

const pad = ( s, n ) => String( s ).padEnd( n );
console.log( `\n${ pad( 'file', 34 ) } ${ 'before'.padStart( 8 ) } ${ 'after'.padStart( 8 ) } ${ 'saved'.padStart( 7 ) }` );
console.log( '-'.repeat( 62 ) );

for ( const r of rows ) {
	const saved = Math.round( ( 1 - r.after / r.before ) * 100 );
	console.log(
		`${ pad( r.file, 34 ) } ${ String( r.before ).padStart( 8 ) } ${ String( r.after ).padStart( 8 ) } ${ ( saved + '%' ).padStart( 7 ) }`
	);
}

console.log(
	`\n${ rows.length } file(s): ${ totalIn } → ${ totalOut } bytes ` +
		`(${ Math.round( ( 1 - totalOut / totalIn ) * 100 ) }% smaller, ${ totalIn - totalOut } saved)`
);
