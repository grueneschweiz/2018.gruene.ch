import { EventEmitter } from 'events';
import debounce from 'utils/debounce';

const MOBILE_BREAKPOINT = 768;
const LAPTOP_BREAKPOINT = 1024;
const LARGE_LAPTOP_BREAKPOINT = 1280;
const DEBOUNCE_DELAY = 100; // in ms

export default class Viewport extends EventEmitter {
	init() {
		this.resizeHandler();
	}

	/**
	 * Return the viewport width.
	 * @return {Number}
	 */
	getViewportWidth() {
		return Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
	}

	/**
	 * Return the viewport height.
	 * @return {Number}
	 */
	getViewportHeight() {
		return Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
	}

	getViewportRatio() {
		return (this.getCurrentWidth() / this.getCurrentHeight());
	}

	setCurrentWidth(width) {
		this.current_width = width;
	}

	getCurrentWidth() {
		return this.current_width;
	}

	setCurrentHeight(height) {
		this.current_height = height;
	}

	getCurrentHeight() {
		return this.current_height;
	}

	bindResize() {
        window.addEventListener('resize', debounce(this.resizeHandler.bind(this), DEBOUNCE_DELAY, false));
	}

	resizeHandler() {
		this.setCurrentWidth(this.getViewportWidth());
		this.setCurrentHeight(this.getViewportHeight());
		this.emit('change');
	}

	isMobile() {
		return this.getCurrentWidth() < MOBILE_BREAKPOINT;
	}

	isTablet() {
			return this.getCurrentWidth() >= MOBILE_BREAKPOINT &&  this.getCurrentWidth() < LAPTOP_BREAKPOINT;
	}

	isComputer() {
			return this.getCurrentWidth() >= LAPTOP_BREAKPOINT;
	}

	isLaptop() {
			return this.getCurrentWidth() >= LAPTOP_BREAKPOINT &&  this.getCurrentWidth() < LARGE_LAPTOP_BREAKPOINT;
	}

	isLargeLaptop() {
			return this.getCurrentWidth() > LARGE_LAPTOP_BREAKPOINT;
	}
}
