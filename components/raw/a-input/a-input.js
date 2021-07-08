import BaseView from 'base-view';

const INPUT_SELECTOR = '.a-input__field';
const LABEL_SELECTOR = '.a-input__label';
const EMPTY_STATE = 'is-empty';
const TOUCHED_STATE = 'is-touched';

export default class AInput extends BaseView {
	initialize() {
		this.input = this.getScopedElement( INPUT_SELECTOR );
		this.label = this.getScopedElement( LABEL_SELECTOR );

		this.manageEmptyState();
	}

	bind() {
		super.bind();

		this.on( 'keyup', INPUT_SELECTOR, () => this.setStateClasses() );
		this.on( 'blur', INPUT_SELECTOR, () => this.manageEmptyState() );
		// don't use the focus event, since it makes input fields unusable on FF mobile for Android
	}

	setStateClasses() {
		this.addClass( this.input, TOUCHED_STATE );
		this.manageEmptyState();
	}

	manageEmptyState() {
		if (this.isEmpty()) {
			this.setEmptyState();
		} else {
			this.removeEmptyState();
		}
	}

	setEmptyState() {
		this.addClass( this.label, EMPTY_STATE );
		this.addClass( this.input, EMPTY_STATE );
	}

	removeEmptyState() {
		this.removeClass( this.label, EMPTY_STATE );
		this.removeClass( this.input, EMPTY_STATE );
	}

	isEmpty() {
		return ! this.input.value;
	}
}
