<?php
/**
 * Timetable caching, and the one function the page calls.
 *
 * Two layers, because they answer different questions:
 *
 * 1. A transient, holding the current answer for the configured TTL. This is the
 *    normal path and it is what keeps the page fast.
 * 2. A "last known good" option, rewritten on every successful fetch and never
 *    expiring. This is what the page falls back to when the portal is down,
 *    unlicensed, or answering with something that is not a timetable.
 *
 * The page must never white-screen, never block on the remote, and never show a
 * visitor an error about somebody else's server. Worst case it shows the last
 * timetable we know to have been right, dated honestly; if there has never been
 * a good fetch, it shows the designed "to confirm" state.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Transient holding the current answer. */
const SJPTA_TEMPO_TRANSIENT = 'sjpta_tempo_timetable';

/** Option holding the last timetable that was known to be good. */
const SJPTA_TEMPO_BACKUP = 'sjpta_tempo_last_good';

/** Cron hook that refreshes the cache before anyone needs it. */
const SJPTA_TEMPO_CRON = 'sjpta_tempo_refresh';

/**
 * How long the transient lives.
 *
 * Fifteen minutes by default, per the brief. The remote sends
 * Cache-Control: max-age=300, so ours sits comfortably above it; the cron
 * prewarm is what actually keeps visitors off the round trip.
 *
 * @return int Seconds.
 */
function sjpta_tempo_ttl(): int {
	$minutes = 15;

	/*
	 * Read directly rather than through sjpta_setting(), which returns a string
	 * and rejects anything else. ACF's number field hands back an int, so the
	 * helper would have discarded the client's value and quietly used the
	 * default for ever.
	 */
	if ( function_exists( 'get_field' ) ) {
		$saved = get_field( 'tempo_cache_minutes', 'option' );

		if ( is_numeric( $saved ) && (int) $saved > 0 ) {
			$minutes = (int) $saved;
		}
	}

	// A zero or negative TTL would put every visitor on the remote round trip.
	return max( MINUTE_IN_SECONDS, $minutes * MINUTE_IN_SECONDS );
}

/**
 * The timetable, as the page should render it.
 *
 * Never returns null. The `state` key says which of the three situations this
 * is, so the block can be honest about it rather than guessing from whether the
 * session list happens to be empty:
 *
 * - `live`    fetched or cached from a healthy feed.
 * - `stale`   the feed failed, this is the last copy known to be good.
 * - `unknown` the feed failed and there has never been a good copy.
 *
 * @return array<string,mixed>
 */
function sjpta_timetable(): array {
	$cached = get_transient( SJPTA_TEMPO_TRANSIENT );

	if ( is_array( $cached ) ) {
		return $cached + array( 'state' => 'live' );
	}

	$fresh = sjpta_tempo_fetch();

	if ( null !== $fresh ) {
		set_transient( SJPTA_TEMPO_TRANSIENT, $fresh, sjpta_tempo_ttl() );

		/*
		 * Autoload off. This array carries every session in the term and is
		 * needed on exactly one page, so loading it into memory on every request
		 * across the whole site would be pure waste.
		 */
		update_option( SJPTA_TEMPO_BACKUP, $fresh, false );
		delete_option( 'sjpta_tempo_last_error' );

		return $fresh + array( 'state' => 'live' );
	}

	$backup = get_option( SJPTA_TEMPO_BACKUP );

	if ( is_array( $backup ) && ! empty( $backup['sessions'] ) ) {
		/*
		 * A short transient on the failure too. Without it every visitor during
		 * an outage pays a five second timeout, which turns somebody else's
		 * downtime into our own slow site.
		 */
		set_transient( SJPTA_TEMPO_TRANSIENT, $backup, 5 * MINUTE_IN_SECONDS );

		return $backup + array( 'state' => 'stale' );
	}

	return array(
		'timezone'   => 'Europe/London',
		'term'       => null,
		'half_term'  => null,
		'exclusions' => array(),
		'sessions'   => array(),
		'fetched'    => 0,
		'state'      => 'unknown',
	);
}

/**
 * Refresh the cache on a schedule, so no visitor pays the round trip.
 *
 * @return void
 */
function sjpta_tempo_refresh(): void {
	$fresh = sjpta_tempo_fetch();

	if ( null === $fresh ) {
		return;
	}

	set_transient( SJPTA_TEMPO_TRANSIENT, $fresh, sjpta_tempo_ttl() );
	update_option( SJPTA_TEMPO_BACKUP, $fresh, false );
	delete_option( 'sjpta_tempo_last_error' );
}
add_action( SJPTA_TEMPO_CRON, 'sjpta_tempo_refresh' );

/**
 * Keep the prewarm scheduled.
 *
 * Checked on init rather than only on theme activation, so a site whose cron
 * table was cleared, or which was migrated between hosts, quietly repairs itself
 * instead of serving a timetable that stops updating.
 *
 * @return void
 */
function sjpta_tempo_schedule(): void {
	if ( ! wp_next_scheduled( SJPTA_TEMPO_CRON ) ) {
		wp_schedule_event( time() + MINUTE_IN_SECONDS, 'sjpta_tempo_interval', SJPTA_TEMPO_CRON );
	}
}
add_action( 'init', 'sjpta_tempo_schedule' );

/**
 * A schedule that follows the configured TTL.
 *
 * Core's shortest built-in interval is hourly, which would leave a fifteen
 * minute TTL expiring three times between refreshes and putting whichever
 * visitor arrives first onto the remote.
 *
 * @param array<string,array<string,mixed>> $schedules Registered schedules.
 *
 * @return array<string,array<string,mixed>>
 */
function sjpta_tempo_interval( $schedules ): array {
	$schedules = is_array( $schedules ) ? $schedules : array();

	$schedules['sjpta_tempo_interval'] = array(
		'interval' => sjpta_tempo_ttl(),
		'display'  => __( 'SJP timetable refresh', 'sjptheatrearts' ),
	);

	return $schedules;
}
add_filter( 'cron_schedules', 'sjpta_tempo_interval' ); // phpcs:ignore WordPress.WP.CronInterval.ChangeDetected -- interval follows the configured TTL, minimum 60s.

/**
 * Drop the cached copies.
 *
 * The transient only. The last known good copy is deliberately kept, because it
 * is the safety net: clearing it during an outage would replace a slightly old
 * timetable with no timetable at all.
 *
 * @return void
 */
function sjpta_tempo_flush(): void {
	delete_transient( SJPTA_TEMPO_TRANSIENT );
}

/**
 * Clear the cache when the feed's settings change.
 *
 * Without this, pointing the site at a different portal leaves the old
 * timetable on screen until the TTL runs out, which reads as the setting having
 * been ignored.
 *
 * @return void
 */
function sjpta_tempo_flush_on_save(): void {
	sjpta_tempo_flush();
	wp_clear_scheduled_hook( SJPTA_TEMPO_CRON );
}
add_action( 'acf/options_page/save', 'sjpta_tempo_flush_on_save' );
