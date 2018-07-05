import isFunction from 'lodash.isfunction';
import isEmpty from 'lodash.isempty';
import isBoolean from 'lodash.isboolean';
import delegate from 'delegate';
import pullAt from 'lodash.pullat';



/**
 * Generate an incremental
 * view id.
 */
let VIEW_ID = 0;
function generateViewId() {
	return ++VIEW_ID;
}

/**
 * Parse the array to retrieve the strings and an optional Element.
 * @param array args Array containing the arguments of a function
 * @return object
 */
function parseClassArguments( args ) {
	let el = this.element;
	const classes = [];

	Array.prototype.forEach.call(args, ( arg => {
		if ( typeof arg == "string" ) {
			classes.push( arg );
		}
		else if ( arg.nodeType == 1) {
			el = arg;
		}
	}) );

	return {
		el,
		classes,
	};
}

/**
 * Base view class.
 * All the components should extend this class.
 *
 * @class BaseView
 */
export default class BaseView {

	/**
	 * Constructor.
	 */
	constructor(element) {
		this.element	= element;
		this.channel	= document;
		this.vid		= generateViewId();
		this.events		= {
			global:	{},
			element:	{}
		};
	}

	/**
	 * Default initialize method.
	 */
	initialize() {}

	/**
	 * Return the events attached to the element & channel.
	 *
	 * @return {Object}
	 */
	getEvents() {
		return this.events;
	}

	/**
	 * Main bind function.
	 * Trigger all the code on the view.
	 */
	bind() {
		// this.delegateEvents();
		this.initialize();
	}

	/**
	 * Get a scoped element.
	 *
	 * @param {String} selector
	 * @return {Element}
	 */
	getScopedElement(selector) {
		return this.element.querySelector(selector);
	}

	/**
	 * Get a scoped collection of elements.
	 *
	 * @param {String} selector
	 * @return {elementList}			A non-live NodeList of element objects.
	 */
	getScopedElements(selector) {
		return this.element.querySelectorAll(selector);
	}

	/**
	 * Is desktop?
	 */
	isDesktop() {
		if (window && window.innerWidth) {
			return (window.innerWidth >= 1024);
		}
		return true;
	}

	/**
	 * Trigger a global event.
	 *
	 * @param {String} event_name
	 * @param {Object} params
	 */
	trigger(event_name, params = null) {
		this.channel.dispatchEvent( new CustomEvent(event_name, params) );
	}

	/**
	 * Bind a callback to an element event or global event.
	 *
	 * @param {String}		event_name
	 * @param {String}		selector		Optional.
	 * @param {Function}	handler
	 * @param {Boolean}		channel			Optional. Default: false. Whether or not to bind a global event.
	 */
	on(event_name, selector, handler, channel = false) {

		// Handle the optional selector argument
		if ( isFunction(selector) ) {
			channel = handler || false;
			handler = selector;
			selector = null;
		}

		if ( channel ) {
			if ( !(event_name in this.events.global) ) {
				this.events.global[event_name] = [];
			}

			this.channel.addEventListener( event_name, handler );
			this.events.global[event_name].push(handler);
		}
		else {
			if ( !(event_name in this.events.element) ) {
				this.events.element[event_name] = [];
			}

			if ( isEmpty(selector) ) {
				this.element.addEventListener( event_name, handler );
				this.events.element[event_name].push({
					selector: 'self',
					handler	: handler,
				});
			}
			else {
				this.events.element[event_name].push({
					selector: selector,
					handler	: handler,
					delegate: delegate( this.element, selector, event_name, (e) => {
						e.stopPropagation();
						handler(e);
					}, true )
				});

			}
		}
	}

	/**
	 * Unbind the given element event or global event.
	 *
	 * @param {String}		event_name
	 * @param {String}		selector		Optional.
	 * @param {Boolean}		channel			Optional. Default: false. Whether or not to unbind a global event.
	 */
	off( event_name, selector = null, channel = false) {

		// Handle the optional selector argument
		if ( isBoolean(selector) ) {
			channel = selector;
			selector = null;
		}

		if ( channel ) {
			if ( event_name in this.events.global ) {
				for (let i = 0; i < this.events.global[event_name].length; i++) {
					this.channel.removeEventListener( event_name, this.events.global[event_name][i] );
				}
			}
			delete this.events.global[event_name];
		}
		else if ( event_name in this.events.element ) {

			if ( isEmpty(selector) ) {
				// Events binded to the element
				for (let i = this.events.element[event_name].length-1; i >= 0; i--) {
					if ( 'self' == this.events.element[event_name][i].selector ) {
						this.element.removeEventListener( event_name, this.events.element[event_name][i].handler );
					}
					else {
						this.events.element[event_name][i].delegate.destroy();
					}
				}
				delete this.events.element[event_name];
			}
			else {
				// unbind delegates events
				for (let i = this.events.element[event_name].length-1; i >= 0; i--) {
					if ( selector == this.events.element[event_name][i].selector ) {
						this.events.element[event_name][i].delegate.destroy();
						pullAt(this.events.element[event_name], i);
					}
				}
			}
		}
	}

	/**
	 * Helper to easily add classes in a single line which will then add one per class (IE10 compatible)
	 *
	 * @param {Element}		el		Optional. Default: current View element. The target element to add classes.
	 * @param {String}		cls		Class name separated by a comma or space.
	 */
	addClass() {
		const { el, classes } = parseClassArguments.call( this, arguments );
		classes.forEach( (c) => { el.classList.add(c); } );
	}

	/**
	 * Helper to easily remove classes in a single line which will then remove one per class (IE10 compatible)
	 *
	 * @param {Element}  el     Optional. Default: current View element. The target element to remove classes.
	 * @param {String}   class  Class name. Multiple classes can be passed as extra args.
	 */
	removeClass() {
		const { el, classes } = parseClassArguments.call( this, arguments );
		classes.forEach( (c) => { el.classList.remove(c); } );
	}

	/**
	 * Off all events (element & global).
	 *
	 * @global viewport_service
	 */
	destroy() {
		for (let event_name in this.events.global) {
			this.off( event_name, null, null, true );
		}
		for (let event_name in this.events.element) {
			this.off( event_name );
		}

		// Remove listeners attached to the viewport service
		viewport_service.removeAllListeners();
	}

}
