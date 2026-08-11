<?php
/**
 * Static hero ribbon, as inline SVG.
 *
 * This is the ribbon's resting pose — the exact shape the prototype draws in its
 * `if (reduced)` branch, ported from the same maths rather than eyeballed. It is
 * what touch devices, reduced-motion users and anyone without JavaScript see,
 * and it costs no script at all. On a desktop pointer the canvas upgrades over
 * the top of it and animates.
 *
 * Geometry, from SJP Homepage Alt.dc.html:
 *   pts[i] = ( W*0.72 - t*W*0.44 , H*0.24 + sin(t*3.6)*60 ),  t = i/(N-1)
 * then the ribbon is stroked as two offset edges with a tapering half-width
 *   w = maxW * min(1, t/0.22) * (1-t)^0.85 * 0.5 + 0.4
 * and a sine undulation along the normal.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the ribbon path for a nominal viewBox.
 *
 * Rendered with preserveAspectRatio="none" so it stretches to the hero exactly
 * as the canvas does.
 *
 * @param float $w Nominal viewBox width.
 * @param float $h Nominal viewBox height.
 *
 * @return array{path:string,x1:float,y1:float,x2:float,y2:float}
 */
function sjpta_hero_ribbon_path( float $w = 1440.0, float $h = 760.0 ): array {
	$n     = 18;   // Points in the chain.
	$m     = 10;   // Drawn length at rest: max(8, round(N * 0.55)).
	$max_w = 40.0; // Widest half-width before tapering.

	$pts = array();
	for ( $i = 0; $i < $n; $i++ ) {
		$t     = $i / ( $n - 1 );
		$pts[] = array(
			$w * 0.72 - $t * $w * 0.44,
			$h * 0.24 + sin( $t * 3.6 ) * 60.0,
		);
	}

	$left  = array();
	$right = array();

	for ( $i = 0; $i < $m; $i++ ) {
		$t = $i / ( $m - 1 );

		$a = $pts[ max( $i - 1, 0 ) ];
		$b = $pts[ min( $i + 1, $m - 1 ) ];

		$dx     = $b[0] - $a[0];
		$dy     = $b[1] - $a[1];
		$length = sqrt( $dx * $dx + $dy * $dy );
		$length = $length > 0.0 ? $length : 1.0;
		$dx    /= $length;
		$dy    /= $length;

		// Normal to the direction of travel.
		$px = -$dy;
		$py = $dx;

		$und = sin( $t * 9.42 ) * $t * $t * 14.0;
		$cx  = $pts[ $i ][0] + $px * $und;
		$cy  = $pts[ $i ][1] + $py * $und;

		$half = $max_w * min( 1.0, $t / 0.22 ) * pow( 1.0 - $t, 0.85 ) * 0.5 + 0.4;

		$left[]  = array( $cx + $px * $half, $cy + $py * $half );
		$right[] = array( $cx - $px * $half, $cy - $py * $half );
	}

	/**
	 * Quadratic smoothing, matching the prototype's `smooth()`.
	 *
	 * @param array<int,array{0:float,1:float}> $points Edge points.
	 *
	 * @return string Path commands.
	 */
	$smooth = static function ( array $points ): string {
		$out   = '';
		$count = count( $points );

		for ( $i = 1; $i < $count - 1; $i++ ) {
			$mx   = ( $points[ $i ][0] + $points[ $i + 1 ][0] ) / 2;
			$my   = ( $points[ $i ][1] + $points[ $i + 1 ][1] ) / 2;
			$out .= sprintf(
				' Q %.2f %.2f %.2f %.2f',
				$points[ $i ][0],
				$points[ $i ][1],
				$mx,
				$my
			);
		}

		$last = $points[ $count - 1 ];

		return $out . sprintf( ' L %.2f %.2f', $last[0], $last[1] );
	};

	$reversed = array_reverse( $right );

	$path  = sprintf( 'M %.2f %.2f', $left[0][0], $left[0][1] );
	$path .= $smooth( $left );
	$path .= sprintf( ' L %.2f %.2f', $reversed[0][0], $reversed[0][1] );
	$path .= $smooth( $reversed );
	$path .= ' Z';

	return array(
		'path' => $path,
		'x1'   => $pts[0][0],
		'y1'   => $pts[0][1],
		'x2'   => $pts[ $m - 1 ][0],
		'y2'   => $pts[ $m - 1 ][1],
	);
}

/**
 * The ribbon as inline SVG.
 *
 * @return string
 */
function sjpta_hero_ribbon_svg(): string {
	$w = 1440.0;
	$h = 760.0;

	$r  = sjpta_hero_ribbon_path( $w, $h );
	$id = 'sjpta-ribbon-gradient';

	return sprintf(
		'<svg class="sjpta-hero__ribbon" viewBox="0 0 %1$d %2$d" preserveAspectRatio="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
			<defs>
				<linearGradient id="%3$s" gradientUnits="userSpaceOnUse" x1="%4$.2f" y1="%5$.2f" x2="%6$.2f" y2="%7$.2f">
					<stop offset="0" stop-color="rgba(254,115,0,.55)"/>
					<stop offset="0.5" stop-color="rgba(197,41,155,.42)"/>
					<stop offset="1" stop-color="rgba(106,58,160,.28)"/>
				</linearGradient>
			</defs>
			<path d="%8$s" fill="url(#%3$s)" stroke="url(#%3$s)" stroke-width="1.5" stroke-linejoin="round"/>
		</svg>',
		(int) $w,
		(int) $h,
		esc_attr( $id ),
		$r['x1'],
		$r['y1'],
		$r['x2'],
		$r['y2'],
		esc_attr( $r['path'] )
	);
}
