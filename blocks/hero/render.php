<?php
/**
 * Homepage hero.
 *
 * Background is four layers: a dot grid, two soft radial glows, and the ribbon.
 * All are decorative and none of them carry meaning, so all are aria-hidden and
 * none block the content above them.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/ribbon.php';

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_eyebrow   = sjpta_field( 'eyebrow', __( 'All ages · all abilities', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_heading   = sjpta_field( 'heading', __( "Find their place\nto shine.", 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_highlight = sjpta_field( 'highlight', __( 'shine.', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_intro     = sjpta_field( 'intro', __( 'Dance, sing and perform with welcoming, industry-trained teachers. Classes for babies, children and teens of every ability, plus adult ballet and ballroom.', 'sjptheatrearts' ), $sjpta_ctx );

$sjpta_primary_label   = sjpta_field( 'primary_label', __( 'Enrol now', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_secondary_label = sjpta_field( 'secondary_label', __( 'Find the right class', 'sjptheatrearts' ), $sjpta_ctx );

$sjpta_join    = get_page_by_path( 'join' );
$sjpta_classes = get_page_by_path( 'classes' );

$sjpta_primary_url   = sjpta_field( 'primary_url', $sjpta_join ? (string) get_permalink( $sjpta_join ) : home_url( '/join/' ), $sjpta_ctx );
$sjpta_secondary_url = sjpta_field( 'secondary_url', $sjpta_classes ? (string) get_permalink( $sjpta_classes ) : home_url( '/classes/' ), $sjpta_ctx );
?>
<?php
/*
 * alignfull: the section paints a full-bleed background, so it has to escape
 * both the global side padding and the 1320px constrained layout it sits in.
 * Its own __inner re-establishes the container. See CLAUDE.md, "Full-bleed
 * sections".
 */
?>
<section class="sjpta-hero alignfull" data-sjpta-hero>
	<?php
	/*
	 * All decoration lives on one clipped layer that extends below the section,
	 * because in the design this backdrop runs from the top of the page down
	 * behind the age cards and stops at the ticker. Hero and age cards are
	 * separate blocks here so the client can edit them independently, so the
	 * layer bleeds past its own section by --sjpta-hero-bleed instead.
	 *
	 * The layer clips its own overflow, which is what stops the two glows —
	 * positioned outside the viewport at left:-160px and right:-180px — from
	 * creating a horizontal scrollbar.
	 */
	?>
	<div class="sjpta-hero__decor" aria-hidden="true">
		<span class="sjpta-hero__grid"></span>
		<span class="sjpta-hero__glow sjpta-hero__glow--orange"></span>
		<span class="sjpta-hero__glow sjpta-hero__glow--magenta"></span>
		<?php echo sjpta_hero_ribbon_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated markup, values escaped inside. ?>
	</div>

	<div class="sjpta-hero__inner">
		<div class="sjpta-hero__copy">
			<?php if ( '' !== $sjpta_eyebrow ) : ?>
				<span class="sjpta-hero__eyebrow">
					<svg width="12" height="12" viewBox="0 0 24 24" fill="#FE7300" aria-hidden="true" focusable="false"><path d="M12 0c1 6.5 5.5 11 12 12-6.5 1-11 5.5-12 12-1-6.5-5.5-11-12-12 6.5-1 11-5.5 12-12Z"/></svg>
					<?php echo esc_html( $sjpta_eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h1 class="sjpta-hero__heading">
				<?php
				echo wp_kses(
					sjpta_highlight_words( $sjpta_heading, $sjpta_highlight ),
					array(
						'span' => array( 'class' => array() ),
						'br'   => array(),
					)
				);
				?>
			</h1>

			<?php if ( '' !== $sjpta_intro ) : ?>
				<p class="sjpta-hero__intro"><?php echo esc_html( $sjpta_intro ); ?></p>
			<?php endif; ?>

			<div class="sjpta-hero__actions">
				<?php if ( '' !== $sjpta_primary_label ) : ?>
					<a class="sjpta-btn sjpta-btn--plum sjpta-hero__btn" href="<?php echo esc_url( $sjpta_primary_url ); ?>">
						<?php echo esc_html( $sjpta_primary_label ); ?>
					</a>
				<?php endif; ?>

				<?php if ( '' !== $sjpta_secondary_label ) : ?>
					<a class="sjpta-btn sjpta-btn--outline sjpta-hero__btn" href="<?php echo esc_url( $sjpta_secondary_url ); ?>">
						<?php echo esc_html( $sjpta_secondary_label ); ?>
						<?php echo sjpta_icon( 'arrow-right', 15, '#381064' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
