import BaseView from 'base-view';

const INPUT_SELECTOR = '.a-select__field';
const BACKGROUND_SELECTOR = '.a-select__background';
const LABEL_SELECTOR = '.a-select__label';

const FOCUS_WITHIN_STATE = 'has-focus-within';
const EMPTY_STATE = 'is-empty';
const TOUCHED_STATE = 'is-touched';
const VALID_STATE = 'is-valid';
const INVALID_STATE = 'is-invalid';

export default class ASelect extends BaseView {
	initialize() {
		this.select = this.getScopedElement( INPUT_SELECTOR );
		this.background = this.getScopedElement( BACKGROUND_SELECTOR );
		this.label = this.getScopedElement( LABEL_SELECTOR );

		this.manageEmptyState();
	}

	bind() {
		super.bind();

		this.on( 'focus', INPUT_SELECTOR, () => this.onSetFocus() );
		this.on( 'blur', INPUT_SELECTOR, () => this.onRemoveFocus() );
		this.on( 'change', INPUT_SELECTOR, () => this.onChange() );
	}

	onChange() {
		this.manageEmptyState();
		this.setTouchedState();
		this.manageValidState();
	}

	onSetFocus() {
		this.setTouchedState();
		this.addClass( this.background, FOCUS_WITHIN_STATE );
		this.manageEmptyState();
	}

	onRemoveFocus() {
		this.removeClass( this.background, FOCUS_WITHIN_STATE );
		this.manageEmptyState();
		this.manageValidState();
	}

	manageEmptyState() {
		if (this.isEmpty()) {
			this.setEmptyState();
		} else {
			this.removeEmptyState();
		}
	}

	isEmpty() {
		return ! this.select.value;
	}

	setEmptyState() {
		this.addClass( this.label, EMPTY_STATE );
	}

	removeEmptyState() {
		this.removeClass( this.label, EMPTY_STATE );
	}

	setTouchedState() {
		this.addClass( this.background, TOUCHED_STATE );
	}

	manageValidState() {
		if (! this.select.hasAttribute( 'required' )) {
			return;
		}

		if (! this.isEmpty()) {
			this.addClass( this.background, VALID_STATE );
			this.removeClass( this.background, INVALID_STATE );
		}

		if (this.select.hasAttribute( 'required' ) && this.isEmpty()) {
			this.removeClass( this.background, VALID_STATE );
			this.addClass( this.background, INVALID_STATE );
		}
	}
}
