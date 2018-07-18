import throttle from 'lodash.throttle';

import BaseView from "base-view";

const IMAGE_SELECTOR = '.o-campaign__image';
const BARS_SELECTOR = '.o-campaign__bars';
const CTA_WRAPPER_SELECTOR = '.o-campaign__cta-wrapper';

const BLUR_SIZE_TINY = 2;
const BLUR_ADD_LARGE = 6; // so total will be 8

const SIZE_TINY = 375;
const SIZE_ADD_LARGE = 1305; // so total will be 1680

export default class OCampaign extends BaseView {
    initialize() {
        this.image = this.getScopedElement(IMAGE_SELECTOR);
        this.bars = this.getScopedElement(BARS_SELECTOR);
        this.ctaWrapper = this.getScopedElement(CTA_WRAPPER_SELECTOR);

        this.isFixed = true;

        this.onResize();
    }

    bind() {
        super.bind();

        this.scrollHandler = throttle(() => this.onScroll(), 100, {leading: true, trailing: true});
        this.resizeHandler = throttle(() => this.onResize(), 100, {leading: true, trailing: true});

        window.addEventListener("scroll", this.scrollHandler);
        window.addEventListener("resize", this.resizeHandler);
    }

    onResize() {
        this.getScrollStart(true);
        this.bars.style.height = this.image.clientHeight + 'px';
    }

    getScrollStart(recalculate = false) {
        if (!this.scrollStart || recalculate) {
            this.scrollStart = this.image.clientTop + this.image.clientHeight * 2 / 3; // 2 / 3 so it doesn't scroll fully up to the top
        }

        return this.scrollStart;
    }

    onScroll() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        let scrollStart = this.getScrollStart();

        // handle positioning
        if (this.isFixed && scrollTop > scrollStart) {
            this.image.style.top = scrollTop + 'px';
            this.image.style.position = 'absolute';
            this.isFixed = false;
        }

        if (!this.isFixed && scrollTop <= scrollStart) {
            this.image.style.top = 0;
            this.image.style.position = 'fixed';
            this.isFixed = true;
        }

        // handle bluring
        if (this.isFixed && scrollTop < scrollStart && scrollTop > this.image.clientTop) {
            // calculate the effective blur respecting the scroll height
            let blur = (scrollTop - this.image.clientTop) / scrollStart * this.getSizedBlur();

            this.image.style.filter = "blur("+blur+"px)";
        } else {
            if (scrollTop < scrollStart) {
                this.image.style.filter = 'none';
            } else {
                this.image.style.filter = "blur("+this.getSizedBlur()+"px)";
            }
        }
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