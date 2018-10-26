import BaseView from 'base-view';

const LAZY_STATE = 'a-image__image--lazy';
const LAZY_LOADED = 'a-image__image--loaded';
const DEBOUNCE_DELAY_MS = 300;

export default class AInput extends BaseView {
	initialize() {
		this.ticking = false;
		this.loading = false;
	}

	bind() {
		super.bind();

		// use this variable to have a reference to destroy it later on
		this.eventHandler = () => this.requestTick();

		window.addEventListener( 'scroll', this.eventHandler, false );
		window.addEventListener( 'resize', this.eventHandler, false );

		this.requestTick();
	}

	requestTick() {
		this.timer = this.timer || setTimeout( () => {
			this.timer = null;
			if (! this.ticking && ! this.loading) {
				this.ticking = true;
				window.requestAnimationFrame( this.maybeLoadImage.bind( this ) );
			}
		}, DEBOUNCE_DELAY_MS );
	}

	maybeLoadImage() {
		if (this.inView()) {
			this.loading = true;
			let img = this.loadFullImage();
			if (img.complete) {
				this.replaceImage( img );
			} else {
				img.onload = () => this.replaceImage( img );
			}
		}
		this.ticking = false;
	}

	inView() {
		let windowTop = window.pageYOffset;
		let windowBottom = windowTop + window.innerHeight;
		let imgRect = this.element.getBoundingClientRect();
		let imgTop = windowTop + imgRect.top;
		let imgBottom = imgTop + imgRect.height;
		return ( windowTop < imgBottom && windowBottom > imgTop );
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

		this.destroy();
	}

	destroy() {
		window.removeEventListener( 'scroll', this.eventHandler );
		window.removeEventListener( 'resize', this.eventHandler );

		super.destroy();
	}
}
