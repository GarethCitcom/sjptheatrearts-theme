<?php
/**
 * Tempo Book It timetable client.
 *
 * Fetches the public feed from the member portal, validates it, and maps it into
 * a plain array the timetable block can render. Schema in inc/tempo/README.md,
 * documented from the plugin source rather than from observed responses.
 *
 * Nothing here touches the page directly. The caching in cache.php decides when
 * this runs; a visitor never waits on it.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Where the feed lives when nobody has set an address.
 *
 * A confirmed fact from the build brief, so it is a real default rather than an
 * empty string.
 */
const SJPTA_TEMPO_BASE = 'https://book.sjptheatrearts.co.uk';

/** The feed path, fixed by the plugin's REST namespace. */
const SJPTA_TEMPO_PATH = '/wp-json/dsbook/v1/timetable';

/**
 * Five seconds, per the brief.
 *
 * Long enough for a healthy round trip, short enough that a hanging portal
 * cannot hold a cron run open. Visitors are served from cache either way.
 */
const SJPTA_TEMPO_TIMEOUT = 5;

/**
 * The feed's address.
 *
 * @return string
 */
function sjpta_tempo_url(): string {
	$base = sjpta_setting( 'tempo_base_url', SJPTA_TEMPO_BASE );

	return untrailingslashit( $base ) . SJPTA_TEMPO_PATH;
}

/**
 * Where a visitor books a particular session.
 *
 * Built on the feed's base address rather than the `portal_url` setting, even
 * though today they are the same host: a `schedule_id` only means anything on
 * the site that issued it, so the booking link has to follow the feed if the two
 * ever diverge.
 *
 * The portal answers this by sending a signed-out visitor to its sign-in page
 * with the session held in `redirect_to`, so they land on the right booking
 * afterwards. That makes "Book" an action for families who already have an
 * account; new families enrol instead, which is what the header button is for.
 *
 * @param int $schedule_id The feed's id for the session.
 *
 * @return string Empty when there is no id to link to.
 */
function sjpta_tempo_booking_url( int $schedule_id ): string {
	if ( $schedule_id < 1 ) {
		return '';
	}

	$base = sjpta_setting( 'tempo_base_url', SJPTA_TEMPO_BASE );

	return add_query_arg( 'dsb_schedule', $schedule_id, trailingslashit( $base ) );
}

/**
 * Fetch and map the timetable.
 *
 * Returns null on any failure at all: a transport error, an HTTP status other
 * than 200, unparseable JSON, or a body that is not shaped like the feed. The
 * caller decides what to show instead; this function never throws, never warns
 * and never returns half a timetable.
 *
 * Two failures are worth naming because neither looks like "the network is
 * down". The portal answers 403 when the feed toggle is switched off, and 402
 * when its licence lapses. A licence lapse on the portal would otherwise take
 * the marketing site's timetable down with it, silently, so both are treated
 * exactly like an unreachable host: fall through to the last known good copy.
 *
 * @return array<string,mixed>|null
 */
function sjpta_tempo_fetch(): ?array {
	$response = wp_remote_get(
		sjpta_tempo_url(),
		array(
			'timeout'     => SJPTA_TEMPO_TIMEOUT,
			'redirection' => 2,
			'user-agent'  => 'SJPTheatreArts/' . SJPTA_VERSION . '; ' . home_url( '/' ),
			'headers'     => array( 'Accept' => 'application/json' ),
		)
	);

	if ( is_wp_error( $response ) ) {
		sjpta_tempo_log( 'transport: ' . $response->get_error_message() );
		return null;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );

	if ( 200 !== $code ) {
		sjpta_tempo_log( 'http ' . $code );
		return null;
	}

	$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $body ) || ! isset( $body['classes'] ) || ! is_array( $body['classes'] ) ) {
		sjpta_tempo_log( 'body was not a timetable' );
		return null;
	}

	return sjpta_tempo_map( $body );
}

/**
 * Turn the feed's JSON into the array the block renders.
 *
 * Every string is passed through sanitisation here, once, at the boundary. The
 * feed is authored on a different site by people who are not thinking about our
 * markup, so it is treated as untrusted input rather than as our own data.
 *
 * @param array<string,mixed> $body Decoded feed body.
 *
 * @return array<string,mixed>
 */
function sjpta_tempo_map( array $body ): array {
	$sessions = array();

	foreach ( $body['classes'] as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		/*
		 * Keyed off day_of_week, the ISO integer, never day_name. The name is
		 * localised on the portal, so a change of language there would silently
		 * scatter our week grid.
		 */
		$day = isset( $item['day_of_week'] ) ? (int) $item['day_of_week'] : 0;

		if ( $day < 1 || $day > 7 ) {
			continue;
		}

		$teachers = array();

		foreach ( (array) ( $item['teachers'] ?? array() ) as $teacher ) {
			$teacher = sanitize_text_field( (string) $teacher );

			if ( '' !== $teacher ) {
				$teachers[] = $teacher;
			}
		}

		$sessions[] = array(
			'id'       => (int) ( $item['schedule_id'] ?? 0 ),
			'name'     => sanitize_text_field( (string) ( $item['name'] ?? '' ) ),
			'day'      => $day,
			'start'    => sanitize_text_field( (string) ( $item['start_time'] ?? '' ) ),
			'end'      => sanitize_text_field( (string) ( $item['end_time'] ?? '' ) ),

			/*
			 * The portal's own `time_range` is kept only as a fallback. Like its
			 * date strings it is formatted with the portal's settings, which
			 * nobody here controls, so the times are rebuilt from start and end
			 * at render time and this is what shows if either is missing.
			 */
			'time'     => sanitize_text_field( (string) ( $item['time_range'] ?? '' ) ),
			'minutes'  => (int) ( $item['duration_minutes'] ?? 0 ),
			'ages'     => sanitize_text_field( (string) ( $item['ages'] ?? '' ) ),
			'type'     => sanitize_text_field( (string) ( $item['class_type'] ?? '' ) ),
			'location' => sanitize_text_field( (string) ( $item['location'] ?? '' ) ),
			'teachers' => $teachers,

			/*
			 * false means the session is not bookable online, which for this
			 * client means invitation only. The design badges those rows rather
			 * than hiding them: a parent who cannot see the troupe rehearsal
			 * cannot ask how their child might be invited to it.
			 */
			'bookable' => ! isset( $item['online_open'] ) || (bool) $item['online_open'],
		);
	}

	/*
	 * Sorted here rather than trusted from the feed. The plugin does order by
	 * day, time and name, but this array is what the whole page is built on and
	 * a future change at the portal must not be able to reorder our grid.
	 */
	usort(
		$sessions,
		static function ( array $a, array $b ): int {
			return array( $a['day'], $a['start'], $a['name'] ) <=> array( $b['day'], $b['start'], $b['name'] );
		}
	);

	return array(
		'timezone'   => sanitize_text_field( (string) ( $body['timezone'] ?? 'Europe/London' ) ),
		'term'       => sjpta_tempo_map_term( $body['term'] ?? null ),
		'half_term'  => sjpta_tempo_map_break( $body['half_term'] ?? null ),
		'exclusions' => array_values(
			array_filter(
				array_map( 'sjpta_tempo_map_break', (array) ( $body['exclusions'] ?? array() ) )
			)
		),
		'sessions'   => $sessions,
		'fetched'    => time(),
	);
}

/**
 * Map the current term, if one resolves.
 *
 * `term` is null whenever the portal has no active, unexpired term. That is a
 * normal state, not an error: it is what the feed returned for the whole of the
 * build until 2026-08-10.
 *
 * @param mixed $term Raw term.
 *
 * @return array<string,string>|null
 */
function sjpta_tempo_map_term( $term ): ?array {
	if ( ! is_array( $term ) || empty( $term['name'] ) ) {
		return null;
	}

	return array(
		'name'  => sanitize_text_field( (string) $term['name'] ),
		'start' => sanitize_text_field( (string) ( $term['start_date'] ?? '' ) ),
		'end'   => sanitize_text_field( (string) ( $term['end_date'] ?? '' ) ),
	);
}

/**
 * Map a half term or an excluded date.
 *
 * The portal's own display strings are deliberately not used. They are formatted
 * with the portal's date_format option, which nobody here controls, so dates are
 * kept as Y-m-d and formatted against this site's settings at render time.
 *
 * @param mixed $exclusion Raw exclusion.
 *
 * @return array<string,string>|null
 */
function sjpta_tempo_map_break( $exclusion ): ?array {
	if ( ! is_array( $exclusion ) || empty( $exclusion['start_date'] ) ) {
		return null;
	}

	return array(
		'type'  => sanitize_text_field( (string) ( $exclusion['type'] ?? '' ) ),
		'label' => sanitize_text_field( (string) ( $exclusion['label'] ?? '' ) ),
		'start' => sanitize_text_field( (string) $exclusion['start_date'] ),
		'end'   => sanitize_text_field( (string) ( $exclusion['end_date'] ?? '' ) ),
	);
}

/**
 * Record why a fetch failed, without ever surfacing it to a visitor.
 *
 * Kept in an option rather than only in the error log so the reason is visible
 * from WP-CLI on a host where the log is awkward to reach. A parent looking at
 * the timetable must never learn that a remote site returned 402.
 *
 * @param string $reason Short machine-written reason.
 *
 * @return void
 */
function sjpta_tempo_log( string $reason ): void {
	update_option(
		'sjpta_tempo_last_error',
		array(
			'reason' => $reason,
			'when'   => time(),
		),
		false
	);

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( 'SJP Tempo: ' . $reason ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- diagnostic, WP_DEBUG only.
	}
}
