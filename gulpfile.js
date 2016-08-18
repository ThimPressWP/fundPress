/* 
 * Install Nodejs, npm
 * 'npm install --global gulp-cli'
 * 'npm init' - generate package.json
 * 'npm install --save-dev gulp' - install gulp in project
 * 'npm install --save-dev gulp-sass gulp-concat gulp-uglify gulp-save gulp-rename' - install gulp addons
 * 'npm install'
 * 'gulp'
 */

var gulp = require( 'gulp' ),
        concat = require( 'gulp-concat' ),
        sass = require( 'gulp-sass' ),
        uglify = require( 'gulp-uglify' ),
        rename = require( 'gulp-rename' ),
        save = require( 'gulp-save' );

/**
 * Compile sass files
 * @param {type} param1
 * @param {type} param2
 */
gulp.task( 'sass', function(){
    return gulp.src( [ 'assets/*/*.scss', 'assets/*/*.sass' ] )
            .pipe( sass().on('error', sass.logError) )
            .pipe( gulp.dest( 'assets/css' ) );
} );

/**
 * Minify css files
 */
gulp.task( 'styles', function(){
   return gulp.src( [ 'assets/*/*.css', '!assets/*/*.min.*' ] )
           .pipe( uglify() )
           .pipe( rename({ suffix: '.min' }) )
           .pipe( gulp.dest( 'assets/css' ) );
});

/**
 * Minify javascript files
 */
gulp.task( 'scripts', function(){
    return gulp.src( [ 'assets/*/*.js', '!assets/*/*.min.*' ] )
            .pipe( uglify() )
            .pipe( rename( { suffix: '.min' } ) )
            .pipe( gulp.dest( 'assets/js' ) );
});

/**
 * Gulp watch
 */
gulp.task( 'watch', function(){
    gulp.watch( [ 'assets/*/*.scss', 'assets/*/*.sass' ], [ 'sass', 'styles', 'scripts' ] );
    gulp.watch( [ 'assets/*/*.js', '!assets/*/*.min.*' ], [ 'scripts' ] );
});