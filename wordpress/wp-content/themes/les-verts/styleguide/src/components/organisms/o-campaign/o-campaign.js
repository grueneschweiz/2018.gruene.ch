import throttle from 'lodash.throttle';

import BaseView from "./../../../js/base-view";

const IMAGE_SELECTOR = '.o-campaign__image';
const IMAGE_WRAPPER_SELECTOR = '.o-campaign__image-wrapper';

const SCROLL_SPEED = 0.1;
const RESIZE_THROTTLING_MS = 100;

export default class OCampaign extends BaseView {
    initialize() {
        this.image = this.getScopedElement(IMAGE_SELECTOR);
        this.imageWrapper = this.getScopedElement(IMAGE_WRAPPER_SELECTOR);

        this.header = this.getComponent('OHeader0');
        this.branding = this.header.getBranding();

        this.ticking = false;
    }

    bind() {
        super.bind();

        this.scrollHandler = () => this.requestTick();
        this.resizeHandler = throttle(() => this.onResize(), RESIZE_THROTTLING_MS, {leading: false, trailing: true});

        window.addEventListener("resize", this.resizeHandler);
        window.addEventListener("scroll", this.scrollHandler, false);

        this.scrollHandler();
        this.resizeHandler();
    }

    requestTick() {
        if (!this.ticking) {
            this.ticking = true;
            window.requestAnimationFrame(this.onScroll.bind(this));
        }
    }

    onResize() {
        this.setBrandingHeight();
        this.setScrollStop();
    }

    onScroll() {
        if (!this.header.getFixed()) {
            this.imageWrapper.style.transform = this.translate(-this.getScrollTop());
        } else {
            if (this.getScrollTop() < this.scrollStop) {
                let delta = this.getScrollTop() - this.brandingHeight;
                this.imageWrapper.style.transform = this.translate(-this.brandingHeight - delta * SCROLL_SPEED);
            }
        }

        this.ticking = false;
    }

    setBrandingHeight() {
        this.brandingHeight = this.branding.clientHeight;
    }

    setScrollStop() {
        this.scrollStop = this.brandingHeight + this.element.clientHeight;
    }

    translate(y) {
        return `translate3d(0px, ${y}px, 0px)`;
    }

    destroy() {
        super.destroy();

        window.removeEventListener("scroll", this.scrollHandler);
        window.removeEventListener("resize", this.resizeHandler);
    }
}