// console.log('JavaScript is awesome at superhuit!');

// Polyfills
import 'utils/closest';
import objectFitImages from 'object-fit-images';

// Components loader
import load_components from './components/_component-loader';

// object-fit polyfill for IE
objectFitImages();

// Load all the components
load_components();
