/*
 * Client-side registration for the theme's plain dynamic blocks.
 *
 * Registering a block in PHP makes it render on the front end, but it does not
 * put it in the editor's own registry. Any such block sitting in *page content*
 * therefore shows "Your site doesn't include support for this block" and cannot
 * be edited at all. Most of this theme's dynamic blocks live in templates or
 * template parts, where that never comes up; the closing CTA band is seeded into
 * every page's content, so it was broken on all of them.
 *
 * The ACF blocks are unaffected: ACF registers those in the editor itself.
 *
 * Deliberately not ServerSideRender. That fires a REST request per block per
 * keystroke, and the band is a fixed panel whose copy is all in attributes, so a
 * plain preview drawn from those attributes is both faster and honest about
 * what is editable.
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	'use strict';

	if ( ! blocks || ! blocks.registerBlockType ) {
		return;
	}

	var el = element.createElement;
	var __ = i18n.__;
	var useBlockProps = blockEditor.useBlockProps;

	blocks.registerBlockType( 'sjptheatrearts/cta-band', {
		apiVersion: 3,
		title: __( 'SJP closing CTA band', 'sjptheatrearts' ),
		icon: 'megaphone',
		category: 'theme',
		description: __(
			'The purple call-to-action panel that closes a page.',
			'sjptheatrearts'
		),
		supports: {
			html: false,
			reusable: false,
			align: false,
			customClassName: false,
		},
		attributes: {
			heading: { type: 'string', default: 'Ready when you are' },
			text: {
				type: 'string',
				default:
					'Enrol now and we will confirm your class time, teacher and what to bring.',
			},
			primaryLabel: { type: 'string', default: 'Enrol now' },
			primaryUrl: { type: 'string', default: '' },
			secondaryLabel: { type: 'string', default: 'Timetable & fees' },
			secondaryUrl: { type: 'string', default: '' },
			roomy: { type: 'boolean', default: false },
			suppressed: { type: 'boolean', default: false },
		},

		edit: function ( props ) {
			var a = props.attributes;
			var set = props.setAttributes;

			return el(
				element.Fragment,
				null,

				el(
					blockEditor.InspectorControls,
					null,
					el(
						components.PanelBody,
						{ title: __( 'Buttons', 'sjptheatrearts' ) },
						el( components.TextControl, {
							label: __( 'First button label', 'sjptheatrearts' ),
							value: a.primaryLabel,
							onChange: function ( v ) {
								set( { primaryLabel: v } );
							},
						} ),
						el( components.TextControl, {
							label: __( 'First button link', 'sjptheatrearts' ),
							help: __( 'Leave empty to use the Join page.', 'sjptheatrearts' ),
							value: a.primaryUrl,
							onChange: function ( v ) {
								set( { primaryUrl: v } );
							},
						} ),
						el( components.TextControl, {
							label: __( 'Second button label', 'sjptheatrearts' ),
							value: a.secondaryLabel,
							onChange: function ( v ) {
								set( { secondaryLabel: v } );
							},
						} ),
						el( components.TextControl, {
							label: __( 'Second button link', 'sjptheatrearts' ),
							help: __(
								'Leave empty to use the Timetable & fees page.',
								'sjptheatrearts'
							),
							value: a.secondaryUrl,
							onChange: function ( v ) {
								set( { secondaryUrl: v } );
							},
						} ),
						el( components.ToggleControl, {
							label: __( 'Extra tall', 'sjptheatrearts' ),
							checked: !! a.roomy,
							onChange: function ( v ) {
								set( { roomy: v } );
							},
						} )
					)
				),

				el(
					'div',
					useBlockProps( { className: 'sjpta-ctaedit' } ),
					el( blockEditor.RichText, {
						tagName: 'h2',
						className: 'sjpta-ctaedit__heading',
						value: a.heading,
						allowedFormats: [],
						placeholder: __( 'Heading', 'sjptheatrearts' ),
						onChange: function ( v ) {
							set( { heading: v } );
						},
					} ),
					el( blockEditor.RichText, {
						tagName: 'p',
						className: 'sjpta-ctaedit__text',
						value: a.text,
						allowedFormats: [],
						placeholder: __( 'Supporting line', 'sjptheatrearts' ),
						onChange: function ( v ) {
							set( { text: v } );
						},
					} ),
					el(
						'p',
						{ className: 'sjpta-ctaedit__buttons' },
						el( 'span', null, a.primaryLabel || __( 'First button', 'sjptheatrearts' ) ),
						el(
							'span',
							null,
							a.secondaryLabel || __( 'Second button', 'sjptheatrearts' )
						)
					)
				)
			);
		},

		// Dynamic: the front end renders from PHP, so nothing is saved as markup.
		save: function () {
			return null;
		},
	} );

	/*
	 * The class list's filter bar. No settings of its own: it reads the classes
	 * and the age routes at render time, so the editor only needs to know the
	 * block exists and to say what it is.
	 */
	blocks.registerBlockType( 'sjptheatrearts/class-filters', {
		apiVersion: 2,
		title: __( 'SJP class filters', 'sjptheatrearts' ),
		icon: 'filter',
		category: 'theme',
		description: __(
			'The filter bar for the class list. Works without JavaScript; filters in place with it.',
			'sjptheatrearts'
		),
		supports: {
			align: false,
			anchor: true,
			html: false,
			customClassName: false,
		},

		edit: function () {
			return el(
				'div',
				useBlockProps( { className: 'sjpta-blockedit' } ),
				el(
					'strong',
					null,
					__( 'Class filters', 'sjptheatrearts' )
				),
				el(
					'span',
					null,
					__(
						'The age and style buttons above the class list. Built from the classes themselves, so there is nothing to set here.',
						'sjptheatrearts'
					)
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n
);
