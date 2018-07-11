import BaseView from "base-view";

const MAIN_ENTRY_SELECTOR = '.m-menu__nav-link';
const SUBMENU_SELECTOR = '.m-menu__submenu';
const CLOSE_SELECTOR = '.m-menu__submenu__close';

const OPEN_STATE = 'is-open';

export default class MMenu extends BaseView {

    initialize() {
        this.currentOpen = null;
    }

    bind() {
        this.toggleNavigationHandler = this.toggleNavigation.bind(this);
        this.closeNavigationHandler = this.closeNavigation.bind(this);

        this.on("click", MAIN_ENTRY_SELECTOR, this.toggleNavigationHandler);
        this.on("click", CLOSE_SELECTOR, this.closeNavigationHandler);
    }

    toggleNavigation(event) {
        if (this.currentOpen !== event.target) {
            this.openNavigation(event.target);
        }
        else {
            this.closeNavigation();
        }
    }

    openNavigation(element) {
        this.currentOpen = element;

        let submenu = element.parentElement.querySelector(SUBMENU_SELECTOR);

        if (null === submenu) {
            return;
        }

        let classes = submenu.getAttribute('class') + ' ' + OPEN_STATE;
        submenu.setAttribute('class', classes);
    }

    closeNavigation() {
        this.currentOpen = null;

        let elements = this.getScopedElements(SUBMENU_SELECTOR);

        elements.forEach(element => {
            console.log(element);
            let classes = element.getAttribute('class').replace(' ' + OPEN_STATE, '');
            element.setAttribute('class', classes);
        });
    }
}