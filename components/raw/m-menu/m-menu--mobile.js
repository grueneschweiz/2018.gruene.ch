import { MAIN_MENU_SELECTOR, MMenuBase, OPEN_STATE } from './m-menu--base';

const NAV_WRAPPER_SELECTOR = '.m-menu__nav';
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

		this.onResize = () => this.updateNavPosition();

		this.setOrientation( 'vertical' );
	}

	bind() {
		super.bind();

		this.on( 'click', HAMBURGER_SELECTOR,
			( event ) => this.toggleMainNavigation( event ) );
	}

	toggleMainNavigation( event ) {
		event.preventDefault();

		if (this.mobileOpen) {
			this.closeMainNavigation();
		}
		else {
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

		this.updateNavPosition();
		window.addEventListener( 'resize', this.onResize );
	}

	closeMainNavigation() {
		this.mobileOpen = false;
		const toClose = [
			this.navWrapper,
			this.hamburger,
			this.mainNav,
			this.right,
		];

		for (const el of toClose) {
			if (el) {
				this.removeClass( el, OPEN_STATE );
			}
		}

		if (this.hamburger) {
			this.hamburger.setAttribute( 'aria-expanded', false );
		}

		window.removeEventListener( 'resize', this.onResize );
		this.resetNavPosition();

		this.closeSubNavigation();
	}

	/**
	 * The CTA buttons / language switcher (`.m-menu__right`) can wrap onto several
	 * lines on mobile depending on their content, which changes their rendered height.
	 * The main navigation is positioned right below that block, so its offset needs to
	 * be recalculated whenever the menu opens (or the viewport is resized) to prevent
	 * the navigation items from being hidden behind (or overlapping) the CTA buttons.
	 */
	updateNavPosition() {
		if (!this.navWrapper || !this.right) {
			return;
		}

		if (this.isDesktop()) {
			this.resetNavPosition();
			return;
		}

		const headerRect = this.element.getBoundingClientRect();
		const rightRect = this.right.getBoundingClientRect();

		this.navWrapper.style.top = `${ rightRect.bottom - headerRect.top }px`;
	}

	resetNavPosition() {
		if (this.navWrapper) {
			this.navWrapper.style.top = '';
		}
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
