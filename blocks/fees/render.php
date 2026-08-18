<?php
/**
 * What it costs.
 *
 * Nothing here comes from the member portal: the feed carries no pricing at all,
 * by design, so fees can only ever be what the client has typed. Every card is
 * an editable field with an honest empty state, because a fee is exactly the
 * kind of fact that must never be invented.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx = sjpta_block_context( $block ?? null );

$sjpta_label = sjpta_field( 'label', __( 'What it costs', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_note  = sjpta_field( 'note', '', $sjpta_ctx );

$sjpta_has_acf = ( null !== $sjpta_ctx && function_exists( 'get_field' ) );

$sjpta_cards    = $sjpta_has_acf ? get_field( 'cards', $sjpta_ctx ) : array();
$sjpta_included = $sjpta_has_acf ? get_field( 'included', $sjpta_ctx ) : array();
$sjpta_extras   = $sjpta_has_acf ? get_field( 'extras', $sjpta_ctx ) : array();

$sjpta_cards    = is_array( $sjpta_cards ) ? $sjpta_cards : array();
$sjpta_included = is_array( $sjpta_included ) ? $sjpta_included : array();
$sjpta_extras   = is_array( $sjpta_extras ) ? $sjpta_extras : array();

$sjpta_inc_title = sjpta_field( 'included_title', __( 'Included in the fee', 'sjptheatrearts' ), $sjpta_ctx );
$sjpta_ext_title = sjpta_field( 'extras_title', __( 'Costs on top, and when', 'sjptheatrearts' ), $sjpta_ctx );

/* Icon per tone, so an editor picks a tone and never an SVG. */
$sjpta_icons = array(
	'orange'  => 'coins',
	'magenta' => 'people',
	'purple'  => 'card',
	'green'   => 'check',
);
?>
<section<?php echo sjpta_anchor_attr( $block ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-fees alignfull">
	<div class="sjpta-inner">
		<h2 class="screen-reader-text"><?php echo esc_html( $sjpta_label ); ?></h2>

		<?php sjpta_section_bar( $sjpta_label, $sjpta_note, '', '' ); ?>

		<?php if ( ! empty( $sjpta_cards ) ) : ?>
			<div class="sjpta-fees__cards" data-reveal data-stagger>
				<?php foreach ( $sjpta_cards as $sjpta_card ) : ?>
					<?php
					$sjpta_title = trim( (string) ( $sjpta_card['title'] ?? '' ) );

					if ( '' === $sjpta_title ) {
						continue;
					}

					$sjpta_tone  = (string) ( $sjpta_card['tone'] ?? 'orange' );
					$sjpta_tone  = isset( $sjpta_icons[ $sjpta_tone ] ) ? $sjpta_tone : 'orange';
					$sjpta_value = trim( (string) ( $sjpta_card['value'] ?? '' ) );
					$sjpta_text  = trim( (string) ( $sjpta_card['text'] ?? '' ) );
					?>
					<div class="sjpta-feecard sjpta-tone--<?php echo esc_attr( $sjpta_tone ); ?>">
						<span class="sjpta-feecard__icon" aria-hidden="true">
							<?php echo sjpta_icon( $sjpta_icons[ $sjpta_tone ], 22, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</span>

						<h3 class="sjpta-feecard__title"><?php echo esc_html( $sjpta_title ); ?></h3>

						<?php
						/*
						 * An empty value is the normal state until the client
						 * confirms the fee, so it renders the placeholder rather
						 * than collapsing the card to a heading and a paragraph.
						 */
						?>
						<p class="sjpta-feecard__value<?php echo '' === $sjpta_value ? ' sjpta-toconfirm' : ''; ?>">
							<?php echo esc_html( '' === $sjpta_value ? __( 'Ask us', 'sjptheatrearts' ) : $sjpta_value ); ?>
						</p>

						<?php if ( '' !== $sjpta_text ) : ?>
							<p class="sjpta-feecard__text"><?php echo esc_html( $sjpta_text ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $sjpta_included ) || ! empty( $sjpta_extras ) ) : ?>
			<div class="sjpta-fees__lists" data-reveal>
				<?php if ( ! empty( $sjpta_included ) ) : ?>
					<div class="sjpta-feelist">
						<h3 class="sjpta-feelist__title"><?php echo esc_html( $sjpta_inc_title ); ?></h3>
						<ul class="sjpta-feelist__items sjpta-feelist__items--ticks">
							<?php foreach ( $sjpta_included as $sjpta_row ) : ?>
								<?php $sjpta_text = trim( (string) ( $sjpta_row['text'] ?? '' ) ); ?>
								<?php if ( '' !== $sjpta_text ) : ?>
									<li>
										<span class="sjpta-feelist__tick" aria-hidden="true">
											<?php echo sjpta_icon( 'check', 15, 'var(--wp--custom--color--success-green)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
										</span>
										<span><?php echo esc_html( $sjpta_text ); ?></span>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $sjpta_extras ) ) : ?>
					<div class="sjpta-feelist">
						<h3 class="sjpta-feelist__title"><?php echo esc_html( $sjpta_ext_title ); ?></h3>
						<ul class="sjpta-feelist__items">
							<?php foreach ( $sjpta_extras as $sjpta_row ) : ?>
								<?php
								$sjpta_name = trim( (string) ( $sjpta_row['title'] ?? '' ) );
								$sjpta_text = trim( (string) ( $sjpta_row['text'] ?? '' ) );
								?>
								<?php if ( '' !== $sjpta_name || '' !== $sjpta_text ) : ?>
									<li>
										<span class="sjpta-feelist__dot" aria-hidden="true">&middot;</span>
										<span>
											<?php if ( '' !== $sjpta_name ) : ?>
												<strong><?php echo esc_html( $sjpta_name ); ?></strong>
											<?php endif; ?>
											<?php echo esc_html( $sjpta_text ); ?>
										</span>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
