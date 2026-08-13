<?php
/**
 * The weekly timetable.
 *
 * Server rendered, always. The schedule is the reason most people open this
 * page, so it is in the HTML: readable by a search engine, readable with
 * JavaScript off, and not waiting on a request to a third site.
 *
 * The day filter is an enhancement over something that already works. Every day
 * is rendered, the pills are real links to `#day-1`..`#day-7`, and with no
 * JavaScript they jump to that day's table. The script turns them into a filter.
 *
 * A row links to its class page only when the portal's name for the session has
 * been mapped to one. The portal names sessions by level ("Grade 3 Ballet") and
 * this site's pages are the discipline ("Ballet"), so the mapping is explicit
 * and lives on the class. An unmapped row simply has no link, which is honest;
 * guessing would send a parent to the wrong class.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label = sjpta_field( 'label', __( 'Weekly schedule', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_note  = sjpta_field( 'note', __( 'What runs, and when', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_aside = sjpta_field( 'aside_title', '', $sjpta_ctx );
$sjpta_body  = sjpta_field( 'aside_text', '', $sjpta_ctx );

$sjpta_timetable = sjpta_timetable();
$sjpta_sessions  = $sjpta_timetable['sessions'];

/*
 * Group by ISO weekday. Only days that actually have something on are rendered,
 * so an empty Friday is absent rather than shown as an empty table.
 */
$sjpta_days = array();

foreach ( $sjpta_sessions as $sjpta_session ) {
	$sjpta_days[ $sjpta_session['day'] ][] = $sjpta_session;
}

ksort( $sjpta_days );

/**
 * A session's time, in this site's format rather than the portal's.
 *
 * "10:00 to 10:45 am": the meridiem is dropped from the start when both ends
 * share it, which is how the design writes a time and how anyone reading a
 * timetable says one. Falls back to whatever the portal sent if either end is
 * missing, and to the placeholder if there is nothing at all.
 *
 * @param array<string,mixed> $session One mapped session.
 *
 * @return string
 */
$sjpta_zone = static function () use ( $sjpta_timetable ): DateTimeZone {
	try {
		return new DateTimeZone( (string) $sjpta_timetable['timezone'] );
	} catch ( Exception $e ) {
		return new DateTimeZone( 'Europe/London' );
	}
};

$sjpta_time = static function ( array $session ) use ( $sjpta_zone ): string {
	/*
	 * "10:00:00" is a wall clock reading in the feed's own zone, not an instant.
	 * Passing it through strtotime() made a timestamp in the server's zone,
	 * which wp_date() then shifted into Europe/London and moved every class an
	 * hour later. Building the time in the feed's zone and formatting it back
	 * into the same zone leaves the clock face alone, which is the whole point.
	 */
	$zone  = $sjpta_zone();
	$from  = DateTimeImmutable::createFromFormat( 'H:i:s', (string) $session['start'], $zone );
	$until = DateTimeImmutable::createFromFormat( 'H:i:s', (string) ( $session['end'] ?? '' ), $zone );

	if ( ! $from || ! $until ) {
		return (string) $session['time'];
	}

	$start = $from->getTimestamp();
	$end   = $until->getTimestamp();

	$format = (string) get_option( 'time_format', 'g:i a' );
	$from   = wp_date( $format, $start, $zone );
	$to     = wp_date( $format, $end, $zone );

	// Same half of the day: say "am" once, at the end.
	$half = (string) wp_date( 'a', $start, $zone );

	if ( wp_date( 'a', $end, $zone ) === $half ) {
		$from = trim( str_ireplace( $half, '', (string) $from ) );
	}

	return sprintf(
		/* translators: 1: start time, 2: end time. */
		__( '%1$s to %2$s', 'sjptheatrearts' ),
		$from,
		$to
	);
};

$sjpta_names = array(
	1 => array( __( 'Monday', 'sjptheatrearts' ), __( 'Mon', 'sjptheatrearts' ) ),
	2 => array( __( 'Tuesday', 'sjptheatrearts' ), __( 'Tue', 'sjptheatrearts' ) ),
	3 => array( __( 'Wednesday', 'sjptheatrearts' ), __( 'Wed', 'sjptheatrearts' ) ),
	4 => array( __( 'Thursday', 'sjptheatrearts' ), __( 'Thu', 'sjptheatrearts' ) ),
	5 => array( __( 'Friday', 'sjptheatrearts' ), __( 'Fri', 'sjptheatrearts' ) ),
	6 => array( __( 'Saturday', 'sjptheatrearts' ), __( 'Sat', 'sjptheatrearts' ) ),
	7 => array( __( 'Sunday', 'sjptheatrearts' ), __( 'Sun', 'sjptheatrearts' ) ),
);
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-timetable alignfull">
	<div class="sjpta-inner">
		<?php
		/*
		 * The section bar's label is a span, because it is set beside a note and a
		 * row of filter pills rather than as a heading. That left the day names,
		 * which are h3, sitting directly under the page's h1 with nothing in
		 * between: a real heading-order failure, not a pedantic one, because
		 * anyone navigating by heading meets five days with no idea what they are
		 * a list of. This is their parent, exactly as the fees block does it.
		 */
		?>
		<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_label ); ?></h2>

		<div class="sjpta-sectionbar sjpta-timetable__bar">
			<div class="sjpta-sectionbar__text">
				<span class="sjpta-sectionbar__label">
					<span class="sjpta-accent" aria-hidden="true">+</span> <?php echo esc_html( $sjpta_label ); ?>
				</span>
				<?php if ( '' !== $sjpta_note ) : ?>
					<span class="sjpta-sectionbar__note"><?php echo esc_html( $sjpta_note ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $sjpta_days ) ) : ?>
				<?php
				/*
				 * Links, not buttons. Buttons do nothing without JavaScript,
				 * and the design's pills are a way of getting to a day.
				 */
				?>
				<?php
				/*
				 * The pills are built once and printed twice: a row for desktop
				 * and a <details> dropdown for phones, where the row wraps to
				 * three lines. Only one is ever displayed (primitives.css), so
				 * only one is in the accessibility tree — the same deliberate
				 * duplication as the header's two nav lists. data-label is what
				 * the filter script echoes into the dropdown's summary.
				 */
				ob_start();
				?>
				<a class="sjpta-daypill is-current" href="#timetable-days" data-day="all" data-label="<?php esc_attr_e( 'All days', 'sjptheatrearts' ); ?>" aria-current="true">
					<?php esc_html_e( 'All days', 'sjptheatrearts' ); ?>
				</a>
				<?php foreach ( array_keys( $sjpta_days ) as $sjpta_day ) : ?>
					<a class="sjpta-daypill" href="#timetable-day-<?php echo esc_attr( (string) $sjpta_day ); ?>" data-day="<?php echo esc_attr( (string) $sjpta_day ); ?>" data-label="<?php echo esc_attr( $sjpta_names[ $sjpta_day ][0] ); ?>">
						<?php echo esc_html( $sjpta_names[ $sjpta_day ][1] ); ?>
						<span class="screen-reader-text"><?php echo esc_html( $sjpta_names[ $sjpta_day ][0] ); ?></span>
					</a>
				<?php endforeach; ?>
				<?php $sjpta_pills = (string) ob_get_clean(); ?>
				<nav class="sjpta-timetable__filter" aria-label="<?php esc_attr_e( 'Filter the timetable by day', 'sjptheatrearts' ); ?>" data-timetable-filter>
					<div class="sjpta-filterrow">
						<?php echo $sjpta_pills; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped where built above. ?>
					</div>

					<details class="sjpta-filterdrop">
						<summary class="sjpta-filterdrop__toggle">
							<span data-filter-label><?php esc_html_e( 'All days', 'sjptheatrearts' ); ?></span>
							<span class="sjpta-filterdrop__chevron" aria-hidden="true">
								<?php echo sjpta_icon( 'chevron-down', 14, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
							</span>
						</summary>
						<div class="sjpta-filterdrop__panel">
							<?php echo $sjpta_pills; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped where built above. ?>
						</div>
					</details>
				</nav>
			<?php endif; ?>
		</div>

		<?php if ( empty( $sjpta_days ) ) : ?>
			<?php
			/*
			 * No term, or the portal is unreachable and nothing was ever cached.
			 * The designed "to confirm" state, never an error: whether a remote
			 * server answered is not a visitor's problem to read about.
			 */
			?>
			<div class="sjpta-timetable__empty">
				<p class="sjpta-timetable__emptyhead sjpta-toconfirm">
					<?php esc_html_e( 'The timetable for the coming term is being confirmed.', 'sjptheatrearts' ); ?>
				</p>
				<p class="sjpta-timetable__emptytext">
					<?php esc_html_e( 'Ask us and we will send you the current sheet, with the days and times for the class you have in mind.', 'sjptheatrearts' ); ?>
				</p>
			</div>
		<?php else : ?>
			<div class="sjpta-timetable__days" id="timetable-days">
				<?php foreach ( $sjpta_days as $sjpta_day => $sjpta_rows ) : ?>
					<div class="sjpta-timetable__day" id="timetable-day-<?php echo esc_attr( (string) $sjpta_day ); ?>" data-day="<?php echo esc_attr( (string) $sjpta_day ); ?>">
						<div class="sjpta-timetable__dayhead">
							<h3 class="sjpta-timetable__dayname"><?php echo esc_html( $sjpta_names[ $sjpta_day ][0] ); ?></h3>
							<span class="sjpta-timetable__rule" aria-hidden="true"></span>
							<span class="sjpta-timetable__count">
								<?php
								printf(
									/* translators: %d: number of classes on this day. */
									esc_html( _n( '%d class', '%d classes', count( $sjpta_rows ), 'sjptheatrearts' ) ),
									count( $sjpta_rows )
								);
								?>
							</span>
						</div>

						<?php
						/*
						 * A real table. This is tabular data with row and column
						 * headers, and a screen reader user navigating it by cell
						 * needs the header association that only a table gives.
						 */
						?>
						<table class="sjpta-timetable__table">
							<caption class="screen-reader-text">
								<?php
								printf(
									/* translators: %s: day name. */
									esc_html__( 'Classes on %s', 'sjptheatrearts' ),
									esc_html( $sjpta_names[ $sjpta_day ][0] )
								);
								?>
							</caption>
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Time', 'sjptheatrearts' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Class', 'sjptheatrearts' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Ages', 'sjptheatrearts' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Teacher', 'sjptheatrearts' ); ?></th>
									<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Book or read more', 'sjptheatrearts' ); ?></span></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $sjpta_rows as $sjpta_row ) : ?>
									<?php
									$sjpta_class_id = sjpta_timetable_class_id( $sjpta_row['name'] );
									$sjpta_teachers = implode( ', ', $sjpta_row['teachers'] );
									$sjpta_when     = $sjpta_time( $sjpta_row );

									/*
									 * Only sessions the portal will actually take a
									 * booking for. A troupe rehearsal is invitation
									 * only, and offering to book a place nobody can
									 * book is worse than offering nothing.
									 */
									$sjpta_book = $sjpta_row['bookable']
										? sjpta_tempo_booking_url( (int) $sjpta_row['id'] )
										: '';
									?>
									<tr<?php echo $sjpta_row['bookable'] ? '' : ' class="is-invitation"'; ?>>
										<td class="sjpta-timetable__time" data-label="<?php esc_attr_e( 'Time', 'sjptheatrearts' ); ?>">
											<?php if ( '' !== $sjpta_when ) : ?>
												<?php echo esc_html( $sjpta_when ); ?>
											<?php else : ?>
												<span class="sjpta-toconfirm"><?php esc_html_e( 'Time to confirm', 'sjptheatrearts' ); ?></span>
											<?php endif; ?>
										</td>

										<th scope="row" class="sjpta-timetable__class">
											<?php echo esc_html( $sjpta_row['name'] ); ?>
											<?php if ( ! $sjpta_row['bookable'] ) : ?>
												<span class="sjpta-timetable__badge"><?php esc_html_e( 'Invitation only', 'sjptheatrearts' ); ?></span>
											<?php endif; ?>
										</th>

										<td data-label="<?php esc_attr_e( 'Ages', 'sjptheatrearts' ); ?>">
											<?php if ( '' !== $sjpta_row['ages'] ) : ?>
												<?php echo esc_html( $sjpta_row['ages'] ); ?>
											<?php else : ?>
												<span class="sjpta-toconfirm"><?php esc_html_e( 'Ask us', 'sjptheatrearts' ); ?></span>
											<?php endif; ?>
										</td>

										<td data-label="<?php esc_attr_e( 'Teacher', 'sjptheatrearts' ); ?>">
											<?php if ( '' !== $sjpta_teachers ) : ?>
												<?php echo esc_html( $sjpta_teachers ); ?>
											<?php else : ?>
												<span class="sjpta-toconfirm"><?php esc_html_e( 'To confirm', 'sjptheatrearts' ); ?></span>
											<?php endif; ?>
										</td>

										<td class="sjpta-timetable__more">
											<?php
											/*
											 * Both actions name the session for a screen
											 * reader. Twenty rows of "Book" and "Details"
											 * are indistinguishable from one another in a
											 * list of links otherwise.
											 */
											?>
											<?php if ( '' !== $sjpta_book ) : ?>
												<a class="sjpta-timetable__book" href="<?php echo esc_url( $sjpta_book ); ?>"<?php echo sjpta_external_attr( $sjpta_book ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes from sjpta_external_attr(). ?>>
													<?php esc_html_e( 'Book', 'sjptheatrearts' ); ?>
													<span class="screen-reader-text">
														<?php
														printf(
															/* translators: 1: class name, 2: day, 3: time. */
															esc_html__( '%1$s on %2$s at %3$s, on the member portal', 'sjptheatrearts' ),
															esc_html( $sjpta_row['name'] ),
															esc_html( $sjpta_names[ $sjpta_day ][0] ),
															esc_html( $sjpta_when )
														);
														?>
													</span>
													<?php echo sjpta_new_tab_note( $sjpta_book ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_new_tab_note(). ?>
												</a>
											<?php endif; ?>

											<?php if ( $sjpta_class_id ) : ?>
												<a class="sjpta-timetable__link" href="<?php echo esc_url( (string) get_permalink( $sjpta_class_id ) ); ?>">
													<?php esc_html_e( 'Details', 'sjptheatrearts' ); ?>
													<span class="screen-reader-text">
														<?php
														printf(
															/* translators: %s: class name. */
															esc_html__( 'about %s', 'sjptheatrearts' ),
															esc_html( $sjpta_row['name'] )
														);
														?>
													</span>
													<?php echo sjpta_icon( 'arrow-right', 14, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
												</a>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endforeach; ?>

				<?php
				/*
				 * Only reachable by the filter script, so it is hidden until the
				 * script has something to say. Announced politely rather than
				 * assertively: a filter result is not an interruption.
				 */
				?>
				<p class="sjpta-timetable__none" data-timetable-none hidden role="status">
					<?php esc_html_e( 'Nothing on that day.', 'sjptheatrearts' ); ?>
				</p>
			</div>

			<?php if ( 'stale' === $sjpta_timetable['state'] && $sjpta_timetable['fetched'] ) : ?>
				<?php
				/*
				 * The portal is unreachable, so this is the last copy known to be
				 * right. Said plainly and dated, rather than either hiding it or
				 * telling a parent that somebody else's server returned an error.
				 */
				?>
				<p class="sjpta-timetable__asof">
					<?php
					printf(
						/* translators: %s: date the timetable was last confirmed. */
						esc_html__( 'Timetable correct as of %s. Please ask if you need to be certain.', 'sjptheatrearts' ),
						esc_html( wp_date( 'j F Y', (int) $sjpta_timetable['fetched'] ) )
					);
					?>
				</p>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( '' !== $sjpta_aside || '' !== $sjpta_body ) : ?>
			<aside class="sjpta-timetable__aside">
				<span class="sjpta-timetable__asideicon" aria-hidden="true">i</span>
				<p class="sjpta-timetable__asidetext">
					<?php if ( '' !== $sjpta_aside ) : ?>
						<strong><?php echo esc_html( $sjpta_aside ); ?></strong>
					<?php endif; ?>
					<?php echo esc_html( $sjpta_body ); ?>
				</p>
			</aside>
		<?php endif; ?>
	</div>
</section>
