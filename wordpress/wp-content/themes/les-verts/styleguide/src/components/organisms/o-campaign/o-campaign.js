import throttle from 'lodash.throttle';

import BaseView from "./../../../js/base-view";
import OCampaignInitial from "./o-campaign--initial";
import OCampaignFixed from "./o-campaign--fixed";
import OCampaignScrolling from "./o-campaign--scrolling";

const IMAGE_SELECTOR = '.o-campaign__image';
const BARS_SELECTOR = '.o-campaign__bars';

const BLUR_SIZE_TINY = 2;
const BLUR_ADD_LARGE = 6; // so total will be 8

const SIZE_TINY = 375;
const SIZE_ADD_LARGE = 1305; // so total will be 1680

const IMAGE_OVERLAP_FACTOR = 2 / 3;

const SCROLL_THROTTLING_MS = 20;
const RESIZE_THROTTLING_MS = 100;

export default class OCampaign extends BaseView {
    initialize() {
        this.image = this.getScopedElement(IMAGE_SELECTOR);
        this.bars = this.getScopedElement(BARS_SELECTOR);

        this.header = this.getComponent('OHeader0');

        this.onResize();
        this.onScroll();
    }

    bind() {
        super.bind();

        this.scrollHandler = throttle(() => this.onScroll(), SCROLL_THROTTLING_MS, {leading: false, trailing: true});
        this.resizeHandler = throttle(() => this.onResize(), RESIZE_THROTTLING_MS, {leading: false, trailing: true});

        window.addEventListener("scroll", this.scrollHandler);
        window.addEventListener("resize", this.resizeHandler);
    }

    onResize() {
        this.getScrollStart(true);
        this.bars.style.height = this.image.clientHeight + 'px';
    }

    getScrollStart(recalculate = false) {
        if (!this.scrollStart || recalculate) {
            this.scrollStart = this.header.getBranding().clientHeight
                + this.image.clientTop
                + this.image.clientHeight * IMAGE_OVERLAP_FACTOR;
        }

        return this.scrollStart;
    }

    getScrollTop() {
        return window.pageYOffset || document.documentElement.scrollTop;
    }

    getHeader() {
        return this.header;
    }

    getImage() {
        return this.image;
    }

    setState(state) {
        if (! (this.state instanceof state)) {
            this.state = new state(this);
        }
    }

    onScroll() {
        if (! this.header.getFixed()) {
            this.setState(OCampaignInitial);
        } else {
            if (this.getScrollTop() < this.getScrollStart()) {
                this.setState(OCampaignFixed);
            } else {
                this.setState(OCampaignScrolling);
            }
        }

        this.state.run();
    }

    /**
     * Calculate the max blur size respecting the device size
     *
     * @returns {number}
     */
    getSizedBlur() {
        let vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        return (vw - SIZE_TINY) / SIZE_ADD_LARGE * BLUR_ADD_LARGE + BLUR_SIZE_TINY;
    }

    destroy() {
        super.destroy();

        window.removeEventListener("scroll", this.scrollHandler);
        window.removeEventListener("resize", this.resizeHandler);
    }
}