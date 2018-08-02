const gulp = require('gulp');

module.exports = function (config) {
    // Watch changes on files
    gulp.watch(config.sass, ['gutenberg:sass']);
    gulp.watch(config.scripts, ['gutenberg:lint', 'gutenberg:scripts']);
};
