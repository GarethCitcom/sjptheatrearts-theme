<?php
/**
 * Title: Token proof
 * Slug: sjptheatrearts/token-proof
 * Inserter: no
 * Description: Development-only page proving every theme.json token resolves. Not client-facing; delete before launch.
 *
 * Each colour swatch is painted by its CSS custom property, with a chip of the
 * literal hex inside it. If a token fails to resolve, or resolves to the wrong
 * value, the chip stops matching its surround and the failure is visible rather
 * than silent.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_palette = array(
	'brand-orange'       => '#FE7300',
	'orange-hover'       => '#e56800',
	'orange-light'       => '#FF9A3D',
	'orange-text'        => '#B85400',
	'brand-purple'       => '#381064',
	'deep-purple'        => '#2A0B4D',
	'darkest-purple'     => '#1D0B36',
	'magenta'            => '#C5299B',
	'page-background'    => '#F7F6F9',
	'surface'            => '#FFFFFF',
	'card-border'        => '#EAE3F0',
	'inner-divider'      => '#F0EBF5',
	'section-rule'       => '#E9E4F0',
	'nav-divider'        => '#EEE7F4',
	'text-body'          => '#211B27',
	'text-secondary'     => '#5A5265',
	'text-muted'         => '#736C7E',
	'text-on-purple'     => '#D9C8EC',
	'text-on-purple-dim' => '#B49BD3',
	'success-green'      => '#26CC8C',
	'badge-purple-bg'    => '#EFE7F7',
	'badge-purple-fg'    => '#6A3AA0',
	'badge-orange-bg'    => '#FFE9D6',
	'badge-orange-fg'    => '#A34A00',
	'badge-magenta-bg'   => '#FAE6F4',
	'badge-magenta-fg'   => '#9C1F7A',
	'badge-green-bg'     => '#DFF7EC',
	'badge-green-fg'     => '#0F7350',
);

/*
 * Slug => [ sample text, is a heading face ].
 *
 * Slugs deliberately avoid a digit next to a letter: WordPress kebab-cases
 * preset slugs, so "h1-hero" would be emitted as "--wp--preset--font-size--h-1-hero"
 * and every reference to the un-cased name would silently resolve to nothing.
 */
$sjpta_sizes = array(
	'display'    => array( 'Find their place to shine. (76px)', true ),
	'page-title' => array( 'Find the right class (52-58px)', true ),
	'section-lg' => array( 'A school where every child (38-46px)', true ),
	'section-sm' => array( 'What happens in a class (25-36px)', true ),
	'card-title' => array( 'Card heading (17-20px)', true ),
	'hero-body'  => array( 'Hero body copy at 18px / 1.65.', false ),
	'intro'      => array( 'Page intro copy at 17px / 1.65.', false ),
	'body'       => array( 'Body copy at 15-16px / 1.7.', false ),
	'card-body'  => array( 'Card body copy at 14px / 1.6.', false ),
	'fine-print' => array( 'Small print at 13px.', false ),
	'meta'       => array( 'META LABEL 12px', false ),
	'eyebrow'    => array( 'EYEBROW 11-12px', false ),
);

$sjpta_radii = array( 'image', 'small', 'card', 'band', 'pill' );

$sjpta_shadows = array( 'nav', 'frame', 'header-rest', 'header-stuck' );

$sjpta_controls = array(
	'min'     => '44px standard',
	'sidebar' => '46px sidebar CTA',
	'hero'    => '52px hero button',
);
?>
<!-- wp:html -->
<style>
	.sjpta-proof { font-family: var(--wp--preset--font-family--montserrat); }
	.sjpta-proof h2 {
		font-family: var(--wp--preset--font-family--poppins);
		font-size: var(--wp--preset--font-size--h2-small);
		color: var(--wp--preset--color--brand-purple);
		margin: 2.5rem 0 1rem;
	}
	.sjpta-proof__grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
		gap: var(--wp--preset--spacing--40);
	}
	.sjpta-proof__swatch {
		border: 1px solid var(--wp--custom--color--card-border);
		border-radius: var(--wp--custom--radius--small);
		overflow: hidden;
		background: var(--wp--preset--color--surface);
	}
	.sjpta-proof__fill {
		height: 84px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.sjpta-proof__chip {
		width: 34px;
		height: 34px;
		border-radius: 6px;
		box-shadow: 0 0 0 2px rgba(255, 255, 255, .85);
	}
	.sjpta-proof__label {
		padding: 8px 10px;
		font-size: var(--wp--preset--font-size--meta);
		line-height: 1.4;
		color: var(--wp--preset--color--text-secondary);
	}
	.sjpta-proof__label code { font-size: 11px; color: var(--wp--custom--color--text-muted); }
	.sjpta-proof__box {
		background: var(--wp--preset--color--surface);
		border: 1px solid var(--wp--custom--color--card-border);
		height: 96px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: var(--wp--preset--font-size--meta);
		color: var(--wp--preset--color--text-secondary);
	}
	.sjpta-proof__type { margin: 0 0 .75rem; color: var(--wp--preset--color--text-body); }
	.sjpta-proof__ctl {
		display: inline-flex;
		align-items: center;
		padding: 0 22px;
		border-radius: var(--wp--custom--radius--pill);
		background: var(--wp--preset--color--brand-orange);
		color: #fff;
		font-weight: 700;
		font-size: var(--wp--preset--font-size--card-body);
		margin-right: 12px;
	}
</style>

<div class="sjpta-proof">

	<h2>Colour — swatch is the token, chip is the literal hex</h2>
	<div class="sjpta-proof__grid">
		<?php foreach ( $sjpta_palette as $sjpta_slug => $sjpta_hex ) : ?>
			<div class="sjpta-proof__swatch">
				<div class="sjpta-proof__fill" style="background: var(--wp--preset--color--<?php echo esc_attr( $sjpta_slug ); ?>)">
					<span class="sjpta-proof__chip" style="background: <?php echo esc_attr( $sjpta_hex ); ?>"></span>
				</div>
				<div class="sjpta-proof__label">
					<?php echo esc_html( $sjpta_slug ); ?><br><code><?php echo esc_html( strtoupper( $sjpta_hex ) ); ?></code>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Type scale — resize the window to check fluid clamping</h2>
	<?php foreach ( $sjpta_sizes as $sjpta_slug => $sjpta_spec ) : ?>
		<?php
		list( $sjpta_sample, $sjpta_is_heading ) = $sjpta_spec;
		$sjpta_face                              = $sjpta_is_heading ? 'poppins' : 'montserrat';
		$sjpta_weight                            = $sjpta_is_heading ? '700' : '400';
		?>
		<p class="sjpta-proof__type" style="font-size: var(--wp--preset--font-size--<?php echo esc_attr( $sjpta_slug ); ?>); font-family: var(--wp--preset--font-family--<?php echo esc_attr( $sjpta_face ); ?>); font-weight: <?php echo esc_attr( $sjpta_weight ); ?>">
			<?php echo esc_html( $sjpta_sample ); ?>
			<span style="font-family: var(--wp--preset--font-family--montserrat); font-weight: 400; font-size: 11px; color: var(--wp--custom--color--text-muted)">&nbsp;&nbsp;<?php echo esc_html( '--wp--preset--font-size--' . $sjpta_slug ); ?></span>
		</p>
	<?php endforeach; ?>

	<h2>Radii</h2>
	<div class="sjpta-proof__grid">
		<?php foreach ( $sjpta_radii as $sjpta_radius ) : ?>
			<div class="sjpta-proof__box" style="border-radius: var(--wp--custom--radius--<?php echo esc_attr( $sjpta_radius ); ?>)">
				radius / <?php echo esc_html( $sjpta_radius ); ?>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Shadows</h2>
	<div class="sjpta-proof__grid">
		<?php foreach ( $sjpta_shadows as $sjpta_shadow ) : ?>
			<div class="sjpta-proof__box" style="border: 0; border-radius: var(--wp--custom--radius--card); box-shadow: var(--wp--preset--shadow--<?php echo esc_attr( $sjpta_shadow ); ?>)">
				shadow / <?php echo esc_html( $sjpta_shadow ); ?>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Control heights — none may fall below 44px</h2>
	<p>
		<?php foreach ( $sjpta_controls as $sjpta_key => $sjpta_label ) : ?>
			<span class="sjpta-proof__ctl" style="height: var(--wp--custom--control--<?php echo esc_attr( $sjpta_key ); ?>)"><?php echo esc_html( $sjpta_label ); ?></span>
		<?php endforeach; ?>
	</p>

	<h2>Focus state — tab to these</h2>
	<p>
		<a href="#sjpta-proof-focus" id="sjpta-proof-focus">A focusable link</a>
		&nbsp;
		<button type="button" class="wp-element-button">A focusable button</button>
	</p>

</div>
<!-- /wp:html -->
