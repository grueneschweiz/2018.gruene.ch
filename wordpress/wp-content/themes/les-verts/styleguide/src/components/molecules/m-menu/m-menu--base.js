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

        this.on("click", MAIN_ENTRY_SELECTOR, (event) => this.toggleSubNavigation(event));
    }

    toggleSubNavigation(event) {
        if (this.currentSub !== event.target) {
            this.openSubNavigation(event);
        }
        else {
            event.preventDefault();
            this.closeSubNavigation();
        }
    }

    openSubNavigation(event) {
        let element = event.target;
        let submenu = element.parentElement.querySelector(SUBMENU_SELECTOR);

        if (null === submenu) {
            // if there is no submenu, handle it as regular link
            return;
        }

        event.preventDefault();

        this.currentSub = element;

        this.addClass(submenu, OPEN_STATE);
        this.addClass(element, OPEN_STATE);
        element.setAttribute('aria-expanded', true);
        element.nextElementSibling.firstElementChild.firstElementChild.firstElementChild.focus();

        this.afterOpenSubNavigation(element);
    }

    closeSubNavigation() {
        this.currentSub = null;

        let elements = this.getScopedElements(SUBMENU_SELECTOR + ', ' + MAIN_ENTRY_SELECTOR);

        elements.forEach(element => {
            this.removeClass(element, OPEN_STATE);
            element.setAttribute('aria-expanded', false);
        });

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
    afterOpenSubNavigation() {
    }

    /**
     * overwrite those functions to do some more stuff on closing
     */
    afterCloseSubNavigation() {
    }
}