import BaseView from './../../../js/base-view';

const MOBILE_SELECTOR = '.m-share__mobile';
const LABEL_SELECTOR = '.m-share__desc';
const CLOSE_SELECTOR = '.m-share__close';
const BUTTONS_SELECTOR = '.m-share__buttons';

const OPEN_STATE = 'is-open';
const HAS_SHARE_BUTTONS = 'has-share-buttons';

export default class MShare extends BaseView {
	initialize() {
		this.buttons = this.getScopedElement( BUTTONS_SELECTOR );

		this.addPageBottomMargin();
	}

	bind() {
		super.bind();

		this.on( 'click', MOBILE_SELECTOR, () => this.show());
		this.on('click', LABEL_SELECTOR, () => this.show());
		this.on( 'click', CLOSE_SELECTOR, () => this.hide());
	}

	addPageBottomMargin() {
		this.addClass( document.body, HAS_SHARE_BUTTONS );
	}

	show() {
		this.addClass( this.buttons, OPEN_STATE );
		this.addClass( OPEN_STATE );
	}

	hide() {
		this.removeClass( this.buttons, OPEN_STATE );
		this.removeClass( OPEN_STATE );
	}
}
