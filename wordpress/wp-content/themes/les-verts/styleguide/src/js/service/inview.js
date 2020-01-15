class InView {
	constructor( element, callback, debounceDelayMS ) {
		this.element = element;
		this.callback = callback;
		this.debounceDelay = debounceDelayMS;
	}

	requestTick() {
		this.timer = this.timer || setTimeout( () => {
			this.timer = null;
			if (!this.ticking) {
				this.ticking = true;
				window.requestAnimationFrame( this.maybeTriggerAction.bind( this ) );
			}
		}, this.debounceDelay );
	}

	maybeTriggerAction() {
		if (this.inView()) {
			this.callback();
		}

		this.ticking = false;
	}

	inView() {
		const windowTop = window.pageYOffset;
		const windowBottom = windowTop + window.innerHeight;
		const imgRect = this.element.getBoundingClientRect();
		const imgTop = windowTop + imgRect.top;
		const imgBottom = imgTop + imgRect.height;
		return ( windowTop < imgBottom && windowBottom > imgTop );
	}
}

export default function( element, debounceDelayMS = 300 ) {
	let resolve;
	const promise = new Promise( res => resolve = res );
	const watcher = new InView( element, () => resolve(), debounceDelayMS );

	const handler = watcher.requestTick.bind( watcher );

	window.addEventListener( 'scroll', handler, false );
	window.addEventListener( 'resize', handler, false );

	watcher.requestTick();

	return promise.then( () => {
		window.removeEventListener( 'scroll', handler );
		window.removeEventListener( 'resize', handler );
	} );
}
