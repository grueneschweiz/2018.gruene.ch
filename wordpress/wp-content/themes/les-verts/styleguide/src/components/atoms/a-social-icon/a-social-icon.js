import BaseView from 'base-view';

const MESSAGE_SELECTOR = '.a-social-icon__copied';
const MESSAGE_VISIBLE_CLASS = 'a-social-icon__copied--visible';

export default class MShareBlock extends BaseView {
	bind() {
		super.bind();

		if (this.isLinkButton()) {
			this.on( 'click', () => this.copyLink() );
		}
	}

	isLinkButton() {
		return 'link' in this.element.dataset;
	}

	copyLink() {
		navigator.clipboard.writeText( this.element.dataset.link ).
			then( this.showMessage.bind( this ) );
	}

	showMessage() {
		const message = this.getScopedElement( MESSAGE_SELECTOR );
		this.addClass( message, MESSAGE_VISIBLE_CLASS );
		window.setTimeout( () => {
			this.removeClass( message, MESSAGE_VISIBLE_CLASS );
		}, 100 );
	}
}
