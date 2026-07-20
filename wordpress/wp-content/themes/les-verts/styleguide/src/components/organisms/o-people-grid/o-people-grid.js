import BaseView from './../../../js/base-view';
import Swiper from 'swiper';
import { A11y, Keyboard, Navigation } from 'swiper/modules';

const SWIPER_CONTAINER_SELECTOR = '.o-people-grid__library-container';
const SLIDER_NEXT_BUTTON_SELECTOR = '.o-people-grid__slide-button--right';
const SLIDER_PREVIOUS_BUTTON_SELECTOR = '.o-people-grid__slide-button--left';

export default class OPeopleGrid extends BaseView {
	bind() {
		super.bind();

		new Swiper( SWIPER_CONTAINER_SELECTOR, {
			modules: [ Keyboard, Navigation, A11y ],
			slidesPerView: 'auto',
			navigation: {
				nextEl: SLIDER_NEXT_BUTTON_SELECTOR,
				prevEl: SLIDER_PREVIOUS_BUTTON_SELECTOR,
			},
			keyboard: {
				enabled: true,
				onlyInViewport: false,
			},
		} );
	}
}
