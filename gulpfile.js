/* 
 * Install Nodejs, npm
 * 'npm install --global gulp-cli'
 * 'npm init' - generate package.json
 * 'npm install --save-dev gulp' - install gulp in project
 * 'npm install --save-dev gulp-sass gulp-concat gulp-uglify gulp-save gulp-rename gulp-sourcemaps' - install gulp addons
 * 'npm install'
 * 'gulp'
 */

var gulp = require( 'gulp' ),
        concat = require( 'gulp-concat' ),
        sass = require( 'gulp-sass' ),
        uglify = require( 'gulp-uglify' ),
        rename = require( 'gulp-rename' ),
        sourcemaps = require( 'gulp-sourcemaps' ),
        save = require( 'gulp-save' );

/**
 * Compile sass files
 * @param {type} param1
 * @param {type} param2
 */
gulp.task( 'sass', function(){
    console.log( 'Sass is running' );
    return gulp.src( [ 'assets/css/*/*.scss', 'assets/css/*/*.sass' ] )
            .pipe( sourcemaps.init() )
            .pipe( sass().on('error', sass.logError) )
            .pipe( sourcemaps.write() )
            .pipe( gulp.dest( function( file ){
                return file.base;
            } ) );
} );

/**
 * Minify css files
 */
gulp.task( 'styles', function(){
    console.log( 'Styles is running' );
   return gulp.src( [ 'assets/css/*/*.scss'] )
            .pipe( sourcemaps.init() )
            .pipe( sass( { outputStyle: 'compressed' } ).on( 'error', sass.logError ) )
            .pipe( sourcemaps.write() )
            .pipe( rename( { suffix: '.min' } ) )
            .pipe( gulp.dest( function( file ){
                return file.base;
            } ) );
});

/**
 * Minify javascript files
 */
gulp.task( 'scripts', function(){
    console.log( 'Scripts is running' );
    return gulp.src( [ 'assets/js/*/*.js', '!assets/js/*/*.min.js' ] )
            .pipe( rename( { suffix: '.min' } ) )
            .pipe( uglify() )
            .pipe( gulp.dest( function( file ){
                return file.base;
            } ) );
});

/**
 * Gulp watch
 */
gulp.task( 'watch', function(){
    gulp.watch( [ 'assets/css/*/*.scss', 'assets/css/*/*.sass' ], [ 'sass' ] );
    gulp.watch( [ 'assets/css/*/*.scss' ], [ 'styles' ] );
    gulp.watch( [ 'assets/js/*/*.js', '!assets/js/*/*.min.*' ], [ 'scripts' ] );
});