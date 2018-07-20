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

export default class OCampaign extends BaseView {
    initialize() {
        this.image = this.getScopedElement(IMAGE_SELECTOR);
        this.imageWrapper = this.getScopedElement(IMAGE_WRAPPER_SELECTOR);
        this.bars = this.getScopedElement(BARS_SELECTOR);

        this.header = this.getComponent('OHeader0');
        this.branding = this.header.getBranding();
        this.menu = this.header.getMenu();

        this.onResize();
        this.onScroll();
    }

    bind() {
        super.bind();

        this.scrollHandler = () => requestAnimationFrame(this.onScroll.bind(this));
        this.resizeHandler = () => requestAnimationFrame(this.onResize.bind(this));

        window.addEventListener("scroll", this.scrollHandler);
        window.addEventListener("resize", this.resizeHandler);
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

    getScrollTop() {
        return window.pageYOffset || document.documentElement.scrollTop;
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