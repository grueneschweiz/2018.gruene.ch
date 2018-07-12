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
        this.toggleSubNavigationHandler = this.toggleSubNavigation.bind(this);

        this.on("click", MAIN_ENTRY_SELECTOR, this.toggleSubNavigationHandler);
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

        MMenuBase.markOpen(submenu);
        MMenuBase.markOpen(element);

        this.afterOpenSubNavigation(element);
    }

    closeSubNavigation() {
        this.currentSub = null;

        let elements = this.getScopedElements(SUBMENU_SELECTOR + ', ' + MAIN_ENTRY_SELECTOR);

        elements.forEach(element => MMenuBase.unmarkOpen(element));

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

    static markOpen(element) {
        MMenuBase.appendClass(element, OPEN_STATE);
    }

    static unmarkOpen(element) {
        MMenuBase.deleteClass(element, OPEN_STATE);
    }

    static appendClass(element, cls) {
        let classes = element.getAttribute('class') + ' ' + cls;
        element.setAttribute('class', classes);
    }

    static deleteClass(element, cls) {
        let classes = element.getAttribute('class').replace(cls, '');
        classes = classes.replace(/\s+/g, ' ');
        element.setAttribute('class', classes);
    }
}