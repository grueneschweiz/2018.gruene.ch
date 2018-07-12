import {MMenuBase} from "./m-menu--base";

const CLOSE_SELECTOR = '.m-menu__submenu__close';

export default class MMenuDesktop extends MMenuBase {
    bind() {
        super.bind();
        this.closeSubNavigationHandler = this.closeSubNavigation.bind(this);

        this.on("click", CLOSE_SELECTOR, this.closeSubNavigationHandler);
    }
}