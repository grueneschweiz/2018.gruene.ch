import BaseView from 'base-view';
import distractionFree from '../../../js/service/distractionFree';

const DISTRACTING_WIDGET_CLASS = 'distracting_widget';

export default class OFooter extends BaseView {
	initialize() {
		this.distractionFree = distractionFree();
	}

	bind() {
		super.bind();

		if (!this.distractionFree) {
			this.showWidgets();
		}
	}

	showWidgets() {
		const widgets = this.getScopedElements( '.' + DISTRACTING_WIDGET_CLASS );

		for (const el of widgets) {
			this.removeClass( el, DISTRACTING_WIDGET_CLASS );
		}
	}
}
