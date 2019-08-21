// Polyfills
import 'utils/polyfills';
// Helpers
import 'utils/classList';
// Components loader
import load_components from './components/_component-loader';

// Load all the components
load_components();

// Fix top offset for anchor links (fixed top nav)
require('service/anchor.js');
