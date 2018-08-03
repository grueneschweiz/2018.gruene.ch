import { PREFIX } from '../shared';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText, MediaUpload, MediaPlaceholder } = wp.editor;
const { Button } = wp.components;

registerBlockType( PREFIX + '/campaign', {
	title: __( 'Campaign' ),
	icon: 'megaphone',
	category: 'theme',
	supports: {
		html: false,
	},
	attributes: {
		imageId: {
			type: 'number',
		},
		imageURL: {
			type: 'string',
			source: 'attribute',
			selector: 'img',
			attribute: 'src',
		},
		content: {
			type: 'array',
			source: 'children',
			selector: 'p',
		},
	},

	edit( props ) {
		const {
			attributes: {
				imageURL,
				imageId,
			},
		} = props;

		const onSelectImage = media => {
			props.setAttributes( {
				imageURL: media.sizes['full-grid'].url,
				imageId: media.id,
			} );
		};

		const imageButtonClass = () => {
			return imageId ? 'image-button' : 'button button-large';
		};

		const image = () => {
			if (imageId) {
				return (
					<img src={ imageURL }
						 alt="Some many things on this image"
						 className="a-campaign-image__image"/>
				);
			} else {
				return (
					<MediaPlaceholder
						icon="format-image"
						labels={ {
							title: __( 'Image' ),
							name: __( 'an image' ),
						} }
						className="a-campaign-image__image"
						onSelect={ onSelectImage }
						//notices={ noticeUI }
						//onError={ noticeOperations.createErrorNotice }
						accept="image/*"
						type="image"
					/>
				);
			}
		};

		const mediaUpload = () => {
			return (
				<MediaUpload
					onSelect={ onSelectImage }
					type="image"
					value={ imageId }
					render={ ( { open } ) => (
						<Button className={ imageButtonClass() } onClick={ open }>
							{ image() }
						</Button>
					) }
				/>
			);
		};

		return (
			<article className="o-campaign">
				<div className="o-campaign__image-wrapper">
					<div className="o-campaign__image">
						<figure className="a-campaign-image">
							{ mediaUpload() }
							<figcaption>
								<small className="a-campaign-image__copy">&copy; Photographer xy</small>
							</figcaption>
						</figure>
					</div>
				</div>
			</article>
		);
	},

	save( props ) {
		const {
			attributes: {
				imageURL,
			},
		} = props;
		return (
			<article className="o-campaign">
				<div className="o-campaign__image-wrapper">
					<div className="o-campaign__image">
						<figure className="a-campaign-image">
							<img src={ imageURL }
								 alt="Some many things on this image"
								 className="a-campaign-image__image"/>
							<figcaption>
								<small className="a-campaign-image__copy">&copy; Photographer xy</small>
							</figcaption>
						</figure>
					</div>
				</div>
			</article>
		);
	},

} );
