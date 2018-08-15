import BaseView from "./../../../js/base-view";

const MOBILE_SELECTOR = '.m-share__mobile';
const BUTTONS_SELECTOR = '.m-share__buttons';

const OPEN_STATE = 'is-open';

export default class MShare extends BaseView {
	initialize() {
		this.buttons = this.getScopedElement(BUTTONS_SELECTOR);

		this.isOpen = false;
	}

	bind() {
		super.bind();

		this.on('click', MOBILE_SELECTOR, () => {
			if (this.isOpen){
				this.removeClass(this.buttons, OPEN_STATE);
			} else {
				this.addClass(this.buttons, OPEN_STATE);
			}

			this.isOpen = !this.isOpen;
		});
	}
}
