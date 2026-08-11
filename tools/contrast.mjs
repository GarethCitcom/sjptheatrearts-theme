/**
 * WCAG 2.2 contrast audit of the design's own colour pairings.
 *
 * The palette is fixed by the design, so the useful question is not "is this
 * hex accessible" but "which pairings the design actually uses are safe, and at
 * what text size". Run this before relying on a pairing in a template.
 *
 * Thresholds: 4.5:1 normal text, 3:1 large text (>=24px, or >=18.66px bold),
 * 3:1 UI component boundaries.
 *
 * Usage: npm run contrast
 */

const C = {
	'brand-orange': '#FE7300',
	'orange-hover': '#e56800',
	'orange-light': '#FF9A3D',
	'orange-text': '#B85400',
	'brand-purple': '#381064',
	'deep-purple': '#2A0B4D',
	'darkest-purple': '#1D0B36',
	magenta: '#C5299B',
	'bt-red': '#F63631',
	'bt-red-hover': '#d92b26',
	'bt-violet': '#791B93',
	'bt-red-light': '#F9AFAD',
	'tint-red': '#FFF1F0',
	'tint-red-fg': '#8A4A46',
	'tint-lilac': '#F5EBFF',
	'tint-lilac-fg': '#6A3AA0',
	'tint-peach': '#FFF5E9',
	'tint-peach-fg': '#96603A',
	'tint-mint': '#EDF8F3',
	'tint-mint-fg': '#2C6B54',
	'page-background': '#F7F6F9',
	surface: '#FFFFFF',
	'text-body': '#211B27',
	'text-secondary': '#5A5265',
	'text-muted': '#736C7E',
	'text-on-purple': '#D9C8EC',
	'text-on-purple-dim': '#B49BD3',
	'success-green': '#26CC8C',
	'badge-purple-bg': '#EFE7F7',
	'badge-purple-fg': '#6A3AA0',
	'badge-orange-bg': '#FFE9D6',
	'badge-orange-fg': '#A34A00',
	'badge-magenta-bg': '#FAE6F4',
	'badge-magenta-fg': '#9C1F7A',
	'badge-green-bg': '#DFF7EC',
	'badge-green-fg': '#0F7350',
};

/**
 * Signed-off exceptions (Gaz, phase 1).
 *
 * The "Enrol now" button ships exactly as designed — white on brand orange —
 * even though it fails AA at 2.74:1. Accepted knowingly in exchange for brand
 * fidelity on the primary sitewide action; it is the reason the accessibility
 * score sits near 91 rather than >= 95 on templates carrying the button.
 * Reported below as WAIVED, and deliberately does not fail the run.
 */
const WAIVED = new Set( [
	'surface on brand-orange',
	'surface on orange-hover',
	// Born To Be, signed off by Gaz in phase 3c on the same basis as the orange.
	// The design's own #d92b26 would have passed at 4.86; #F63631 was kept for
	// brand fidelity. See CLAUDE.md.
	'surface on bt-red',
	'bt-red on surface',
	'bt-red on page-background',
] );

/** Pairings the design actually uses: [foreground, background, where, typical px, bold]. */
const PAIRS = [
	[ 'text-body', 'surface', 'body copy on cards', 16, false ],
	[ 'text-body', 'page-background', 'body copy on page', 16, false ],
	[ 'text-secondary', 'surface', 'secondary copy on cards', 15, false ],
	[ 'text-secondary', 'page-background', 'hero sub-paragraph', 18, false ],
	[ 'text-muted', 'surface', 'meta / label on cards', 12, false ],
	[ 'text-muted', 'page-background', 'meta / label on page', 12, false ],
	[ 'brand-purple', 'surface', 'headings on cards', 20, true ],
	[ 'brand-purple', 'page-background', 'page headings', 52, true ],
	[ 'orange-text', 'surface', 'orange link text on white', 16, false ],
	[ 'orange-text', 'page-background', 'orange link text on page', 16, false ],
	[ 'surface', 'brand-orange', 'button label on orange', 14, true ],
	[ 'surface', 'orange-hover', 'button label, hover', 14, true ],
	[ 'surface', 'brand-purple', 'label on dark button', 14, true ],
	[ 'magenta', 'surface', '"to confirm" placeholder', 13, true ],

	// ---- Born To Be ----
	[ 'surface', 'bt-red', 'Born To Be button label', 15, true ],
	[ 'surface', 'bt-red-hover', 'Born To Be button, hover', 15, true ],
	[ 'bt-red', 'surface', 'Born To Be link and eyebrow', 16, false ],
	[ 'bt-red', 'page-background', 'Born To Be link on page', 16, false ],
	[ 'bt-violet', 'surface', 'Born To Be link hover', 16, false ],
	[ 'surface', 'bt-violet', 'Senior badge label', 12, true ],
	[ 'bt-red-light', 'darkest-purple', 'accent on the dark shows band', 15, false ],
	[ 'tint-red-fg', 'tint-red', 'singing card body', 14, false ],
	[ 'tint-lilac-fg', 'tint-lilac', 'dance card body', 14, false ],
	[ 'tint-peach-fg', 'tint-peach', 'acting card body', 14, false ],
	[ 'tint-mint-fg', 'tint-mint', 'backstage card body', 14, false ],
	[ 'magenta', 'page-background', '"to confirm" on page', 13, true ],
	[ 'text-on-purple', 'deep-purple', 'CTA band body', 16, false ],
	[ 'text-on-purple', 'darkest-purple', 'utility bar text', 12, false ],
	[ 'text-on-purple-dim', 'deep-purple', 'dimmer text on CTA band', 16, false ],
	[ 'text-on-purple-dim', 'darkest-purple', 'utility bar centre text', 12, false ],
	[ 'surface', 'deep-purple', 'CTA band heading', 38, true ],
	[ 'orange-light', 'deep-purple', 'orange accent on dark', 46, true ],
	/*
	 * The Join page's step-3 numeral is #26CC8C on #DFF7EC at 52px in the
	 * design, which is only 1.85:1 — below even the 3:1 large-text floor. It
	 * carries meaning (the step number), so it uses badge-green-fg instead.
	 * success-green remains a decorative/state accent, never a text colour.
	 */
	[ 'badge-green-fg', 'badge-green-bg', 'Join step-3 numeral', 52, true ],
	[ 'badge-purple-fg', 'badge-purple-bg', 'purple badge', 12, true ],
	[ 'badge-orange-fg', 'badge-orange-bg', 'orange badge', 12, true ],
	[ 'badge-magenta-fg', 'badge-magenta-bg', 'magenta badge', 12, true ],
	[ 'badge-green-fg', 'badge-green-bg', 'green badge', 12, true ],
];

const srgb = ( v ) => {
	const c = v / 255;
	return c <= 0.04045 ? c / 12.92 : Math.pow( ( c + 0.055 ) / 1.055, 2.4 );
};

const luminance = ( hex ) => {
	const h = hex.replace( '#', '' );
	const r = parseInt( h.slice( 0, 2 ), 16 );
	const g = parseInt( h.slice( 2, 4 ), 16 );
	const b = parseInt( h.slice( 4, 6 ), 16 );
	return 0.2126 * srgb( r ) + 0.7152 * srgb( g ) + 0.0722 * srgb( b );
};

const ratio = ( a, b ) => {
	const la = luminance( a );
	const lb = luminance( b );
	return ( Math.max( la, lb ) + 0.05 ) / ( Math.min( la, lb ) + 0.05 );
};

// WCAG "large text": >= 24px, or >= 18.66px when bold.
const isLarge = ( px, bold ) => px >= 24 || ( bold && px >= 18.66 );

let failures = 0;
const rows = [];

for ( const [ fg, bg, where, px, bold ] of PAIRS ) {
	const pair = `${ fg } on ${ bg }`;
	const r = ratio( C[ fg ], C[ bg ] );
	const large = isLarge( px, bold );
	const need = large ? 3 : 4.5;
	const pass = r >= need;
	const waived = WAIVED.has( pair );

	if ( ! pass && ! waived ) {
		failures++;
	}

	rows.push( {
		pair,
		where,
		size: `${ px }px${ bold ? ' bold' : '' }`,
		ratio: r.toFixed( 2 ),
		need: need.toFixed( 1 ),
		verdict: pass ? 'pass' : ( waived ? 'WAIVED' : 'FAIL' ),
		large,
	} );
}

const w = ( s, n ) => String( s ).padEnd( n );
console.log( '\nWCAG 2.2 AA — design pairings\n' );
console.log( w( 'pair', 42 ), w( 'size', 12 ), 'ratio'.padStart( 6 ), 'need'.padStart( 5 ), ' verdict' );
console.log( '-'.repeat( 90 ) );

for ( const r of rows ) {
	console.log(
		w( r.pair, 42 ),
		w( r.size, 12 ),
		r.ratio.padStart( 6 ),
		r.need.padStart( 5 ),
		' ' + r.verdict.padEnd( 6 ),
		'pass' === r.verdict ? '' : `  <- ${ r.where }`
	);
}

const waivedCount = rows.filter( ( r ) => 'WAIVED' === r.verdict ).length;

console.log(
	failures
		? `\n${ failures } pairing(s) fail at the size the design uses them.`
		: `\nAll design pairings pass at the sizes used${ waivedCount ? `, with ${ waivedCount } signed-off exception(s).` : '.' }`
);

process.exit( failures ? 1 : 0 );
