import BaseView from './../../../js/base-view';
import browserSupportsClassList from './../../../js/utils/classList';

const SELECT_SELECTOR = '#party-selector';
const DIALOG_WRAPPER_SELECTOR = '.o-engagement-map__dialog';
const MAP_SELECTOR = '.o-engagement-map__map';
const MAP_SECTION_SELECTOR = MAP_SELECTOR + ' .map-item';
const DIALOG_CLOSE_SELECTOR = '.a-simple-dialog__close';
const MODAL_SELECTOR = '.o-engagement-map__modal';

const DIALOG_ID_PREFIX = 'a-simple-dialog--';

const OPEN_STATE = 'is-open';
const ALIGN_LEFT_STATE = 'pull-left';
const ACTIVE_STATE = 'is-active';

/*eslint no-console: ["error", { allow: ["error"] }] */
export default class OEngagementMap extends BaseView {

	initialize() {
		super.initialize();

		this.select = this.getScopedElement(SELECT_SELECTOR);
		this.map = this.getScopedElement(MAP_SELECTOR);
		this.modal = this.getScopedElement(MODAL_SELECTOR);

		this.dialog = null;
		this.mapItem = null;
	}

	bind() {
		super.bind();

		this.on('change', SELECT_SELECTOR, () => this.selectHandler(), false, false);
		this.on('click', MAP_SECTION_SELECTOR, (event) => this.mapClickHandler(event));
		this.on('click', DIALOG_CLOSE_SELECTOR, () => this.closeClickHandler());
		this.on('click', MODAL_SELECTOR, () => this.closeClickHandler());
	}

	closeClickHandler() {
		this.hideDialog();
		this.unhighlightClickedMapItem();
	}

	mapClickHandler(event) {
		this.select.value = event.delegateTarget.id;
		this.select.dispatchEvent(new Event('change'));
		this.setPosition(event.offsetX);
	}

	selectHandler() {
		this.hideDialog();
		this.unhighlightClickedMapItem();

		if (!this.select.value) {
			this.dialog = null;
			return;
		}

		this.showDialog();
		this.highlightClickedMapItem();
	}

	setMapItem() {
		let selector = '#' + this.select.value;
		let mapItem = this.getScopedElement(MAP_SELECTOR).querySelector(selector);

		if (!mapItem) {
			this.mapItem = null;
			console.error(`NOT FOUND: There was no map item with the selector '${selector}'.`);
			return;
		}

		this.mapItem = mapItem;
	}

	unhighlightClickedMapItem() {
		// check for class list as well to ensure that browsers,
		// which don't support the classList property on svg elements,
		// don't crash.
		if (this.mapItem && browserSupportsClassList(this.mapItem)) {
			this.removeClass(this.mapItem, ACTIVE_STATE);
		}
	}

	highlightClickedMapItem() {
		this.setMapItem();

		// check for class list as well to ensure that browsers,
		// which don't support the classList property on svg elements,
		// don't crash.
		if (this.mapItem && browserSupportsClassList(this.mapItem)) {
			this.addClass(this.mapItem, ACTIVE_STATE);
		}
	}

	setDialog() {
		let selector = '#' + DIALOG_ID_PREFIX + this.select.value;
		let dialog = this.getScopedElement(selector);

		if (!dialog) {
			this.dialog = null;
			console.error(`NOT FOUND: There was no address with the selector '${selector}'.`);
			return;
		}

		this.dialog = dialog.closest(DIALOG_WRAPPER_SELECTOR);
	}

	hideDialog() {
		this.hideModal();

		if (this.dialog) {
			this.removeClass(this.dialog, OPEN_STATE);
			this.removeClass(this.dialog, ALIGN_LEFT_STATE);
		}
	}

	showDialog() {
		this.setDialog();

		if (this.dialog) {
			this.addClass(this.dialog, OPEN_STATE);

			this.showModal();
		}
	}

	showModal() {
		this.addClass(this.modal, OPEN_STATE);
	}

	hideModal() {
		this.removeClass(this.modal, OPEN_STATE);
	}

	setPosition(clickOffsetX) {
		if (!this.dialog) {
			return;
		}

		if ((this.map.clientWidth - clickOffsetX) < this.dialog.clientWidth) {
			this.addClass(this.dialog, ALIGN_LEFT_STATE);
		}
	}
}
