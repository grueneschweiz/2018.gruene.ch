// Utils
import elementExist from 'utils/elementExist';

/**
 *  Import here below you component
 *
 * Example: the path is relative from this file
 *    import MyComponent from "organisms/my-component/my-component.js";
 */
import MMenu from 'molecules/m-menu/m-menu.js';
import AInput from 'atoms/form-elements/a-input/a-input.js';
import ASelect from 'atoms/form-elements/a-select/a-select.js';
import OHeader from './organisms/o-header/o-header';
import OCampaign from './organisms/o-campaign/o-campaign';
import OPeopleGrid from './organisms/o-people-grid/o-people-grid';
import OEngagementMap from './organisms/o-engagement-map/o-engagement-map';
import MShare from './molecules/m-share/m-share';
import ASearch from './atoms/a-search/a-search';
import MPerson from './molecules/m-person/m-person';
import AImage from './atoms/a-image/a-image';

// ================= STOP TO ADD HERE

/**
 * Add your component in the COMPONENTS_TO_LOAD array with the tree properties "view", "name" & "selector"
 *
 * Example: where the selector is the DOM selector for querySelector
 *    { view: MyComponent, name: 'MyComponent', selector: '[data-my-component]' }
 */
const COMPONENTS_TO_LOAD = [
	{ view: MMenu, name: 'MMenu', selector: '.m-menu' }, // load as very first (performance)
	{ view: OHeader, name: 'OHeader', selector: '.o-header' }, // load second (performance)
	{ view: ASearch, name: 'ASearch', selector: '.a-search'},
	{ view: OCampaign, name: 'OCampaign', selector: '.o-campaign' },
	{ view: AInput, name: 'AInput', selector: '.a-input' },
	{ view: ASelect, name: 'ASelect', selector: '.a-select' },
	{ view: OPeopleGrid, name: 'OPeopleGrid', selector: '.o-people-grid--horizontal' },
	{ view: OEngagementMap, name: 'OEngagementMap', selector: '.o-engagement-map' },
	{ view: MShare, name: 'MShare', selector: '.m-share' },
	{ view: MPerson, name: 'MPerson', selector: '.m-person--expandable' },
	{ view: AImage, name: 'AImage', selector: '.a-image__image--lazy' },
];

const LOADED_COMPONENTS = {};

/**
 * Load all the components.
 * All the component are builded using the same interface, so...
 * the .bind method does the magic for everyone.
 */
export default function() {
	for (let i = 0; i < COMPONENTS_TO_LOAD.length; i ++) {
		let { selector, view, name } = COMPONENTS_TO_LOAD[ i ];
		let element = document.querySelectorAll( selector );
		// Only if the DOM element exist
		// we load the view.
		if (elementExist( element )) {

			for (let j = 0; j < element.length; j ++) {
				// Create the view instance
				// and push the view in the loaded view cache object.
				let v = new view( element[ j ] );
				v.setComponentsReference( LOADED_COMPONENTS );
				LOADED_COMPONENTS[ name + j ] = v;
			}
		}
	}

	Object.entries( LOADED_COMPONENTS ).forEach( ( [ , component ] ) => {
		component.bind();
	} );

	// Exposes the loaded views for dev purposes
	// TODO remove after dev
	window.COMPONENTS = LOADED_COMPONENTS;

	return LOADED_COMPONENTS;
}
