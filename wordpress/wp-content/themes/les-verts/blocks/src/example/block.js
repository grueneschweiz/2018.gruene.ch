import { PREFIX } from '../shared';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText } = wp.editor;

registerBlockType( PREFIX + '/example', {
	title: __( 'Example' ),
	icon: 'megaphone',
	category: 'theme',
	supports: {
		html: false,
	},
	attributes: {
		content: {
			type: 'array',
			source: 'children',
			selector: 'p',
		},
	},

	edit( props ) {
		const content = props.attributes.content;

		const onChangeContent = newContent => {
			props.setAttributes( { content: newContent } );
		};

		return (
			<RichText
				className={ props.className }
				tagName="p"
				placeholder={ __( 'some text' ) }
				value={ content }
				onChange={ onChangeContent }
			/>
		);
	},

	save( props ) {
		return (
			<p className={ props.className }>{ props.attributes.content }</p>
		);
	},

} );
