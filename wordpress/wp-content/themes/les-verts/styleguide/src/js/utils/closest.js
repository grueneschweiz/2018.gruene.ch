/**
 * Element.closest polyfill IE9+
 * @see https://developer.mozilla.org/fr/docs/Web/API/Element/closest#Polyfill
 */
if (! Element.prototype.matches) {
	Element.prototype.matches = Element.prototype.msMatchesSelector ||
		Element.prototype.webkitMatchesSelector;
}

if (! Element.prototype.closest) {
	Element.prototype.closest = function( s ) {
		var el = this;
		if (! document.documentElement.contains( el )) {
			return null;
		}
		do {
			if (el.matches( s )) {
				return el;
			}
			el = el.parentElement || el.parentNode;
		} while (el && el.nodeType == 1 && el !== null);
		return null;
	};
}
