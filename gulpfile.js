/**
 * Run:
 * npm install
 *
 * gulp release
 */
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    cleanCSS = require('gulp-clean-css'),
    clean = require('gulp-clean'),
    gulpCopy = require('gulp-copy'),
    readFile = require('read-file'),
    replace = require('gulp-replace'),
    mkdirp = require("mkdirp"),
    argv = require('yargs').argv,
    zip = require('gulp-zip'),
    rename = require('gulp-rename');
// Compile Sass and Watch Files For Changes
gulp.task('sass', function () {

    gulp.src('assets/sass/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css/')); //
    gulp.watch('assets/sass/*.scss', ['sass']);

});

// Pack
var pluginName = 'fundpress';
var plgVer = '';
var getVer = function (file) {
    var ver = readFile.sync(file, {encoding: 'utf8'}).match(/Version:\s*(.*)/);
    ver = ver ? ver[1] : 'unversioned';

    return ver;
};

gulp
    .task('clr', function () {
        return gulp.src('releases/' + pluginName + '/', {read: false}).pipe(clean({force: true}));
    });

gulp.task('copy', ['clr'], function () {
    var s = 'releases/' + pluginName + '/' + pluginName;
    var files = [
        'assets/**/*',
        'inc/**/*',
        'languages/**/*',
        'templates/**/*',
        'fundpress.php',
        'license.txt',
        'readme.txt',
    ];
    return gulp.src(files).pipe(gulpCopy(s));
});

gulp.task('zip', ['copy'], function () {
    return gulp.src('releases/' + pluginName + '/**/*')
        .pipe(zip(pluginName + '.zip'))
        .pipe(gulp.dest('./releases/'));


});

gulp.task('get-ver', ['zip'], function () {
    plgVer = argv.v;
    if (!plgVer) {
        plgVer = getVer(pluginName + '.php');
    }
    return plgVer;
});

gulp.task('rename', ['get-ver'], function () {
    return gulp.src('releases/' + pluginName + '.zip')
        .pipe(rename('releases/' + pluginName + '.' + plgVer + '.zip'))
        .pipe(gulp.dest(''));
});

gulp.task('clean', ['rename'], function () {
    //console.log('releases/' + plgPath + '/')
    //gulp.src('releases/' + pluginName + '/', {read: false}).pipe(clean({force: true}));
    gulp.src('releases/' + pluginName + '.zip', {read: false}).pipe(clean({force: true}));
})

gulp.task('release', ['clean'], function () {


});