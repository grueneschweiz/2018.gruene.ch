import BaseView from 'base-view';

const WIN_CLASS = 'windows';

export default class ABar extends BaseView {
	bind() {
		if (navigator.appVersion.indexOf("Win") !== -1) {
			this.addClass(WIN_CLASS)
		}
	}
}
