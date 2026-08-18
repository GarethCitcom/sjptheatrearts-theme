<?php
/**
 * Parent testimonials.
 *
 * These are real people's words. PRODUCT.md requires consent to be verified
 * before publication and forbids fabricating testimonials — so every quote here
 * is editable, none is hard-coded, and the block renders nothing at all when
 * empty rather than inventing filler.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_ctx    = sjpta_block_context( $block ?? null );
$sjpta_quotes = ( null !== $sjpta_ctx && function_exists( 'get_field' ) ) ? get_field( 'quotes', $sjpta_ctx ) : array();

if ( ! is_array( $sjpta_quotes ) || array() === $sjpta_quotes ) {
	return;
}

$sjpta_tones = array( 'orange', 'magenta', 'purple', 'green' );
?>
<section class="sjpta-quotes alignfull">
	<div class="sjpta-quotes__inner">
		<div class="sjpta-quotes__grid" data-stagger>
			<?php foreach ( $sjpta_quotes as $sjpta_i => $sjpta_quote ) : ?>
				<?php
				$sjpta_text = isset( $sjpta_quote['quote'] ) ? trim( (string) $sjpta_quote['quote'] ) : '';

				if ( '' === $sjpta_text ) {
					continue;
				}

				$sjpta_name    = isset( $sjpta_quote['name'] ) ? trim( (string) $sjpta_quote['name'] ) : '';
				$sjpta_context = isset( $sjpta_quote['context'] ) ? (string) $sjpta_quote['context'] : '';
				$sjpta_tone    = $sjpta_tones[ $sjpta_i % count( $sjpta_tones ) ];

				// Initials from the attributed name, for the avatar disc.
				$sjpta_words    = preg_split( '/\s+/', $sjpta_name );
				$sjpta_words    = is_array( $sjpta_words ) ? $sjpta_words : array();
				$sjpta_initials = '';

				foreach ( $sjpta_words as $sjpta_word ) {
					if ( '' !== $sjpta_word && strlen( $sjpta_initials ) < 2 ) {
						$sjpta_initials .= function_exists( 'mb_substr' ) ? mb_substr( $sjpta_word, 0, 1 ) : substr( $sjpta_word, 0, 1 );
					}
				}
				?>
				<figure class="sjpta-quote">
					<svg class="sjpta-quote__mark sjpta-quote__mark--<?php echo esc_attr( $sjpta_tone ); ?>" width="40" height="31" viewBox="0 0 46 36" fill="none" aria-hidden="true" focusable="false"><path d="M0 36V21C0 9.4 6.2 1.6 18 0l2 6C13.4 7.6 9.6 11.6 9.4 17H19v19H0Zm27 0V21C27 9.4 33.2 1.6 45 0l2 6c-6.6 1.6-10.4 5.6-10.6 11H46v19H27Z" fill="currentColor"/></svg>

					<blockquote class="sjpta-quote__text"><?php echo esc_html( $sjpta_text ); ?></blockquote>

					<?php if ( '' !== $sjpta_name ) : ?>
						<figcaption class="sjpta-quote__by">
							<span class="sjpta-quote__avatar sjpta-quote__avatar--<?php echo esc_attr( $sjpta_tone ); ?>" aria-hidden="true">
								<?php echo esc_html( strtoupper( $sjpta_initials ) ); ?>
							</span>
							<span>
								<span class="sjpta-quote__name"><?php echo esc_html( $sjpta_name ); ?></span>
								<?php if ( '' !== $sjpta_context ) : ?>
									<span class="sjpta-quote__context"><?php echo esc_html( $sjpta_context ); ?></span>
								<?php endif; ?>
							</span>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
