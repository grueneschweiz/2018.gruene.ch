import throttle from 'lodash.throttle';

import BaseView from "./../../../js/base-view";
import OCampaignInitial from "./o-campaign--initial";
import OCampaignFixed from "./o-campaign--fixed";
import OCampaignScrolling from "./o-campaign--scrolling";

const IMAGE_SELECTOR = '.o-campaign__image';
const IMAGE_WRAPPER_SELECTOR = '.o-campaign__image-wrapper';
const BARS_SELECTOR = '.o-campaign__bars';

const BLUR_SIZE_TINY = 2;
const BLUR_ADD_LARGE = 6; // so total will be 8

const SIZE_TINY = 375;
const SIZE_ADD_LARGE = 1305; // so total will be 1680

const IMAGE_OVERLAP_FACTOR = 2 / 3;

const RESIZE_THROTTLING_MS = 100;

export default class OCampaign extends BaseView {
    initialize() {
        this.image = this.getScopedElement(IMAGE_SELECTOR);
        this.imageWrapper = this.getScopedElement(IMAGE_WRAPPER_SELECTOR);
        this.bars = this.getScopedElement(BARS_SELECTOR);

        this.header = this.getComponent('OHeader0');
        this.branding = this.header.getBranding();
        this.menu = this.header.getMenu();

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
        if (!this.ticking){
            this.ticking = true;
            window.requestAnimationFrame(this.onScroll.bind(this));
        }
    }

    onResize() {
        this.getScrollStart(true);
        this.bars.style.height = this.image.clientHeight + 'px';
    }

    onScroll() {
        if (!this.header.getFixed()) {
            this.setState(OCampaignInitial);
        } else {
            if (this.getScrollTop() < this.getScrollStart()) {
                this.setState(OCampaignFixed);
            } else {
                this.setState(OCampaignScrolling);
            }
        }

        this.state.run();
        this.ticking = false;
    }

    setState(state) {
        if (!(this.state instanceof state)) {
            this.state = new state(this);
        }
    }

    getScrollStart(recalculate = false) {
        if (!this.scrollStart || recalculate) {
            this.scrollStart = this.header.getBranding().clientHeight
                + this.image.clientTop
                + this.image.clientHeight * IMAGE_OVERLAP_FACTOR;
        }

        return this.scrollStart;
    }

    getBranding() {
        return this.branding;
    }

    getMenu() {
        return this.menu;
    }

    getImage() {
        return this.image;
    }

    getImageWrapper() {
        return this.imageWrapper;
    }

    translate(y) {
        return `translate3d(0px, ${y}px, 0px)`;
    }

    /**
     * Calculate the max blur size respecting the device size (cached)
     *
     * @returns {number}
     */
    getSizedBlur() {
        if (!this.sizedBlur) {
            let vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
            let sizedBlur = (vw - SIZE_TINY) / SIZE_ADD_LARGE * BLUR_ADD_LARGE + BLUR_SIZE_TINY;
            this.sizedBlur = Math.max(sizedBlur, 0);
        }
        return this.sizedBlur;
    }

    destroy() {
        super.destroy();

        window.removeEventListener("scroll", this.scrollHandler);
        window.removeEventListener("resize", this.resizeHandler);
    }
}