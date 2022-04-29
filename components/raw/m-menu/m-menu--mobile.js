import { MMenuBase, OPEN_STATE } from './m-menu--base';

const NAV_WRAPPER_SELECTOR = '.m-menu__nav';
const MAIN_MENU_SELECTOR = '.m-menu__nav-list';
const RIGHT_SELECTOR = '.m-menu__right';
const HAMBURGER_SELECTOR = '.a-hamburger';
const SUBMENU_ITEMS_SELECTOR = '.m-menu__nav-item';

const HIDDEN_STATE = 'is-hidden';
const ACTIVE_STATE = 'is-active';

export default class MMenuMobile extends MMenuBase {
	initialize() {
		super.initialize();

		this.mobileOpen = false;

		this.navWrapper = this.getScopedElement( NAV_WRAPPER_SELECTOR );
		this.mainNav = this.getScopedElement( MAIN_MENU_SELECTOR );
		this.hamburger = this.getScopedElement( HAMBURGER_SELECTOR );
		this.right = this.getScopedElement( RIGHT_SELECTOR );
		this.menuItems = this.getScopedElements( SUBMENU_ITEMS_SELECTOR );
	}

	bind() {
		super.bind();

		this.on( 'click', HAMBURGER_SELECTOR, ( event ) => this.toggleMainNavigation( event ) );
	}

	toggleMainNavigation( event ) {
		event.preventDefault();

		if (this.mobileOpen) {
			this.closeMainNavigation();
		} else {
			this.openMainNavigation();
		}
	}

	openMainNavigation() {
		this.mobileOpen = true;

		this.addClass( this.navWrapper, OPEN_STATE );
		this.addClass( this.hamburger, OPEN_STATE );
		this.addClass( this.mainNav, OPEN_STATE );
		this.addClass( this.right, OPEN_STATE );

		this.hamburger.setAttribute( 'aria-expanded', true );
	}

	closeMainNavigation() {
		this.mobileOpen = false;

		this.removeClass( this.navWrapper, OPEN_STATE );
		this.removeClass( this.hamburger, OPEN_STATE );
		this.removeClass( this.mainNav, OPEN_STATE );
		this.removeClass( this.right, OPEN_STATE );

		this.hamburger.setAttribute( 'aria-expanded', false );

		this.closeSubNavigation();
	}

	afterOpenSubNavigation( element ) {
		const isRight = element.closest( RIGHT_SELECTOR );
		if (isRight) {
			return;
		}

		this.removeClass( this.right, OPEN_STATE );

		const liClicked = element.parentElement;

		this.menuItems.forEach( item => {
			if (item === liClicked) {
				this.addClass( item, ACTIVE_STATE );
			}
			else {
				this.addClass( item, HIDDEN_STATE );
			}
		} );
	}

	afterCloseSubNavigation() {
		if (this.mobileOpen) {
			this.addClass( this.right, OPEN_STATE );
		}

		this.menuItems.forEach( item => {
			this.removeClass( item, HIDDEN_STATE );
			this.removeClass( item, ACTIVE_STATE );
		} );
	}

	closeNavigation() {
		this.closeMainNavigation();
	}
}
