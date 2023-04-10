import BaseView from 'base-view';

const LINK_BUTTON_SELECTOR = '.a-link-button';
const TARGET_ID = 'main-content';

export default class MMenu extends BaseView {
	initialize() {
		const link = this.getScopedElement( LINK_BUTTON_SELECTOR );
		this.clickHandler = ( e ) => {
			const target = document.getElementById( TARGET_ID );
			if (target) {
				target.focus();
				e.preventDefault();
			}
		};

		if (link) {
			link.addEventListener( 'click', this.clickHandler.bind( this ) );
		}
	}

	destroy() {
		super.destroy();

		window.removeEventListener( 'click', this.clickHandler );
	}
}
