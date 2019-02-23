/**
 * Check if the browser supports the class list property for this element.
 *
 * @param {Element} element
 * @return {Boolean}
 */
function browserSupportsClassList(element) {
	return ! ! element.classList;
}

export default browserSupportsClassList;



