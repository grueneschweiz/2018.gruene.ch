import BaseView from "base-view";

const INPUT_SELECTOR = ".a-input__field";
const LABEL_SELECTOR = ".a-input__label";
const EMPTY_STATE = 'is-empty';
const TOUCHED_STATE = 'is-touched';

export default class ACheckbox extends BaseView {
    initialize() {
        this.input = this.getScopedElement(INPUT_SELECTOR);
        this.label = this.getScopedElement(LABEL_SELECTOR);

        this.manageEmptyState();
    }

    bind() {
        super.bind();

        this.on("keyup", INPUT_SELECTOR, () => this.setStateClasses());
        this.on("focus", INPUT_SELECTOR, () => this.removeEmptyState());
        this.on("blur", INPUT_SELECTOR, () => this.manageEmptyState());
    }

    setStateClasses() {
        this.addClass(this.input, TOUCHED_STATE);
        this.off("keyup");
    }

    manageEmptyState() {
        if (this.isEmpty()) {
            this.removeEmptyState();
        } else {
            this.setEmptyState();
        }
    }

    setEmptyState() {
        this.addClass(this.label, EMPTY_STATE);
    }

    removeEmptyState() {
        this.removeClass(this.label, EMPTY_STATE);
    }

    isEmpty() {
        return this.input.value;
    }
}