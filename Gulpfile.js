// Main Gulp logic
const gulp = require('gulp');
const zip = require('gulp-zip');

gulp.task('package', () => {
	folders = ['plg_system_webcomponents'];

	gulp.src('plg_system_webcomponents/**')
		.pipe(zip('plg_system_webcomponents.zip'))
		.pipe(gulp.dest('dist'));
});

gulp.task('default', ['package']);
