import BaseView from 'base-view';

export const MAIN_MENU_SELECTOR = '.m-menu__nav-list';
export const MAIN_ENTRY_SELECTOR = '.m-menu__nav-link--js-hook';
export const SUBMENU_SELECTOR = '.m-menu__submenu';

export const OPEN_STATE = 'is-open';

export class MMenuBase extends BaseView {
	initialize() {
		super.initialize();

		this.currentSub = null;
	}

	bind() {
		super.bind();

		this.on( 'click', MAIN_ENTRY_SELECTOR,
			( event ) => this.toggleSubNavigation( event ) );
	}

	setOrientation( orientation ) {
		const main = this.getScopedElement( MAIN_MENU_SELECTOR );

		if (main) {
			main.setAttribute( 'aria-orientation', orientation );
		}
	}

	toggleSubNavigation( event ) {
		if (this.currentSub !== event.target) {
			this.closeSubNavigation();
			this.openSubNavigation( event );
		}
		else {
			event.preventDefault();
			this.closeSubNavigation();
		}
	}

	openSubNavigation( event ) {
		let element = event.target;
		let submenu = element.parentElement.querySelector( SUBMENU_SELECTOR );

		if (null === submenu) {
			// if there is no submenu, handle it as regular link
			return;
		}

		event.preventDefault();

		this.currentSub = element;

		this.addClass( submenu, OPEN_STATE );
		this.addClass( element, OPEN_STATE );
		element.setAttribute( 'aria-expanded', true );

		this.afterOpenSubNavigation( element );
	}

	closeSubNavigation() {
		this.currentSub = null;

		this.getScopedElements( MAIN_ENTRY_SELECTOR ).forEach( ( element ) => {
			this.removeClass( element, OPEN_STATE );
			element.setAttribute( 'aria-expanded', false );
		} );

		this.getScopedElements( SUBMENU_SELECTOR ).forEach( ( element ) => {
			this.removeClass( element, OPEN_STATE );
		} );

		this.afterCloseSubNavigation();
	}

	closeNavigation() {
		this.closeSubNavigation();
	}

	/**
	 * overwrite those functions to do some more stuff on opening
	 *
	 * @param {EventTarget} element
	 */
	afterOpenSubNavigation() {
	}

	/**
	 * overwrite those functions to do some more stuff on closing
	 */
	afterCloseSubNavigation() {
	}
}
