import BaseView from 'base-view';

const FIRST_LINK_SELECTOR = 'div > span > span > a';
const MENU_SELECTOR = '.o-header__display';
const ITEM_SELECTOR = '.m-menu__nav-link--';

const PARENT_CLASS = 'm-menu__nav-link--parent';

const HASH_PREFIX = '#menu-item-';

export default class ABreadcrumbs extends BaseView {
	bind() {
		super.bind();

		this.on('click', FIRST_LINK_SELECTOR, (event) => this.handleClick(event));
	}

	handleClick(event) {
		const hash = event.delegateTarget.hash;
		const id = hash.replace(HASH_PREFIX, '');

		const menu = document.querySelector(MENU_SELECTOR);
		const item = menu.querySelector(ITEM_SELECTOR + id);

		if (item && item.classList.contains(PARENT_CLASS)) {
			item.click();
			event.preventDefault();
		}
	}
}
