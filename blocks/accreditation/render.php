<?php
/**
 * Accreditation row.
 *
 * The marks are shown desaturated at reduced opacity, per the design. They are
 * still meaningful, so each keeps its alt text rather than being decorative —
 * "recognised by IDTA" is exactly the reassurance a parent is scanning for.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx    = sjpta_block_context( $block ?? null );
$sjpta_strong = sjpta_field( 'intro_strong', '', $sjpta_ctx );
$sjpta_intro  = sjpta_field( 'intro', '', $sjpta_ctx );
$sjpta_logos  = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'logos', $sjpta_ctx ) : array();

if ( ! is_array( $sjpta_logos ) ) {
	$sjpta_logos = array();
}

if ( '' === $sjpta_strong && '' === $sjpta_intro && array() === $sjpta_logos ) {
	return;
}

/*
 * The homepage runs this row bare across the page background; About sets it in
 * a bordered panel, because there it closes a run of panels and an unboxed row
 * beneath them reads as though it fell out of one.
 *
 * Read directly rather than through sjpta_field(), which only ever returns a
 * string and so silently discards a true/false field.
 */
$sjpta_panel = ( null !== $sjpta_ctx && function_exists( 'get_field' ) )
	? (bool) get_field( 'panel', $sjpta_ctx )
	: false;
?>
<section class="sjpta-accred alignfull<?php echo $sjpta_panel ? ' sjpta-accred--panel' : ''; ?>">
	<div class="sjpta-accred__inner">
		<?php
		/*
		 * The panel variant needs an inner box to carry the border, because the
		 * wrapper already holds the container's max-width and padding. The bare
		 * variant must not have one: the row's flex layout lives on the wrapper,
		 * so a box between it and the two children makes them a single flex item
		 * that shrinks to its content and stacks, which is what happened.
		 */
		if ( $sjpta_panel ) :
			?>
		<div class="sjpta-accred__box">
		<?php endif; ?>
		<?php if ( '' !== $sjpta_strong || '' !== $sjpta_intro ) : ?>
			<p class="sjpta-accred__intro">
				<?php if ( '' !== $sjpta_strong ) : ?>
					<strong><?php echo esc_html( $sjpta_strong ); ?></strong>
				<?php endif; ?>
				<?php echo esc_html( $sjpta_intro ); ?>
			</p>
		<?php endif; ?>

		<?php if ( array() !== $sjpta_logos ) : ?>
			<ul class="sjpta-accred__logos">
				<?php foreach ( $sjpta_logos as $sjpta_logo ) : ?>
					<?php
					$sjpta_id = isset( $sjpta_logo['image'] ) ? (int) $sjpta_logo['image'] : 0;

					if ( ! $sjpta_id ) {
						continue;
					}

					$sjpta_height = isset( $sjpta_logo['height'] ) ? (int) $sjpta_logo['height'] : 38;
					$sjpta_height = $sjpta_height > 0 ? $sjpta_height : 38;
					?>
					<li class="sjpta-accred__logo" style="--sjpta-logo-height: <?php echo esc_attr( (string) $sjpta_height ); ?>px">
						<?php
						echo wp_get_attachment_image(
							$sjpta_id,
							'medium',
							false,
							array(
								'loading' => 'lazy',
								'sizes'   => '160px',
							)
						);
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $sjpta_panel ) : ?>
		</div>
		<?php endif; ?>
	</div>
</section>
