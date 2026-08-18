<?php
/**
 * Term status strip.
 *
 * Three facts and a way to ask about them. Two come from the member portal and
 * one is the school's own policy, which is why only the third is editable.
 *
 * The design was drawn while the feed was empty, so it shows "Term dates to
 * confirm" and "No classes, dates to confirm" in the magenta placeholder tone.
 * Those are still exactly what renders when the portal has no active term. Now
 * that it has one, the real dates take their place, which is what the
 * placeholder was always standing in for.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_new   = sjpta_field( 'new_students', __( 'Can start mid-term', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_label = sjpta_field( 'cta_label', __( 'Ask for the current sheet', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_url   = sjpta_field( 'cta_url', '', $sjpta_ctx );

if ( '' === $sjpta_url ) {
	$sjpta_join = get_page_by_path( 'join' );
	$sjpta_url  = $sjpta_join ? (string) get_permalink( $sjpta_join ) : home_url( '/join/' );
}

$sjpta_timetable = sjpta_timetable();
$sjpta_term      = $sjpta_timetable['term'];
$sjpta_half      = $sjpta_timetable['half_term'];

/**
 * A date range in the site's own format, or nothing if it is not a real range.
 *
 * The portal sends its own pre-formatted display strings as well as the raw
 * dates. They are deliberately ignored: they are formatted with the portal's
 * date_format setting, which nobody here controls, so a change over there would
 * silently reformat dates on this site.
 *
 * @param array<string,string>|null $range Range with `start` and `end` keys.
 *
 * @return string
 */
$sjpta_dates = static function ( ?array $range ): string {
	if ( ! is_array( $range ) || empty( $range['start'] ) ) {
		return '';
	}

	$start = strtotime( $range['start'] );
	$end   = ! empty( $range['end'] ) ? strtotime( $range['end'] ) : false;

	if ( ! $start ) {
		return '';
	}

	/*
	 * Months abbreviated. Written out, the three items and the button no longer
	 * fitted the bar on one row and the button dropped to a line of its own.
	 */
	$format = 'j M Y';

	if ( ! $end ) {
		return wp_date( $format, $start );
	}

	// "3 Sep to 20 Dec 2026" rather than repeating the year.
	$short = wp_date( 'Y', $start ) === wp_date( 'Y', $end ) ? 'j M' : $format;

	return sprintf(
		/* translators: 1: start date, 2: end date. */
		__( '%1$s to %2$s', 'sjptheatrearts' ),
		wp_date( $short, $start ),
		wp_date( $format, $end )
	);
};

$sjpta_term_dates = $sjpta_dates( $sjpta_term );
$sjpta_half_dates = $sjpta_dates( $sjpta_half );

/**
 * One item in the strip.
 *
 * @param string $icon  Icon name.
 * @param string $tone  Tone modifier.
 * @param string $label Small label above.
 * @param string $value The fact itself.
 * @param string $fallback What to say when there is no fact yet.
 *
 * @return void
 */
$sjpta_item = static function ( string $icon, string $tone, string $label, string $value, string $fallback = '' ): void {
	$missing = ( '' === $value );
	?>
	<div class="sjpta-termstatus__item">
		<span class="sjpta-termstatus__icon sjpta-termstatus__icon--<?php echo esc_attr( $tone ); ?>" aria-hidden="true">
			<?php echo sjpta_icon( $icon, 20, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
		</span>
		<span class="sjpta-termstatus__text">
			<span class="sjpta-termstatus__label"><?php echo esc_html( $label ); ?></span>
			<span class="sjpta-termstatus__value<?php echo $missing ? ' sjpta-toconfirm' : ''; ?>">
				<?php echo esc_html( $missing ? $fallback : $value ); ?>
			</span>
		</span>
	</div>
	<?php
};
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-termstatus alignfull">
	<div class="sjpta-inner">
		<div class="sjpta-termstatus__bar">
			<?php
			/*
			 * "Current term", as the design labels it, rather than the term's own
			 * name. The name is the wider of the two and pushed the button onto a
			 * line of its own, and "Autumn Term 2026" above the very dates that
			 * say which term it is earns none of that room.
			 */
			$sjpta_item(
				'calendar',
				'orange',
				__( 'Current term', 'sjptheatrearts' ),
				$sjpta_term_dates,
				__( 'Term dates to confirm', 'sjptheatrearts' )
			);
			?>

			<span class="sjpta-termstatus__rule" aria-hidden="true"></span>

			<?php
			/*
			 * The dates alone. "No classes," in front of them said what "Half
			 * term" already says, and the two together were what pushed the bar
			 * onto a second row. The placeholder drops it for the same reason,
			 * so the two states read the same way.
			 */
			$sjpta_item(
				'clock',
				'magenta',
				__( 'Half term', 'sjptheatrearts' ),
				$sjpta_half_dates,
				__( 'Dates to confirm', 'sjptheatrearts' )
			);
			?>

			<span class="sjpta-termstatus__rule" aria-hidden="true"></span>

			<?php $sjpta_item( 'check', 'green', __( 'New students', 'sjptheatrearts' ), $sjpta_new ); ?>

			<a class="sjpta-pillbtn sjpta-termstatus__cta" href="<?php echo esc_url( $sjpta_url ); ?>">
				<?php echo esc_html( $sjpta_label ); ?>
				<span class="sjpta-pillbtn__icon" aria-hidden="true">
					<?php echo sjpta_icon( 'arrow-right', 12, '#381064' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
				</span>
			</a>
		</div>
	</div>
</section>
