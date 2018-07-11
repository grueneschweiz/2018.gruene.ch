import BaseView from "base-view";

const MAIN_MENU_SELECTOR = '.m-menu__nav';
const MAIN_ENTRY_SELECTOR = '.m-menu__nav-link';
const SUBMENU_SELECTOR = '.m-menu__submenu';

const RIGHT_SELECTOR = '.m-menu__right';

const CLOSE_SELECTOR = '.m-menu__submenu__close';
const HAMBURGER_SELECTOR = '.a-hamburger';

const OPEN_STATE = 'is-open';

export default class MMenu extends BaseView {

    initialize() {
        this.currentSub = null;
        this.mobileOpen = false;
    }

    bind() {
        this.toggleSubNavigationHandler = this.toggleSubNavigation.bind(this);
        this.toggleMainNavigationHandler = this.toggleMainNavigation.bind(this);
        this.closeNavigationHandler = this.closeSubNavigation.bind(this);

        this.on("click", MAIN_ENTRY_SELECTOR, this.toggleSubNavigationHandler);
        this.on("click", CLOSE_SELECTOR, this.closeNavigationHandler);
        this.on("click", HAMBURGER_SELECTOR, this.toggleMainNavigationHandler);
    }

    toggleSubNavigation(event) {
        event.preventDefault();

        if (this.currentSub !== event.target) {
            this.openSubNavigation(event.target);
        }
        else {
            this.closeSubNavigation();
        }
    }

    toggleMainNavigation() {
        event.preventDefault();

        if (this.mobileOpen) {
            this.closeMainNavigation();
        } else {
            this.openMainNavigation();
        }
    }

    openSubNavigation(element) {
        this.currentSub = element;

        let submenu = element.parentElement.querySelector(SUBMENU_SELECTOR);

        if (null === submenu) {
            return;
        }

        this.markOpen(submenu);
        this.markOpen(element);

        let right = this.getScopedElement(RIGHT_SELECTOR);
        this.unmarkOpen(right);
    }

    closeSubNavigation() {
        this.currentSub = null;

        let elements = this.getScopedElements(SUBMENU_SELECTOR + ', ' + MAIN_ENTRY_SELECTOR);

        elements.forEach(element => this.unmarkOpen(element));
    }

    markOpen(element) {
        let classes = element.getAttribute('class') + ' ' + OPEN_STATE;
        element.setAttribute('class', classes);
    }

    unmarkOpen(element) {
        let classes = element.getAttribute('class').replace(' ' + OPEN_STATE, '');
        element.setAttribute('class', classes);
    }

    openMainNavigation() {
        this.mobileOpen = true;

        let mainNav = this.getScopedElement(MAIN_MENU_SELECTOR);
        let hamburger = this.getScopedElement(HAMBURGER_SELECTOR);
        let right = this.getScopedElement(RIGHT_SELECTOR);

        this.markOpen(hamburger);
        this.markOpen(mainNav);
        this.markOpen(right);
    }

    closeMainNavigation() {
        this.mobileOpen = false;

        let mainNav = this.getScopedElement(MAIN_MENU_SELECTOR);
        let hamburger = this.getScopedElement(HAMBURGER_SELECTOR);
        let right = this.getScopedElement(RIGHT_SELECTOR);

        this.unmarkOpen(hamburger);
        this.unmarkOpen(mainNav);
        this.unmarkOpen(right);
    }
}