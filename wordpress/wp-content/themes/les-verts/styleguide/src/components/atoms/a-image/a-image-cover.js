import BaseView from 'base-view';

const COVER_IMAGE_SELECTOR = '.a-image__image--fp';

const LAZY_LOADED = 'a-image__image--loaded';
const COMPATIBILITY_STATE = 'object-fit-ie';

const LAZY_LOAD_WAIT_MS = 200;

/**
 * Since IE and Edge don't support object-fit, use background: cover css for them
 *
 * @see https://medium.com/@primozcigler/neat-trick-for-css-object-fit-fallback-on-edge-and-other-browsers-afbc53bbb2c3
 */
export default class AImageCover extends BaseView {
	initialize() {
		this.initUrl = this.getUrl();
	}

	bind() {
		super.bind();

		this.on('afterReplaceImage', () => this.objectFit());

		//this.polyfill();
	}

	objectFit() {
		if ('objectFit' in document.documentElement.style !== false) {
			this.destroy();
			return;
		}

		const img = this.getScopedElement(COVER_IMAGE_SELECTOR);

		if (!img) {
			this.destroy();
			return;
		}

		this.polyfill(img);
	}

	polyfill(img) {
		const url = this.getUrl();

		// wait for lazy loading to be complete
		if (img.classList.contains(LAZY_LOADED)) {
			if (this.initUrl === url) {
				window.setTimeout(() => this.polyfill(img), LAZY_LOAD_WAIT_MS);
				return;
			}
		}

		let fp = img.className.match(/\ba-image__image--fp-[^\s]+\b/g);
		if (fp) {
			this.addClass('a-image__image--fp');
			this.addClass(fp[0]);
		}

		this.addClass(COMPATIBILITY_STATE);
		this.element.style.backgroundImage = "url('"+url+"')";

		this.addClass(img, COMPATIBILITY_STATE);
	}

	getUrl() {
		const img = this.getScopedElement(COVER_IMAGE_SELECTOR);

		if (img) {
			return img.src;
		}

		return '';
	}
}
