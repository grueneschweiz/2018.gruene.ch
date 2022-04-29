import BaseView from 'base-view';
import distractionFree from '../../../js/service/distractionFree';

const DISTRACTING_BREADCRUMBS_CLASS = 'page__breadcrumbs--distracting';
const DISTRACTING_POST_META_CLASS = 'page__post-meta--distracting';

export default class Page extends BaseView {
	initialize() {
		this.distractionFree = distractionFree();
	}

	bind() {
		super.bind();

		if (!this.distractionFree) {
			this.showBreadcrumbs();
			this.showPostMeta();
		}
	}

	showBreadcrumbs() {
		this.removeDistractingClass( DISTRACTING_BREADCRUMBS_CLASS );
	}

	showPostMeta() {
		this.removeDistractingClass( DISTRACTING_POST_META_CLASS );
	}

	removeDistractingClass( domClass ) {
		const el = this.getScopedElement( '.' + domClass );

		if (el) {
			this.removeClass( el, domClass );
		}
	}
}
