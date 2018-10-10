import BaseView from 'base-view';

const BUTTON_SELECTOR = '.m-person__toggle';
const CONTENT_SELECTOR = '.m-person__expandable';

const COLLABSED_STATE = 'is-collapsed';
const EXPANDED_STATE = 'is-expanded';

export default class MPerson extends BaseView {
	initialize() {
		this.button = this.getScopedElement(BUTTON_SELECTOR);
		this.content = this.getScopedElement(CONTENT_SELECTOR);

		this.expanded = false;
	}

	bind() {
		super.bind();

		this.on( 'click', BUTTON_SELECTOR, () => this.toggle() );
	}

	toggle() {
		if (this.expanded) {
			// collaps it
			this.removeClass(this.button, EXPANDED_STATE);
			this.removeClass(this.content, EXPANDED_STATE);
			this.addClass(this.button, COLLABSED_STATE);
			this.addClass(this.content, COLLABSED_STATE);
			this.button.innerText = this.button.getAttribute('data-expand');
		} else {
			// expand it
			this.removeClass(this.button, COLLABSED_STATE);
			this.removeClass(this.content, COLLABSED_STATE);
			this.addClass(this.button, EXPANDED_STATE);
			this.addClass(this.content, EXPANDED_STATE);
			this.button.innerText = this.button.getAttribute('data-collaps');
		}

		this.content.setAttribute('aria-hidden', this.expanded);
		this.expanded = !this.expanded;
	}
}
