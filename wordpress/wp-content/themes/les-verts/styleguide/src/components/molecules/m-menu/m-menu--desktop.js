import {
	MAIN_MENU_SELECTOR,
	MMenuBase,
	OPEN_STATE,
	SUBMENU_SELECTOR,
} from './m-menu--base';

const CLOSE_SELECTOR = '.m-menu__submenu__close';

const UP = 1;
const DOWN = 2;

export default class MMenuDesktop extends MMenuBase {
	initialize() {
		super.initialize();

		this.ticking = false;

		this.getScopedElement( MAIN_MENU_SELECTOR ).
			setAttribute( 'aria-orientation', 'horizontal' );
	}

	bind() {
		super.bind();

		this.on('click', CLOSE_SELECTOR, () => this.closeSubNavigation());
	}

	afterOpenSubNavigation() {
		this.clickedItem = this.currentSub;

		this.submenu = this.getScopedElement(SUBMENU_SELECTOR + '.' + OPEN_STATE);

		if (this.submenu.offsetHeight !== this.submenu.scrollHeight) {
			this.setInitialScrollPos();
			this.scrollHandler = (event) => this.requestTick(event);
			window.addEventListener('scroll', this.scrollHandler, false);
		}
	}

	afterCloseSubNavigation() {
		if (this.submenu) {
			window.removeEventListener('scroll', this.scrollHandler);
		}
		if (this.clickedItem === document.activeElement) {
			document.activeElement.blur();
			this.clickedItem = null;
		}
	}

	setInitialScrollPos() {
		const scrollTop = this.scrollTop();
		const submenuHeight = this.submenu.scrollHeight;

		const body = document.body;
		const html = document.documentElement;
		const documentHeight = Math.max(body.scrollHeight, body.offsetHeight,
			html.clientHeight, html.scrollHeight, html.offsetHeight);

		if (scrollTop + submenuHeight > documentHeight) {
			window.scrollTo(0, scrollTop - submenuHeight);
		}

		this.initialScrollPos = this.lastPos = this.scrollTop();
	}

	requestTick() {
		if (!this.ticking && this.submenu) {
			this.ticking = true;
			window.requestAnimationFrame(this.scrollSubNavigation.bind(this));
		}
	}

	scrollTop() {
		// browser incompatibilities are so awful
		return window.scrollY
			|| window.pageYOffset
			|| document.body.scrollTop + (document.documentElement && document.documentElement.scrollTop || 0);
	}

	scrollSubNavigation() {
		const scrollTop = this.scrollTop();
		let direction = DOWN;

		if (this.lastPos > scrollTop) {
			direction = UP;
		}

		if (this.lastDirection !== direction) {
			if (UP === direction) {
				this.initialScrollPos = scrollTop - this.submenu.scrollTop;
			} else {
				this.initialScrollPos = scrollTop;
			}
		}

		this.submenu.scrollTop = scrollTop - this.initialScrollPos;

		this.lastPos = scrollTop;
		this.lastDirection = direction;

		this.ticking = false;
	}

	destroy() {
		super.destroy();

		window.removeEventListener('scroll', this.scrollHandler);
	}
}
