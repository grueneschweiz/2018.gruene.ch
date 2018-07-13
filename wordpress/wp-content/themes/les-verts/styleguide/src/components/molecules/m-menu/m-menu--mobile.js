import {MMenuBase, OPEN_STATE} from "./m-menu--base";

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
    }

    bind() {
        super.bind();

        this.on("click", HAMBURGER_SELECTOR, () => this.toggleMainNavigation());
    }

    toggleMainNavigation() {
        event.preventDefault();

        if (this.mobileOpen) {
            this.closeMainNavigation();
        } else {
            this.openMainNavigation();
        }
    }

    openMainNavigation() {
        this.mobileOpen = true;

        let mainNav = this.getScopedElement(MAIN_MENU_SELECTOR);
        let hamburger = this.getScopedElement(HAMBURGER_SELECTOR);
        let right = this.getScopedElement(RIGHT_SELECTOR);

        this.addClass(hamburger, OPEN_STATE);
        this.addClass(mainNav, OPEN_STATE);
        this.addClass(right, OPEN_STATE);

        hamburger.setAttribute('aria-expanded', true);
    }

    closeMainNavigation() {
        this.mobileOpen = false;

        let mainNav = this.getScopedElement(MAIN_MENU_SELECTOR);
        let hamburger = this.getScopedElement(HAMBURGER_SELECTOR);
        let right = this.getScopedElement(RIGHT_SELECTOR);

        this.removeClass(hamburger, OPEN_STATE);
        this.removeClass(mainNav, OPEN_STATE);
        this.removeClass(right, OPEN_STATE);

        hamburger.setAttribute('aria-expanded', false);

        this.closeSubNavigation();
    }

    afterOpenSubNavigation(element) {
        let right = this.getScopedElement(RIGHT_SELECTOR);
        this.removeClass(right, OPEN_STATE);

        let menuItems = this.getScopedElements(SUBMENU_ITEMS_SELECTOR);
        let liClicked = element.parentElement;

        menuItems.forEach(item => {
            if (item === liClicked) {
                this.addClass(item, ACTIVE_STATE);
            } else {
                this.addClass(item, HIDDEN_STATE);
            }
        });
    }

    afterCloseSubNavigation() {
        if (this.mobileOpen) {
            let right = this.getScopedElement(RIGHT_SELECTOR);
            this.addClass(right, OPEN_STATE);
        }

        let menuItems = this.getScopedElements(SUBMENU_ITEMS_SELECTOR);

        menuItems.forEach(item => {
            this.removeClass(item, HIDDEN_STATE);
            this.removeClass(item, ACTIVE_STATE);
        });
    }

    closeNavigation() {
        this.closeMainNavigation();
    }
}
