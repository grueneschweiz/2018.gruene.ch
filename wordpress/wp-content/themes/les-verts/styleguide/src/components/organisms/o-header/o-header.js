import throttle from 'lodash.throttle';

import BaseView from "base-view";

const BRANDING_SELECTOR = '.o-header__branding';
const MENU_SELECTOR = '.o-header__menu';
const SPACER_SELECTOR = '.o-header__spacer';

const FIXED_STATE = 'is-fixed';
const HIDDEN_STATE = 'is-hidden';
const ACTIVE_STATE = 'is-active';

export default class OHeader extends BaseView {
    initialize() {
        this.branding = this.getScopedElement(BRANDING_SELECTOR);
        this.menu = this.getScopedElement(MENU_SELECTOR);
        this.spacer = this.getScopedElement(SPACER_SELECTOR);
    }

    bind() {
        super.bind();

        window.addEventListener("scroll", throttle(() => this.setMenuBarPosition(), 50));
    }

    setMenuBarPosition() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop >= this.branding.clientHeight && !this.isFixed) {
            this.isFixed = true;

            this.addClass(this.menu, FIXED_STATE);
            this.addClass(this.branding, HIDDEN_STATE);
            this.addClass(this.spacer, ACTIVE_STATE);
        }

        if (scrollTop < this.branding.clientHeight && this.isFixed) {
            this.isFixed = false;

            this.removeClass(this.menu, FIXED_STATE);
            this.removeClass(this.branding, HIDDEN_STATE);
            this.removeClass(this.spacer, ACTIVE_STATE);
        }
    }
}