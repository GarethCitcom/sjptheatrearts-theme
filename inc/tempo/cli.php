<?php
/**
 * WP-CLI diagnostics for the timetable feed.
 *
 * The timetable fails quietly by design: an outage shows the last known good
 * copy, and an unmapped session simply renders without a link. Both are the
 * right behaviour for a visitor and both are invisible to whoever maintains the
 * site, so there has to be somewhere to look.
 *
 *     wp sjp timetable            state, term, session count, unmapped names
 *     wp sjp timetable --refresh  force a fetch, ignoring the cache
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Report on the Tempo timetable feed.
 *
 * ## OPTIONS
 *
 * [--refresh]
 * : Drop the cache and fetch from the portal before reporting.
 *
 * @param array<int,string>    $args       Positional arguments.
 * @param array<string,string> $assoc_args Flags.
 *
 * @return void
 */
function sjpta_cli_timetable( array $args, array $assoc_args ): void {
	if ( isset( $assoc_args['refresh'] ) ) {
		sjpta_tempo_flush();
		WP_CLI::log( 'Cache dropped, fetching...' );
	}

	$timetable = sjpta_timetable();

	WP_CLI::log( 'Feed:  ' . sjpta_tempo_url() );

	$states = array(
		'live'    => 'live, from the portal',
		'stale'   => 'STALE, portal unreachable, showing the last known good copy',
		'unknown' => 'NO DATA, portal unreachable and nothing cached',
	);

	WP_CLI::log( 'State: ' . ( $states[ $timetable['state'] ] ?? $timetable['state'] ) );

	$error = get_option( 'sjpta_tempo_last_error' );

	if ( is_array( $error ) ) {
		WP_CLI::warning(
			sprintf(
				'Last failure: %s (%s ago)',
				$error['reason'],
				human_time_diff( (int) $error['when'] )
			)
		);
	}

	if ( null === $timetable['term'] ) {
		WP_CLI::warning( 'No active term in the portal, so the feed carries no classes. This is a portal setting, not a fault here.' );
	} else {
		WP_CLI::log(
			sprintf(
				'Term:  %s, %s to %s',
				$timetable['term']['name'],
				$timetable['term']['start'],
				$timetable['term']['end']
			)
		);
	}

	if ( is_array( $timetable['half_term'] ) ) {
		WP_CLI::log( 'Half term: ' . $timetable['half_term']['start'] . ' to ' . $timetable['half_term']['end'] );
	}

	WP_CLI::log( 'Sessions: ' . count( $timetable['sessions'] ) );

	if ( $timetable['fetched'] ) {
		WP_CLI::log( 'Fetched: ' . human_time_diff( (int) $timetable['fetched'] ) . ' ago' );
	}

	$conflicts = sjpta_timetable_conflicts();

	if ( ! empty( $conflicts['unmatched'] ) ) {
		WP_CLI::warning( count( $conflicts['unmatched'] ) . ' session name(s) link to no class page:' );

		foreach ( $conflicts['unmatched'] as $name ) {
			WP_CLI::log( '  - ' . $name );
		}

		WP_CLI::log( '  Add these under "Timetable names" on the class they belong to.' );
	} else {
		WP_CLI::success( 'Every session links to a class page.' );
	}

	if ( ! empty( $conflicts['claimed'] ) ) {
		WP_CLI::warning( 'Claimed by more than one class, first wins:' );

		foreach ( $conflicts['claimed'] as $clash ) {
			WP_CLI::log( '  - ' . $clash );
		}
	}
}

WP_CLI::add_command( 'sjp timetable', 'sjpta_cli_timetable' );
