import {MMenuBase} from "./m-menu--base";

const MAIN_MENU_SELECTOR = '.m-menu__nav';
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

        this.toggleMainNavigationHandler = this.toggleMainNavigation.bind(this);

        this.on("click", HAMBURGER_SELECTOR, this.toggleMainNavigationHandler);
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

        MMenuMobile.markOpen(hamburger);
        MMenuMobile.markOpen(mainNav);
        MMenuMobile.markOpen(right);
    }

    closeMainNavigation() {
        this.mobileOpen = false;

        let mainNav = this.getScopedElement(MAIN_MENU_SELECTOR);
        let hamburger = this.getScopedElement(HAMBURGER_SELECTOR);
        let right = this.getScopedElement(RIGHT_SELECTOR);

        MMenuMobile.unmarkOpen(hamburger);
        MMenuMobile.unmarkOpen(mainNav);
        MMenuMobile.unmarkOpen(right);

        this.closeSubNavigation();
    }

    afterOpenSubNavigation(element) {
        let right = this.getScopedElement(RIGHT_SELECTOR);
        MMenuMobile.unmarkOpen(right);

        let menuItems = this.getScopedElements(SUBMENU_ITEMS_SELECTOR);
        let liClicked = element.parentElement;

        menuItems.forEach(item => {
            if (item === liClicked) {
                MMenuMobile.appendClass(item, ACTIVE_STATE);
            } else {
                MMenuMobile.appendClass(item, HIDDEN_STATE);
            }
        });
    }

    afterCloseSubNavigation() {
        if (this.mobileOpen) {
            let right = this.getScopedElement(RIGHT_SELECTOR);
            MMenuMobile.markOpen(right);
        }

        let menuItems = this.getScopedElements(SUBMENU_ITEMS_SELECTOR);

        menuItems.forEach(item => {
            MMenuMobile.deleteClass(item, HIDDEN_STATE);
            MMenuMobile.deleteClass(item, ACTIVE_STATE);
        });
    }

    closeNavigation() {
        this.closeMainNavigation();
    }
}
