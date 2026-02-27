import BaseView from 'base-view';

const STORAGE_KEY = 'trackingConsent';
const CONSENT_EVENT = 'trackingConsentChanged';

/**
 * Cookie Banner Component
 * Manages user consent for tracking scripts
 */
export default class OCookieBanner extends BaseView {

	initialize() {
		this.acceptButton = this.getScopedElement('[data-action="accept"]');
		this.declineButton = this.getScopedElement('[data-action="decline"]');
		this.closeButton = this.getScopedElement('.o-cookie-banner__close');

		// Check if consent already exists
		const consent = this.getConsent();

		if (consent === null) {
			// No consent stored, show banner
			this.show();
		} else {
			// Consent exists, load scripts if accepted
			if (consent.accepted) {
				// Try to load scripts immediately
				this.loadTrackingScripts();

				// Also try after DOM is fully loaded (in case window.trackingScripts is set later)
				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', () => {
						this.loadTrackingScripts();
					});
				}
			}
		}

		// Bind events
		this.on('click', '[data-action="accept"]', this.handleAccept.bind(this));
		this.on('click', '[data-action="decline"]', this.handleDecline.bind(this));
		this.on('click', '.o-cookie-banner__close', this.handleClose.bind(this));
	}

	/**
	 * Show the banner
	 */
	show() {
		this.addClass('o-cookie-banner--visible');
		this.element.setAttribute('aria-hidden', 'false');
	}

	/**
	 * Hide the banner
	 */
	hide() {
		this.removeClass('o-cookie-banner--visible');
		this.element.setAttribute('aria-hidden', 'true');
	}

	/**
	 * Handle accept button click
	 */
	handleAccept(e) {
		e.preventDefault();
		this.saveConsent(true);
		this.loadTrackingScripts();
		this.hide();
	}

	/**
	 * Handle decline button click
	 */
	handleDecline(e) {
		e.preventDefault();
		this.saveConsent(false);
		this.hide();
	}

	/**
	 * Handle close button click
	 */
	handleClose(e) {
		e.preventDefault();
		this.hide();
	}

	/**
	 * Show close button after consent decision
	 */
	showCloseButton() {
		if (this.closeButton) {
			this.closeButton.removeAttribute('hidden');
		}
	}

	/**
	 * Save consent to localStorage
	 * @param {boolean} accepted
	 */
	saveConsent(accepted) {
		const consent = {
			accepted: accepted,
			timestamp: Date.now()
		};

		try {
			localStorage.setItem(STORAGE_KEY, JSON.stringify(consent));
			this.trigger(CONSENT_EVENT, { detail: consent });
		} catch (e) {
			// Silent fail - consent not saved
		}
	}

	/**
	 * Get consent from localStorage
	 * @returns {Object|null}
	 */
	getConsent() {
		try {
			const stored = localStorage.getItem(STORAGE_KEY);
			return stored ? JSON.parse(stored) : null;
		} catch (e) {
			// Silent fail - return null
			return null;
		}
	}

	/**
	 * Load tracking scripts if consent was given
	 */
	loadTrackingScripts() {
		// Check if tracking scripts are defined
		if (typeof window.trackingScripts === 'undefined') {
			return;
		}

		const scripts = window.trackingScripts;

		// Load Facebook Pixel
		if (scripts.facebookPixel && scripts.facebookPixel.id) {
			this.loadFacebookPixel(scripts.facebookPixel.id);
		}

		// Load custom scripts
		Object.keys(scripts).forEach(key => {
			if (key !== 'facebookPixel' && scripts[key].type === 'external') {
				this.loadExternalScript(scripts[key].url, key);
			}
		});
	}

	/**
	 * Load Facebook Pixel
	 * @param {string} pixelId
	 */
	loadFacebookPixel(pixelId) {
		// Check if already loaded or initialized
		if (window.fbq || window._fbPixelInitialized) {
			return;
		}

		// Mark as initialized to prevent duplicate loading
		window._fbPixelInitialized = true;

		// Facebook Pixel Code
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');

		window.fbq('init', pixelId);
		window.fbq('track', 'PageView');
	}

	/**
	 * Load external script
	 * @param {string} url
	 * @param {string} key
	 */
	loadExternalScript(url, key) {
		// Check if already loaded
		const existingScript = document.querySelector(`script[data-tracking-script="${key}"]`);
		if (existingScript) {
			return;
		}

		const script = document.createElement('script');
		script.src = url;
		script.async = true;
		script.setAttribute('data-tracking-script', key);

		script.onload = () => {
			// Script loaded successfully
		};

		script.onerror = () => {
			// Script failed to load
		};

		document.head.appendChild(script);
	}

	/**
	 * Public API: Check if user has consented
	 * @returns {boolean}
	 */
	static hasConsent() {
		try {
			const stored = localStorage.getItem(STORAGE_KEY);
			if (!stored) return false;
			const consent = JSON.parse(stored);
			return consent.accepted === true;
		} catch (e) {
			return false;
		}
	}

	/**
	 * Public API: Reset consent (for testing)
	 */
	static resetConsent() {
		try {
			localStorage.removeItem(STORAGE_KEY);
			// Consent reset - reload page to see banner
		} catch (e) {
			// Failed to reset consent
		}
	}
}

// Expose public API
window.CookieBanner = {
	hasConsent: OCookieBanner.hasConsent,
	resetConsent: OCookieBanner.resetConsent
};
