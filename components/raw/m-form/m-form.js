import BaseView from 'base-view';

const SUBMIT_BUTTON_SELECTOR = '[data-form-submit]';
const SUBMIT_WRAPPER_SELECTOR = '.m-form__submit-wrapper';
const SUCCESS_MESSAGE_SELECTOR = '.m-form__message--success';
const ERROR_MESSAGE_SELECTOR = '.m-form__message--failure';
const INVALID_MESSAGE_SELECTOR = '.m-form__message--invalid';
const SERVER_FEEDBACK_MESSAGE_SELECTOR = '.m-form__message-error';
const FORM_SELECTOR = '.m-form';

const HIDDEN_STATE = 'is-hidden';
const SHOWN_STATE = 'is-shown';
const INVALID_STATE = 'is-invalid';

const LAST_SUBMISSION = 'pred';

export default class MForm extends BaseView {
	static getUrlParam( param, defaultValue ) {
		const url = new URL( window.location.href );
		const value = url.searchParams.get( param );
		if (value) {
			return value;
		}

		return defaultValue;
	}

	bind() {
		super.bind();

		/* @noinspection JSCheckFunctionSignaturesInspection */
		this.on( 'submit', e => this.submit( e ) );
	}

	static buildRedirectUrl( url, formId ) {
		url = new URL( url );
		url.searchParams.set( LAST_SUBMISSION, formId );
		return url.toString();
	}

	handleError( resp ) {
		if (resp instanceof Object && 'data' in resp) {
			if ('nonce' in resp.data) {
				this.element.dataset.nonce = resp.data.nonce;
			}

			if ('general' in resp.data) {
				this.showErrorMessage( resp.data.general );
			}
			else if ('validation' in resp.data) {
				this.showInvalidMessage( resp.data.validation );
			}
			else {
				this.showErrorMessage( '' );
			}
		}
		else {
			this.showErrorMessage( '' );
		}
	}

	clearSendingState() {
		clearInterval( this.sendingTimer );
		this.submitButton.disabled = false;
		this.submitted = false;
		this.submitButton.innerHTML = this.origSubmitLbl;
	}

	showInvalidMessage( messages ) {
		let invalidMessage = this.getScopedElement( INVALID_MESSAGE_SELECTOR );
		let serverMessage = invalidMessage.querySelector(
			SERVER_FEEDBACK_MESSAGE_SELECTOR );
		let message = '';

		for (const key of Object.keys( messages )) {
			let el = this.getScopedElement( '[name=' + key + ']' );
			this.addClass( el, INVALID_STATE );
			message += `<li>${ messages[ key ] }</li>`;
		}

		serverMessage.innerHTML = `<ul>${ message }</ul>`;
		this.addClass( invalidMessage, SHOWN_STATE );
		invalidMessage.setAttribute( 'aria-hidden', 'false' );
	}

	showErrorMessage( message ) {
		let errorMessage = this.getScopedElement( ERROR_MESSAGE_SELECTOR );
		let serverMessage = errorMessage.querySelector(
			SERVER_FEEDBACK_MESSAGE_SELECTOR );

		serverMessage.innerHTML = `<ul><li>${ message }</li></ul>`;
		this.addClass( errorMessage, SHOWN_STATE );
		errorMessage.setAttribute( 'aria-hidden', 'false' );
	}

	initialize() {
		this.submitButton = this.getScopedElement( SUBMIT_BUTTON_SELECTOR );

		this.submitted = false;
		this.sendingTimer = null;
		this.origSubmitLbl = this.submitButton.innerHTML;
		this.predecessorId = MForm.getUrlParam( LAST_SUBMISSION, - 1 );
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

			this.submitButton.innerHTML = lbl + '<span class="m-form__sending">' +
				lblAdd + '</span>';
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
					let resp;
					try {
						resp = JSON.parse( raw );
					}
					catch ( err ) {
						resp = {};
					}

					if (xhr.status === 200) {
						resolve( resp );
					}
					else {
						reject( resp );
					}
				}
			};
		} );
	}

	submit( event ) {
		event.preventDefault();

		// hide error messages
		let errorMessage = this.getScopedElement( ERROR_MESSAGE_SELECTOR );
		let invalidMessage = this.getScopedElement( INVALID_MESSAGE_SELECTOR );
		errorMessage.setAttribute( 'aria-hidden', 'true' );
		invalidMessage.setAttribute( 'aria-hidden', 'true' );
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

		// the form data
		const data = this.getFormData();
		const url = this.element.action;

		// get a nonce, then submit the form
		this.getNonce().
			then( nonce => data.append( 'nonce', nonce ) ).
			then( () => this.sendForm( url, data ) ).
			then( resp => this.showSuccess( resp ) ).
			catch( resp => this.handleError( resp ) ).
			finally( this.clearSendingState );
	}

	sendForm( url, data ) {
		return this.ajax( url, 'POST', data ).then( ( resp ) => {
			if (resp instanceof Object
				&& 'success' in resp
				&& true === resp.success) {
				return Promise.resolve( resp.data );
			}
			else {
				return Promise.reject( resp );
			}
		} );
	}

	getFormData() {
		let data = new FormData( this.element );

		/**
		 * append the following data in JS, so we have a first spam barrier
		 */
		// add wordpress action
		data.append( 'action', 'supt_form_submit' );

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

		// add the id of the last form
		data.append( 'predecessor_id', this.predecessorId );

		return data;
	}

	showSuccess( data ) {
		if (- 1 === data.next_action_id || !data.html) {
			if (data.redirect && - 1 === data.next_action_id) {
				window.location.href = MForm.buildRedirectUrl(
					data.redirect,
					data.predecessor_id
				);
			}
			else {
				this.predecessorId = data.predecessor_id;
				let submitWrapper = this.getScopedElement( SUBMIT_WRAPPER_SELECTOR );
				let successMessage = this.getScopedElement( SUCCESS_MESSAGE_SELECTOR );
				submitWrapper.setAttribute( 'aria-hidden', 'true' );
				successMessage.setAttribute( 'aria-hidden', 'false' );
				this.addClass( submitWrapper, HIDDEN_STATE );
				this.addClass( successMessage, SHOWN_STATE );
			}
		}
		else {
			let parent = this.element.parentNode.parentNode;
			parent.innerHTML = data.html;

			let form = new MForm( parent.querySelector( FORM_SELECTOR ) );
			form.bind();

			this.destroy();
		}
	}

	getNonce() {
		return this.ajax( this.element.dataset.nonce, 'GET' );
	}
}
