import BaseView from './../../../js/base-view';

const MOBILE_SELECTOR = '.m-share__mobile';
const CLOSE_SELECTOR = '.m-share__close';
const BUTTONS_SELECTOR = '.m-share__buttons';

const OPEN_STATE = 'is-open';

export default class MShare extends BaseView {
	initialize() {
		this.buttons = this.getScopedElement( BUTTONS_SELECTOR );
	}

	bind() {
		super.bind();

		this.on( 'click', MOBILE_SELECTOR, () => {
			this.addClass( this.buttons, OPEN_STATE );
			this.addClass( OPEN_STATE );
		} );

		this.on( 'click', CLOSE_SELECTOR, () => {
			this.removeClass( this.buttons, OPEN_STATE );
			this.removeClass( OPEN_STATE );
		} );
	}
}
