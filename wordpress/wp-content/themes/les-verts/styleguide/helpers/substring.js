/**
  * Return substring constructed from 1st character to given index
  * if no index passed, defaults to only first letter of word
  */
 module.exports = function( passedString, index = 1 ) {
  let substring = passedString.substring(0, index);
  return substring;
};
