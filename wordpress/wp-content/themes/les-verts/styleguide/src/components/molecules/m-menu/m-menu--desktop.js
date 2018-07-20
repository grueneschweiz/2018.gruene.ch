import {MMenuBase, SUBMENU_SELECTOR} from "./m-menu--base";

const CLOSE_SELECTOR = '.m-menu__submenu__close';
const NOT_SCROLLABLE_STATE = 'is-not-scrollable';

export default class MMenuDesktop extends MMenuBase {
    initialize() {
        super.initialize();

        this.submenu = this.getScopedElement(SUBMENU_SELECTOR);
    }

    bind() {
        super.bind();

        this.on("click", CLOSE_SELECTOR, () => this.closeSubNavigation());
    }

    afterOpenSubNavigation() {
        if (this.submenu.clientHeight === this.submenu.scrollHeight){
            this.addClass(this.submenu, NOT_SCROLLABLE_STATE);
        }
    }

    afterCloseSubNavigation() {
        this.removeClass(this.submenu, NOT_SCROLLABLE_STATE);
    }
}