/**
 * Determines if the page should be displayed distraction free.
 *
 * Returns true if the page satisfies any of the following criteria:
 * - the url query string `focus` is present
 * - the referrer is not the current site
 *
 * Therefore visitors following internal links do not see the
 * distraction free page, unless the `focus` query string was set.
 *
 * @return boolean
 */
const distractionFree = function() {
	let url, referrer;

	try {
		url = new URL( window.location );
	}
	catch ( e ) {
		return true;
	}

	if (url.searchParams.get( 'focus' ) !== null) {
		return true;
	}

	try {
		referrer = new URL( document.referrer );
	}
	catch ( e ) {
		return true;
	}

	return url.hostname !== referrer.hostname;
};

export default distractionFree;
