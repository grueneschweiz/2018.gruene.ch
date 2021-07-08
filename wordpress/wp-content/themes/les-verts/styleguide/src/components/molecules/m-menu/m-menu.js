import BaseView from 'base-view';
import MMenuDesktop from './m-menu--desktop';
import MMenuMobile from './m-menu--mobile';

const FORCE_MOBILE_CLASS = 'm-menu--force-mobile';
const HAMBURGER_SELECTOR = '.a-hamburger';

export default class MMenu extends BaseView {
	initialize() {
		this.forceMobile = this.element.classList.contains( FORCE_MOBILE_CLASS );

		this.setMenuHandler();
	}

	bind() {
		super.bind();

		this.setMenuHandler = this.setMenuHandler.bind( this );
		window.addEventListener( 'resize', this.setMenuHandler );

		this.on( 'click', HAMBURGER_SELECTOR, () => this.removeForceMobile() );
	}

	removeForceMobile() {
		if (this.forceMobile) {
			this.element.classList.remove( FORCE_MOBILE_CLASS );
			this.forceMobile = false;
		}
	}

	setMenuHandler() {
		let old = this.handler;
		let old_desktop = this.desktop;
		this.desktop = this.isDesktop();

		if (old_desktop !== this.desktop) {
			if (this.desktop) {
				this.handler = new MMenuDesktop( this.element );
			}
			else {
				this.handler = new MMenuMobile( this.element );
			}

			if (undefined !== old) {
				old.closeNavigation();
				old.destroy();
			}

			this.handler.initialize();
			this.handler.bind();
		}
	}

	destroy() {
		super.destroy();

		window.removeEventListener( 'resize', this.setMenuHandler );
	}
}
