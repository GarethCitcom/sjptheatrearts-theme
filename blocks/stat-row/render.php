<?php
/**
 * A row of headline numbers.
 *
 * Two of these are things the site already knows, so they are counted rather
 * than typed: the number of class styles taught, and the number of awarding
 * bodies shown on the page. The prototype hard-coded 13 and 12, which were both
 * out of date before the page was built. A stat nobody can derive, such as how
 * many performing troupes there are, stays an editable field.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_stats = $sjpta_has_acf ? get_field( 'stats', $sjpta_ctx ) : array();
$sjpta_stats = is_array( $sjpta_stats ) ? $sjpta_stats : array();

if ( empty( $sjpta_stats ) ) {
	return;
}
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-statrow alignfull">
	<div class="sjpta-inner">
		<dl class="sjpta-statrow__row" data-reveal>
			<?php foreach ( $sjpta_stats as $sjpta_stat ) : ?>
				<?php
				$sjpta_label = trim( (string) ( $sjpta_stat['label'] ?? '' ) );

				if ( '' === $sjpta_label ) {
					continue;
				}

				$sjpta_source = (string) ( $sjpta_stat['source'] ?? 'typed' );
				$sjpta_value  = sjpta_stat_value( $sjpta_source );

				if ( '' === $sjpta_value ) {
					$sjpta_value = trim( (string) ( $sjpta_stat['value'] ?? '' ) );
				}

				if ( '' === $sjpta_value ) {
					continue;
				}

				$sjpta_tone = (string) ( $sjpta_stat['tone'] ?? 'orange' );

				/*
				 * Only a number counts up. "Baby to adult" is one of these
				 * values, and animating it from zero would be nonsense.
				 */
				$sjpta_countable = (bool) preg_match( '/^\d+$/', $sjpta_value );
				?>
				<div class="sjpta-statrow__stat">
					<dt class="sjpta-statrow__value sjpta-statrow__value--<?php echo esc_attr( $sjpta_tone ); ?>"<?php echo $sjpta_countable ? ' data-count="' . esc_attr( $sjpta_value ) . '"' : ''; ?>>
						<?php echo esc_html( $sjpta_value ); ?>
					</dt>
					<dd class="sjpta-statrow__label"><?php echo esc_html( $sjpta_label ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>
