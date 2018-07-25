import BaseView from "./../../../js/base-view";
import Swiper from 'swiper';

const SWIPER_CONTAINER_SELECTOR = '.o-people-slider__library-container';
const SLIDER_NEXT_BUTTON_SELECTOR = '.o-people-slider__slide-button--right';
const SLIDER_PREVIOUS_BUTTON_SELECTOR = '.o-people-slider__slide-button--left';

export default class OPeopleSlider extends BaseView {
    bind() {
        super.bind();

        new Swiper(SWIPER_CONTAINER_SELECTOR, {
            slidesPerView: 'auto',
            navigation: {
                nextEl: SLIDER_NEXT_BUTTON_SELECTOR,
                prevEl: SLIDER_PREVIOUS_BUTTON_SELECTOR,
            },
        });
    }
}