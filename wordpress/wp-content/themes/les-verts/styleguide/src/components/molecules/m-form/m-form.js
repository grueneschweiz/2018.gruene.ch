import BaseView from 'base-view';

const SUBMIT_BUTTON_SELECTOR = '[data-form-submit]';
const SUBMIT_WRAPPER_SELECTOR = '.m-form__submit-wrapper';
const SUCESS_MESSAGE_SELECTOR = '.m-form__message--success';
const ERROR_MESSAGE_SELECTOR = '.m-form__message--failure';

const HIDDEN_STATE = 'is-hidden';
const SHOWN_STATE = 'is-shown';

export default class MForm extends BaseView {
	initialize() {
		this.submitButton = this.getScopedElement( SUBMIT_BUTTON_SELECTOR );

		this.submitted = false;
		this.sendingTimer = null;
	}

	bind() {
		super.bind();

		/* @noinspection JSCheckFunctionSignaturesInspection */
		this.on( 'submit', e => this.submit( e ) );
	}

	submit( event ) {
		event.preventDefault();

		// hide error message
		let errorMessage = this.getScopedElement( ERROR_MESSAGE_SELECTOR );
		this.removeClass( errorMessage, SHOWN_STATE );

		// prevent double submit
		if (this.submitted) {
			return;
		}
		this.submitButton.disabled = true;
		this.submitted = true;

		// mark as sending
		let origSubmitLbl = this.submitButton.innerHTML;
		this.showSending();

		let url = this.element.action;
		let data = new FormData( this.element );

		// inject wordpress action
		data.append( 'action', 'supt_form_submit' );

		this.ajax( url, 'POST', data )
			.then( resp => {
				// todo: handle server validation
				let submitWrapper = this.getScopedElement( SUBMIT_WRAPPER_SELECTOR );
				let successMessage = this.getScopedElement( SUCESS_MESSAGE_SELECTOR );
				this.addClass( submitWrapper, HIDDEN_STATE );
				this.addClass( successMessage, SHOWN_STATE );
			} )
			.catch( () => {
				clearInterval( this.sendingTimer );
				this.submitButton.disabled = false;
				this.submitted = false;
				this.submitButton.innerHTML = origSubmitLbl;
				let errorMessage = this.getScopedElement( ERROR_MESSAGE_SELECTOR );
				this.addClass( errorMessage, SHOWN_STATE );
			} );
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
					if (xhr.status === 200) {
						let resp = xhr.responseText;
						let respJson = JSON.parse( resp );
						resolve( respJson );
					} else {
						reject( xhr.status );
					}
				}
			};
		} );
	}
}
