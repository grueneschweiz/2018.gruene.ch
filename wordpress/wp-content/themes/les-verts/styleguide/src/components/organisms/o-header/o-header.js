import BaseView from 'base-view';

const BRANDING_SELECTOR = '.o-header__branding';
const MENU_SELECTOR = '.o-header__menu';
const VISIBLE_MENU_SELECTOR = '.o-header__display';

export default class OHeader extends BaseView {
	initialize() {
		this.branding = this.getScopedElement( BRANDING_SELECTOR );
		this.menu = this.getScopedElement( MENU_SELECTOR );
		this.visibleMenu = this.getScopedElement( VISIBLE_MENU_SELECTOR );

		this.ticking = false;
		this.isFixed = false;

		this.setMenuBarPosition();
	}

	bind() {
		super.bind();

		this.scrollHandler = () => this.requestTick();
		window.addEventListener( 'scroll', this.scrollHandler, false );
	}

	requestTick() {
		if (! this.ticking) {
			this.ticking = true;
			window.requestAnimationFrame( this.setMenuBarPosition.bind( this ) );
		}
	}

	setMenuBarPosition() {
		let scrollTop = this.getScrollTop();

		if (! this.isFixed && scrollTop >= this.branding.clientHeight) {
			this.isFixed = true;

			this.visibleMenu.style.transform = this.translate( - this.branding.clientHeight );
		}

		if (scrollTop < this.branding.clientHeight) {
			this.visibleMenu.style.transform = this.translate( - scrollTop );
			this.isFixed = false;
		}

		this.ticking = false;
	}

	translate( y ) {
		return `translate3d(0px, ${y}px, 0px)`;
	}

	getFixed() {
		return this.isFixed;
	}

	getMenu() {
		return this.menu;
	}

	getBranding() {
		return this.branding;
	}

	destroy() {
		super.destroy();

		window.removeEventListener( 'scroll', this.scrollHandler );
	}
}
