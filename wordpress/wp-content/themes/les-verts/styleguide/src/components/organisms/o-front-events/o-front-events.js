import BaseView from "./../../../js/base-view";

const HIDDEN_CLASS = 'o-front-events__event--hidden';

const BUTTON_SELECTOR = '[data-toggle-events]';
const MORE_SELECTOR = '.o-front-events__more';
const HIDDEN_SELECTOR = '.'+HIDDEN_CLASS;

const STATE_HIDDEN = 'is-hidden';

export default class OFrontEvents extends BaseView {
	bind() {
		super.bind();

		this.on('click', BUTTON_SELECTOR, () => {
			let elements = this.getScopedElements(HIDDEN_SELECTOR);
			for (let element of elements) {
				this.removeClass(element, HIDDEN_CLASS);
			}

			let more_button = this.getScopedElement(MORE_SELECTOR);
			this.addClass(more_button, STATE_HIDDEN);
		});
	}
}
