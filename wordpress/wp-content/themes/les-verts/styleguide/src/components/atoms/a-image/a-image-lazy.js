import BaseView from 'base-view';
import inView from '../../../js/service/inview';

const BASE_CONTAINER_SELECTOR = '.a-image';

const LAZY_STATE = 'a-image__image--lazy';
const LAZY_LOADED = 'a-image__image--loaded';
const DEBOUNCE_DELAY_MS = 300;

export default class AImageLazy extends BaseView {
	initialize() {
		this.loading = false;
	}

	bind() {
		super.bind();

		inView(this.element, DEBOUNCE_DELAY_MS).then(() => this.maybeLoadImage());
	}

	maybeLoadImage() {
		if (!this.loading){
			this.loading = true;
			let img = this.loadFullImage();
			if (img.complete) {
				this.replaceImage( img );
			} else {
				img.onload = () => this.replaceImage( img );
			}
		}
	}

	loadFullImage() {
		let img = this.element.cloneNode( true );
		img.srcset = img.dataset.srcset || '';
		img.sizes = img.dataset.sizes || '';
		img.removeAttribute( 'data-srcset' );
		img.removeAttribute( 'data-sized' );
		return img;
	}

	replaceImage( img ) {
		this.element.parentNode.replaceChild( img, this.element );
		img.classList.add( LAZY_LOADED );
		img.classList.remove( LAZY_STATE );

		this.triggerAfterReplaceEvent( img.closest(BASE_CONTAINER_SELECTOR) );

		this.destroy();
	}

	triggerAfterReplaceEvent(elem) {
		// use the old fashioned way, cause we need it in IE
		let event = document.createEvent('Event');
		event.initEvent('afterReplaceImage', true, true);
		elem.dispatchEvent(event);
	}
}
