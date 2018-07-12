import BaseView from "base-view";

export const MAIN_ENTRY_SELECTOR = '.m-menu__nav-link';
export const SUBMENU_SELECTOR = '.m-menu__submenu';

export const OPEN_STATE = 'is-open';

export class MMenuBase extends BaseView {
    initialize() {
        super.initialize();

        this.currentSub = null;
    }

    bind() {
        super.bind();

        this.on("click", MAIN_ENTRY_SELECTOR, () => this.toggleSubNavigation());
    }

    toggleSubNavigation() {
        if (this.currentSub !== event.target) {
            this.openSubNavigation(event.target);
        }
        else {
            this.closeSubNavigation();
        }
    }

    openSubNavigation(element) {
        this.currentSub = element;

        let submenu = element.parentElement.querySelector(SUBMENU_SELECTOR);

        if (null === submenu) {
            return;
        }

        this.addClass(submenu, OPEN_STATE);
        this.addClass(element, OPEN_STATE);

        this.afterOpenSubNavigation(element);
    }

    closeSubNavigation() {
        this.currentSub = null;

        let elements = this.getScopedElements(SUBMENU_SELECTOR + ', ' + MAIN_ENTRY_SELECTOR);

        elements.forEach(element => this.removeClass(element, OPEN_STATE));

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
    afterOpenSubNavigation(){}

    /**
     * overwrite those functions to do some more stuff on closing
     */
    afterCloseSubNavigation(){}
}