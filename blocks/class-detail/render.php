<?php
/**
 * A class page.
 *
 * Hero, the facts strip, the class itself and its enquiry panel. One block
 * rather than five, because every value comes from the class's own fields and
 * there is nothing per-instance for an editor to arrange; the template drops it
 * in and the content decides what appears.
 *
 * A plain dynamic block, not an ACF one: it carries no block data, and an ACF
 * block with none trips a deprecation inside ACF on PHP 8.1+.
 *
 * Anything the client has not confirmed renders its "ask us" state rather than a
 * guess. Times and fees are empty for all fifteen classes today.
 *
 * @package SJPTheatreArts
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sjpta_id = get_the_ID();

if ( ! $sjpta_id || SJPTA_CLASS_POST_TYPE !== get_post_type( $sjpta_id ) ) {
	return;
}

/**
 * Flatten a repeater to its values.
 *
 * @param mixed  $rows Repeater rows.
 * @param string $key  Sub-field name.
 *
 * @return array<int,string>
 */
$sjpta_values = static function ( $rows, string $key ): array {
	$out = array();

	foreach ( is_array( $rows ) ? $rows : array() as $row ) {
		$value = trim( (string) ( $row[ $key ] ?? '' ) );

		if ( '' !== $value ) {
			$out[] = $value;
		}
	}

	return $out;
};

$sjpta_blurb    = (string) sjpta_get_field( 'blurb', $sjpta_id );
$sjpta_tags     = $sjpta_values( sjpta_get_field( 'tags', $sjpta_id ), 'text' );
$sjpta_days     = $sjpta_values( sjpta_get_field( 'days', $sjpta_id ), 'text' );
$sjpta_times    = $sjpta_values( sjpta_get_field( 'times', $sjpta_id ), 'text' );
$sjpta_timenote = trim( (string) sjpta_get_field( 'times_note', $sjpta_id ) );
$sjpta_teachers = $sjpta_values( sjpta_get_field( 'teachers', $sjpta_id ), 'name' );
$sjpta_level    = trim( (string) sjpta_get_field( 'level', $sjpta_id ) );
$sjpta_fees     = trim( (string) sjpta_get_field( 'fees', $sjpta_id ) );
$sjpta_who      = (string) sjpta_get_field( 'who', $sjpta_id );
$sjpta_steps    = sjpta_get_field( 'steps', $sjpta_id );
$sjpta_lead     = (string) sjpta_get_field( 'lead', $sjpta_id );
$sjpta_leadtags = $sjpta_values( sjpta_get_field( 'lead_tags', $sjpta_id ), 'text' );
$sjpta_wear     = $sjpta_values( sjpta_get_field( 'wear_items', $sjpta_id ), 'text' );
$sjpta_wearnote = trim( (string) sjpta_get_field( 'wear_note', $sjpta_id ) );
$sjpta_extra    = trim( (string) sjpta_get_field( 'extra_text', $sjpta_id ) );
$sjpta_extrahd  = trim( (string) sjpta_get_field( 'extra_title', $sjpta_id ) );
$sjpta_gallery  = sjpta_get_field( 'gallery', $sjpta_id );
$sjpta_caption  = trim( (string) sjpta_get_field( 'caption', $sjpta_id ) );

$sjpta_classes_page = get_page_by_path( 'classes' );
$sjpta_classes_url  = $sjpta_classes_page ? (string) get_permalink( $sjpta_classes_page ) : home_url( '/classes/' );

$sjpta_timetable = get_page_by_path( 'timetable-and-fees' );

$sjpta_about = get_page_by_path( 'about' );

$sjpta_watch = sjpta_setting( 'watch_first', '' );
$sjpta_title = get_the_title( $sjpta_id );

/*
 * Enrolling happens on the Join page. "Enrol now" here goes there with the
 * class named in the URL, so the enrolment form opens already knowing which
 * class; the form on this page is for asking, and says so.
 */
$sjpta_enrol_url = add_query_arg( 'pf_class_want', $sjpta_title, sjpta_link_or_page( '', 'join' ) ) . '#enquire';

/**
 * A fact, the client's own wording for it, or its honest "ask us" state.
 *
 * @param array<int,string> $values   Values to join.
 * @param string            $fallback What to say when there are none.
 * @param string            $note     What the client wants said instead.
 *
 * @return void
 */
$sjpta_fact = static function ( array $values, string $fallback, string $note = '' ): void {
	if ( ! empty( $values ) ) {
		printf( '<span>%s</span>', esc_html( implode( ', ', $values ) ) );
		return;
	}

	/*
	 * A note the client has written is a decision, not an outstanding fact.
	 * "See timetable" is the answer, so it reads as ordinary text; the magenta
	 * "to confirm" state stays reserved for the ones nobody has filled in yet,
	 * or it stops meaning anything.
	 */
	if ( '' !== $note ) {
		printf( '<span>%s</span>', esc_html( $note ) );
		return;
	}

	printf( '<span class="sjpta-toconfirm">%s</span>', esc_html( $fallback ) );
};
?>
<section<?php echo sjpta_anchor_attr( $attributes ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sjpta_anchor_attr(). ?> class="sjpta-class alignfull">

	<div class="sjpta-class__hero sjpta-underlap">
		<span class="sjpta-class__decor" aria-hidden="true"></span>

		<div class="sjpta-inner sjpta-class__herogrid<?php echo has_post_thumbnail( $sjpta_id ) ? '' : ' is-solo'; ?>">
			<div class="sjpta-class__intro" data-reveal>
				<nav class="sjpta-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'sjptheatrearts' ); ?>">
					<a class="sjpta-crumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'sjptheatrearts' ); ?></a>
					<span class="sjpta-crumbs__sep" aria-hidden="true">&rsaquo;</span>
					<a class="sjpta-crumbs__link" href="<?php echo esc_url( $sjpta_classes_url ); ?>"><?php esc_html_e( 'Classes', 'sjptheatrearts' ); ?></a>
					<span class="sjpta-crumbs__sep" aria-hidden="true">&rsaquo;</span>
					<span class="sjpta-crumbs__current" aria-current="page"><?php echo esc_html( $sjpta_title ); ?></span>
				</nav>

				<?php if ( ! empty( $sjpta_tags ) ) : ?>
					<ul class="sjpta-class__pills">
						<?php foreach ( $sjpta_tags as $sjpta_i => $sjpta_tag ) : ?>
							<?php
							// The badge palette rotates, matching the class cards.
							$sjpta_tones = array( 'orange', 'magenta', 'green', 'purple' );
							?>
							<li class="sjpta-badge sjpta-badge--<?php echo esc_attr( $sjpta_tones[ $sjpta_i % 4 ] ); ?>"><?php echo esc_html( $sjpta_tag ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<h1 class="sjpta-class__title"><?php echo esc_html( $sjpta_title ); ?></h1>

				<?php if ( '' !== $sjpta_blurb ) : ?>
					<p class="sjpta-class__blurb"><?php echo esc_html( $sjpta_blurb ); ?></p>
				<?php endif; ?>

				<div class="sjpta-class__actions">
					<a class="sjpta-btn sjpta-btn--plum sjpta-class__btn" href="<?php echo esc_url( $sjpta_enrol_url ); ?>"><?php esc_html_e( 'Enrol now', 'sjptheatrearts' ); ?></a>

					<a class="sjpta-btn sjpta-btn--outline sjpta-class__btn" href="#enquire"><?php esc_html_e( 'Ask a question', 'sjptheatrearts' ); ?></a>

					<?php if ( $sjpta_timetable ) : ?>
						<a class="sjpta-btn sjpta-btn--outline sjpta-class__btn" href="<?php echo esc_url( (string) get_permalink( $sjpta_timetable ) ); ?>">
							<?php esc_html_e( 'See the timetable', 'sjptheatrearts' ); ?>
							<?php echo sjpta_icon( 'arrow-right', 15, '#381064' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( has_post_thumbnail( $sjpta_id ) ) : ?>
				<div class="sjpta-class__media">
					<div class="sjpta-class__photo">
						<?php
						echo get_the_post_thumbnail(
							$sjpta_id,
							'large',
							array(
								'sizes'         => '(max-width: 1023px) 92vw, 620px',
								'fetchpriority' => 'high',
							)
						);
						?>

						<?php if ( '' !== $sjpta_caption ) : ?>
							<span class="sjpta-class__caption"><?php echo esc_html( $sjpta_caption ); ?></span>
						<?php endif; ?>
					</div>

					<?php if ( is_array( $sjpta_gallery ) && ! empty( $sjpta_gallery ) ) : ?>
						<div class="sjpta-class__thumbs">
							<?php foreach ( $sjpta_gallery as $sjpta_row ) : ?>
								<?php
								$sjpta_photo = (int) ( $sjpta_row['photo'] ?? 0 );

								if ( ! $sjpta_photo ) {
									continue;
								}
								?>
								<span class="sjpta-class__thumb">
									<?php
									echo wp_get_attachment_image(
										$sjpta_photo,
										'sjpta-480',
										false,
										array(
											'loading' => 'lazy',
											'sizes'   => '200px',
										)
									);
									?>
								</span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="sjpta-inner">
		<dl class="sjpta-class__facts">
			<div class="sjpta-class__fact">
				<dt><?php esc_html_e( 'Days', 'sjptheatrearts' ); ?></dt>
				<dd><?php $sjpta_fact( $sjpta_days, __( 'Ask us for days', 'sjptheatrearts' ) ); ?></dd>
			</div>
			<div class="sjpta-class__fact">
				<dt><?php esc_html_e( 'Times', 'sjptheatrearts' ); ?></dt>
				<dd><?php $sjpta_fact( $sjpta_times, __( 'Ask us for times', 'sjptheatrearts' ), $sjpta_timenote ); ?></dd>
			</div>
			<div class="sjpta-class__fact">
				<dt><?php esc_html_e( 'Teachers', 'sjptheatrearts' ); ?></dt>
				<dd><?php $sjpta_fact( $sjpta_teachers, __( 'Ask us', 'sjptheatrearts' ) ); ?></dd>
			</div>
			<div class="sjpta-class__fact">
				<dt><?php esc_html_e( 'Level', 'sjptheatrearts' ); ?></dt>
				<dd><?php $sjpta_fact( '' === $sjpta_level ? array() : array( $sjpta_level ), __( 'All levels', 'sjptheatrearts' ) ); ?></dd>
			</div>
			<div class="sjpta-class__fact">
				<dt><?php esc_html_e( 'Fees', 'sjptheatrearts' ); ?></dt>
				<dd><?php $sjpta_fact( '' === $sjpta_fees ? array() : array( $sjpta_fees ), __( 'Ask us about fees', 'sjptheatrearts' ) ); ?></dd>
			</div>
		</dl>
	</div>

	<div class="sjpta-inner sjpta-class__body">
		<div class="sjpta-class__main">
			<?php if ( '' !== $sjpta_who ) : ?>
				<h2 class="sjpta-class__h2"><span class="sjpta-accent" aria-hidden="true">+</span> <?php esc_html_e( 'Who it is for', 'sjptheatrearts' ); ?></h2>
				<div class="sjpta-class__prose"><?php echo wp_kses_post( $sjpta_who ); ?></div>
			<?php endif; ?>

			<?php if ( is_array( $sjpta_steps ) && ! empty( $sjpta_steps ) ) : ?>
				<h2 class="sjpta-class__h2"><span class="sjpta-accent" aria-hidden="true">+</span> <?php esc_html_e( 'What happens in a class', 'sjptheatrearts' ); ?></h2>

				<ol class="sjpta-class__steps">
					<?php foreach ( $sjpta_steps as $sjpta_i => $sjpta_step ) : ?>
						<?php
						$sjpta_step_title = trim( (string) ( $sjpta_step['title'] ?? '' ) );

						if ( '' === $sjpta_step_title ) {
							continue;
						}
						?>
						<li class="sjpta-class__step">
							<span class="sjpta-class__stepnum" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $sjpta_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="sjpta-class__stepcopy">
								<span class="sjpta-class__steptitle"><?php echo esc_html( $sjpta_step_title ); ?></span>
								<?php if ( '' !== trim( (string) ( $sjpta_step['text'] ?? '' ) ) ) : ?>
									<span class="sjpta-class__steptext"><?php echo esc_html( (string) $sjpta_step['text'] ); ?></span>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_lead ) : ?>
				<h2 class="sjpta-class__h2"><span class="sjpta-accent" aria-hidden="true">+</span> <?php esc_html_e( 'Where it can lead', 'sjptheatrearts' ); ?></h2>
				<div class="sjpta-class__prose"><?php echo wp_kses_post( $sjpta_lead ); ?></div>

				<?php if ( ! empty( $sjpta_leadtags ) ) : ?>
					<ul class="sjpta-class__leadtags">
						<?php foreach ( $sjpta_leadtags as $sjpta_tag ) : ?>
							<li>
								<?php echo sjpta_icon( 'check', 13, 'var(--wp--custom--color--success-green)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
								<?php echo esc_html( $sjpta_tag ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<aside class="sjpta-class__aside">
			<?php if ( ! empty( $sjpta_wear ) ) : ?>
				<div class="sjpta-class__card sjpta-class__card--tint">
					<h2 class="sjpta-class__cardtitle"><?php esc_html_e( 'What to wear', 'sjptheatrearts' ); ?></h2>

					<ul class="sjpta-class__wear">
						<?php foreach ( $sjpta_wear as $sjpta_item ) : ?>
							<li><span aria-hidden="true">&middot;</span><?php echo esc_html( $sjpta_item ); ?></li>
						<?php endforeach; ?>
					</ul>

					<?php if ( '' !== $sjpta_wearnote ) : ?>
						<p class="sjpta-class__wearnote"><?php echo esc_html( $sjpta_wearnote ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $sjpta_teachers ) ) : ?>
				<div class="sjpta-class__card">
					<h2 class="sjpta-class__cardtitle"><?php echo esc_html( _n( 'Your teacher', 'Your teachers', count( $sjpta_teachers ), 'sjptheatrearts' ) ); ?></h2>

					<ul class="sjpta-class__teachers">
						<?php foreach ( $sjpta_teachers as $sjpta_name ) : ?>
							<li>
								<?php /* An initial, not a photograph: there are no teacher portraits in the class data, and a stock face would be worse than a letter. */ ?>
								<span class="sjpta-class__initial" aria-hidden="true"><?php echo esc_html( mb_substr( $sjpta_name, 0, 1 ) ); ?></span>
								<span class="sjpta-class__teachername"><?php echo esc_html( $sjpta_name ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>

					<?php if ( $sjpta_about ) : ?>
						<a class="sjpta-class__more" href="<?php echo esc_url( (string) get_permalink( $sjpta_about ) . '#teachers' ); ?>">
							<?php esc_html_e( 'Meet the whole team', 'sjptheatrearts' ); ?>
							<?php echo sjpta_icon( 'arrow-right', 14, 'currentColor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_extra ) : ?>
				<div class="sjpta-class__card">
					<h2 class="sjpta-class__cardtitle"><?php echo esc_html( '' !== $sjpta_extrahd ? $sjpta_extrahd : __( 'Good to know', 'sjptheatrearts' ) ); ?></h2>
					<div class="sjpta-class__extra"><?php echo wp_kses_post( $sjpta_extra ); ?></div>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $sjpta_watch ) : ?>
				<div class="sjpta-class__card sjpta-class__card--dark">
					<span class="sjpta-class__cardglow" aria-hidden="true"></span>
					<h2 class="sjpta-class__cardtitle"><?php esc_html_e( 'Want to watch first?', 'sjptheatrearts' ); ?></h2>
					<p class="sjpta-class__watchtext"><?php echo esc_html( $sjpta_watch ); ?></p>
					<a class="sjpta-btn sjpta-btn--primary sjpta-class__watchcta" href="#enquire"><?php esc_html_e( 'Ask about this class', 'sjptheatrearts' ); ?></a>
				</div>
			<?php endif; ?>
		</aside>
	</div>

	<div class="sjpta-inner" id="enquire">
		<div class="sjpta-class__enquire">
			<span class="sjpta-class__enquiredecor" aria-hidden="true"></span>

			<div class="sjpta-class__enquirecopy">
				<span class="sjpta-badge sjpta-badge--orange sjpta-class__eyebrow"><?php esc_html_e( 'Enquire now', 'sjptheatrearts' ); ?></span>

				<h2 class="sjpta-class__enquiretitle">
					<?php
					printf(
						/* translators: %s: class name. */
						esc_html__( 'Ask about %s', 'sjptheatrearts' ),
						esc_html( $sjpta_title )
					);
					?>
				</h2>

				<p class="sjpta-class__enquiretext">
					<?php esc_html_e( 'Your enquiry comes straight to us, and we will come back with class times, what to bring and how to enrol.', 'sjptheatrearts' ); ?>
				</p>

				<?php
				/*
				 * The design's reassurance note. It says how long enrolling takes,
				 * which is a claim about our own process rather than a fact about
				 * the client's fees or timetable, so it is stated rather than left
				 * as a placeholder. The design names a teacher here; who answers an
				 * enquiry is not something the site knows, so the sentence says
				 * what is true instead.
				 */
				?>
				<p class="sjpta-class__enquirenote">
					<span class="sjpta-class__enquiretick" aria-hidden="true">
						<?php echo sjpta_icon( 'check', 16, 'var(--wp--custom--color--success-green)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup from sjpta_icon(). ?>
					</span>
					<?php esc_html_e( 'Already decided? Enrolling takes a few minutes and we will confirm your start date.', 'sjptheatrearts' ); ?>
				</p>
			</div>

			<div class="sjpta-class__enquireform">
				<?php
				/*
				 * The site-wide enquiry form in its class-page shape: this page
				 * already knows the class, so the form states it and links back to
				 * the list rather than asking a question the heading just answered.
				 */
				sjpta_enquiry_form(
					array(
						'variant' => 'class',
						'type'    => 'enquiry',
						'subject' => sprintf(
							/* translators: %s: class name. */
							__( 'Enquiry about %s', 'sjptheatrearts' ),
							$sjpta_title
						),
						'chosen'  => array(
							'value' => $sjpta_title,
							'url'   => $sjpta_classes_url,
							'link'  => __( 'Change', 'sjptheatrearts' ),
						),
						'submit'  => __( 'Send my enquiry', 'sjptheatrearts' ),
						'anchor'  => 'enquire',
						'after'   => array(
							'text'    => __( 'Ready to enrol?', 'sjptheatrearts' ),
							'label'   => __( 'Enrol now', 'sjptheatrearts' ),
							'url'     => $sjpta_enrol_url,
							'prefill' => true,
						),
					)
				);
				?>
			</div>
		</div>
	</div>
</section>
