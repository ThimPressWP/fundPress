var gulp = require('gulp');
var sass = require('gulp-sass');
var cleanCSS = require('gulp-clean-css');

// Compile Sass and Watch Files For Changes
gulp.task('sass', function () {

    gulp.src('assets/sass/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css/')); //
    gulp.watch('assets/sass/*.scss', ['sass']);

});