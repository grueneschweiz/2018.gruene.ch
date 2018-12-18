import BaseView from 'base-view';

const SUBMIT_BUTTON_SELECTOR = '[data-form-submit]';
const SUBMIT_WRAPPER_SELECTOR = '.m-form__submit-wrapper';
const SUCCESS_MESSAGE_SELECTOR = '.m-form__message--success';
const ERROR_MESSAGE_SELECTOR = '.m-form__message--failure';
const INVALID_MESSAGE_SELECTOR = '.m-form__message--invalid';

const HIDDEN_STATE = 'is-hidden';
const SHOWN_STATE = 'is-shown';
const INVALID_STATE = 'is-invalid';

export default class MForm extends BaseView {
	initialize() {
		this.submitButton = this.getScopedElement( SUBMIT_BUTTON_SELECTOR );

		this.submitted = false;
		this.sendingTimer = null;
		this.origSubmitLbl = this.submitButton.innerHTML;
	}

	bind() {
		super.bind();

		/* @noinspection JSCheckFunctionSignaturesInspection */
		this.on( 'submit', e => this.submit( e ) );
	}

	submit( event ) {
		event.preventDefault();

		// hide error messages
		let errorMessage = this.getScopedElement( ERROR_MESSAGE_SELECTOR );
		let invalidMessage = this.getScopedElement( INVALID_MESSAGE_SELECTOR );
		this.removeClass( errorMessage, SHOWN_STATE );
		this.removeClass( invalidMessage, SHOWN_STATE );

		// prevent double submit
		if (this.submitted) {
			return;
		}
		this.submitButton.disabled = true;
		this.submitted = true;

		// mark as sending
		this.showSending();

		let url = this.element.action;
		let data = new FormData( this.element );

		/**
		 * append the following data in JS, so we have a first spam barrier
		 */
		// add wordpress action
		data.append( 'action', 'supt_form_submit' );

		// add nonce
		data.append( 'nonce', this.element.dataset.nonce );

		// add form id
		data.append( 'form_id', this.element.dataset.formId );

		// add action id (engagement funnel plugin)
		if (this.element.dataset.actionId) {
			data.append( 'action_id', this.element.dataset.actionId );
		}

		// add config id (engagement funnel plugin)
		if (this.element.dataset.configId) {
			data.append( 'config_id', this.element.dataset.configId );
		}

		this.ajax( url, 'POST', data )
			.then( ( resp ) => {
				if (resp instanceof Object && 'success' in resp && true === resp.success) {
					this.showSuccess( resp.data );
				} else {
					return Promise.reject( resp );
				}
			} ).catch( ( resp ) => {
			this.handleError( resp );
		} );
	}

	handleError( resp ) {
		if (resp instanceof Object && 'data' in resp) {
			for (const key of Object.keys( resp.data )) {
				let el = this.getScopedElement( '[name=' + key + ']' );
				this.addClass( el, INVALID_STATE );
			}

			let invalidMessage = this.getScopedElement( INVALID_MESSAGE_SELECTOR );
			this.addClass( invalidMessage, SHOWN_STATE );

		} else {

			let errorMessage = this.getScopedElement( ERROR_MESSAGE_SELECTOR );
			this.addClass( errorMessage, SHOWN_STATE );
		}

		clearInterval( this.sendingTimer );
		this.submitButton.disabled = false;
		this.submitted = false;
		this.submitButton.innerHTML = this.origSubmitLbl;
	}

	showSuccess( data ) {
		if (- 1 === data.next_action_id || ! data.html) {
			let submitWrapper = this.getScopedElement( SUBMIT_WRAPPER_SELECTOR );
			let successMessage = this.getScopedElement( SUCCESS_MESSAGE_SELECTOR );
			this.addClass( submitWrapper, HIDDEN_STATE );
			this.addClass( successMessage, SHOWN_STATE );
		} else {
			this.element.innerHTML = data.html;
		}
	}

	showSending() {
		let lbl = this.element.dataset.sendingLbl;
		let counter = 0;
		this.sendingTimer = setInterval( () => {
			counter ++;
			counter = counter <= 3 ? counter : 0;

			let lblAdd = '';
			for (let i = 0; i < counter; i ++) {
				lblAdd += '.';
			}

			this.submitButton.innerHTML = lbl + '<span class="m-form__sending">' + lblAdd + '</span>';
		}, 300 );
	}

	ajax( url, method, data = null ) {
		return new Promise( function( resolve, reject ) {
			let xhr = new XMLHttpRequest();
			xhr.open( method, url );
			xhr.send( data );
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4) {
					let raw = xhr.responseText;
					let resp = '';
					try {
						resp = JSON.parse( raw );
					} catch (err) {
						resp = {};
					}

					if (xhr.status === 200) {
						resolve( resp );
					} else {
						reject( resp );
					}
				}
			};
		} );
	}
}
