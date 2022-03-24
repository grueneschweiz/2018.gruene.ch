import BaseView from 'base-view';
import inView from '../../../js/service/inview';
import ajax from '../../../js/service/ajax';

const BAR_SELECTOR = '.a-progress__bar';
const VALUE_SELECTOR = '.a-progress__value';
const LEGEND_SELECTOR = '.a-progress__legend';
const LEGEND_VALUE_SELECTOR = '.a-progress__legend-value';

const LOADING_CLASS = 'a-progress--loading';

const DEBOUNCE_DELAY_MS = 300;
const STEPS = 200;
const STEP_DELAY = 25; // ms

// sync with m-form.js
const SUBMISSION_NOTIFICATION_EVENT = 'supt_form_submission';

export default class AProgress extends BaseView {
	bind() {
		super.bind();

		this.firstRun = true;
		this.bar = this.getScopedElement( BAR_SELECTOR );

		this.listenForSubmissions();

		this.updateData().finally( () => {
			inView( this.element, DEBOUNCE_DELAY_MS ).
				then( () => this.startAnimation() );
		} );
	}

	updateData() {
		if (!( 'url' in this.bar.dataset )) {
			return new Promise( resolve => resolve() );
		}

		const url = this.bar.dataset.url;
		return ajax( url, 'GET' ).then( ( resp ) => {
			if (resp instanceof Object
				&& 'current' in resp
				&& 'goal' in resp
				&& 'legend' in resp
			) {
				this.bar.setAttribute( 'aria-valuenow', resp.current );
				this.bar.setAttribute( 'aria-valuemax', resp.goal );
				this.getScopedElement( LEGEND_SELECTOR ).innerHTML = resp.legend;
			}
		} );
	}

	startAnimation() {
		this.value = this.getScopedElement( VALUE_SELECTOR );
		this.legend_value = this.getScopedElement( LEGEND_VALUE_SELECTOR );

		this.min = this.bar.getAttribute( 'aria-valuemin' );
		this.max = this.bar.getAttribute( 'aria-valuemax' );
		this.current = this.bar.getAttribute( 'aria-valuenow' );

		if (this.firstRun) {
			this.step_count = 0;
			this.state = 0;
			this.state_percent = 0;
		}

		this.sumOfSteps = 1 / 2 * STEPS * ( STEPS + 1 );
		this.spread = this.current - this.min;

		this.timer = setInterval( this.animate.bind( this ), STEP_DELAY );
	}

	addSubmission() {
		this.updateData().then( this.startAnimation.bind( this ) );
	}

	animate() {
		if (this.firstRun) {
			this.removeClass( this.element, LOADING_CLASS );
			this.firstRun = false;
		}

		if (this.step_count > STEPS) {
			clearInterval( this.timer );

			// we need this extra step to circumvent float calculation errors
			const current = ( this.spread / ( this.max - this.min ) );
			this.bar.style.width = current * 100 + '%';
			this.setLabelValue( this.current );

		}
		else {

			this.step_count ++;

			const easing = ( STEPS - this.step_count ) / this.sumOfSteps;
			const step_size = this.spread * easing;
			const step_percent = step_size / ( this.max - this.min );

			this.state += step_size;
			this.state_percent += step_percent;

			this.bar.style.width = this.state_percent * 100 + '%';
			this.setLabelValue( Math.round( this.state ) );
		}
	}

	setLabelValue( value ) {
		if (this.legend_value) {
			this.legend_value.innerText = value;
		}

		this.value.innerText = value;
	}

	listenForSubmissions() {
		document.addEventListener( SUBMISSION_NOTIFICATION_EVENT, event => {
			if (!( 'form' in this.bar.dataset )) {
				return;
			}

			const progressFormId = parseInt( this.bar.dataset.form );
			const eventFormId = parseInt( event.detail.formId );

			if (progressFormId === eventFormId) {
				this.addSubmission();
			}
		} );
	}

	destroy() {
		window.removeEventListener( 'scroll', this.eventHandler );
		window.removeEventListener( 'resize', this.eventHandler );

		super.destroy();
	}
}
