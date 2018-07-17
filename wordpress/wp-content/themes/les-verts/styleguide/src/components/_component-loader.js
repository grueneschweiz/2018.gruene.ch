// Utils
import elementExist from 'utils/elementExist';

/**
 *  Import here below you component
 *
 * Example: the path is relative from this file
 * 		import MyComponent from "organisms/my-component/my-component.js";
 */
import MMenu from "molecules/m-menu/m-menu.js";
import AInput from "atoms/form-elements/a-input/a-input.js";
import OHeader from "./organisms/o-header/o-header";
// ================= STOP TO ADD HERE

/**
 * Add your component in the COMPONENTS_TO_LOAD array with the tree properties "view", "name" & "selector"
 *
 * Example: where the selector is the DOM selector for querySelector
 * 		{ view: MyComponent, name: 'MyComponent', selector: '[data-my-component]' }
 */
const COMPONENTS_TO_LOAD = [
	// { view: MyComponent, name: 'MyComponent', selector: '[data-my-component]' }
    { view: MMenu, name: 'MMenu', selector: '.m-menu' },
    { view: AInput, name: 'AInput', selector: '.a-input' },
    { view: OHeader, name: 'OHeader', selector: '.o-header' }
];

const LOADED_COMPONENTS = {}

/**
 * Load all the components.
 * All the component are builded using the same interface, so...
 * the .bind method does the magic for everyone.
 */
export default function() {
	for (let i = 0; i < COMPONENTS_TO_LOAD.length; i++) {
		let { selector, view, name } = COMPONENTS_TO_LOAD[i];
		let element = document.querySelectorAll( selector );
		// Only if the DOM element exist
		// we load the view.
		if ( elementExist(element) ) {

			for (var j = 0; j < element.length; j++) {
				// Create the view instance
				// and push the view in the loaded view cache object.
				let v = new view(element[j]);
				v.bind();
				LOADED_COMPONENTS[name + j] = v;
			}
		}
	}

	// Exposes the loaded views for dev purposes
	// TODO remove after dev
	window.COMPONENTS = LOADED_COMPONENTS;

	return LOADED_COMPONENTS;
}
