import BaseView from 'base-view';
import browserSupportsClassList from './../../../js/utils/classList';

const ICON_SELECTOR = '.a-search__icon';
const FIELD_SELECTOR = '.a-search__field';
const SUBMIT_LABEL_SELECTOR = '.a-search__submit-label';

const COVERING_CLASS = 'a-search--covering';

const ACTIVE_STATE = 'is-active';
const HOVER_STATE = 'highlight';

export default class ASearch extends BaseView {
	initialize() {
		this.icon = this.getScopedElement(ICON_SELECTOR);
		this.field = this.getScopedElement(FIELD_SELECTOR);

		this.overIcon = false;
		this.overField = false;
		this.active = false;

		this.covering = this.element.classList.contains(COVERING_CLASS);

		if (this.covering) {
			this.initialWidth = this.element.scrollWidth;
		}
	}

	bind() {
		super.bind();

		this.on('mouseover', ICON_SELECTOR, (event) => this.setHover(event));
		this.on('mouseover', FIELD_SELECTOR, (event) => this.setHover(event));
		this.on('mouseout', ICON_SELECTOR, (event) => this.setHover(event));
		this.on('mouseout', FIELD_SELECTOR, (event) => this.setHover(event));
		this.on('click', SUBMIT_LABEL_SELECTOR, (event) => this.handleGlassClick(event));
		this.on('focus', FIELD_SELECTOR, () => this.setFocus(true));
		this.on('blur', FIELD_SELECTOR, () => this.setFocus(false));
	}

	setHover(event) {
		let overState = 'mouseover' === event.type;
		this.setOverState(event.target, overState);

		let hoverState = (!this.active && (this.overIcon || this.overField));
		this.setHoverState(hoverState);
	}

	setHoverState(active) {
		if (active) {
			if (browserSupportsClassList(this.icon)) {
				this.addClass(this.icon, HOVER_STATE);
			}
			this.addClass(this.field, HOVER_STATE);
		} else {
			if (browserSupportsClassList(this.icon)) {
				this.removeClass(this.icon, HOVER_STATE);
			}
			this.removeClass(this.field, HOVER_STATE);
		}
	}

	handleGlassClick(event) {
		if (!this.active) {
			event.preventDefault();
			this.setFocus(true);
		} else if (!this.field.value) {
			event.preventDefault();
			this.setFocus(false);
		}
	}

	setFocus(active) {
		if (active) {
			this.open();
		} else {
			setTimeout(() => {
				// without delay we can't click the icon
				// this allows us send the form by click
				this.close();
			}, 200);
		}
	}

	open() {
		this.active = true;
		this.field.focus();
		this.setHoverState(false);
		this.addClass(this.field, ACTIVE_STATE);

		// expand the whole element to make sure we hide all elements on the right
		if (this.covering) {
			let left = this.element.offsetLeft;
			let vw = document.body.clientWidth;
			this.element.style.maxWidth = (vw - left) + 'px';
			this.addClass(ACTIVE_STATE);
		}
	}

	close() {
		this.removeClass(this.field, ACTIVE_STATE);
		this.active = false;

		// shrink the background
		if (this.covering) {
			this.element.style.maxWidth = null;
			this.removeClass(ACTIVE_STATE);
		}
	}

	setOverState(target, state) {
		if ('text' === target.type) {
			this.overField = state;
		} else {
			this.overIcon = state;
		}
	}
}
