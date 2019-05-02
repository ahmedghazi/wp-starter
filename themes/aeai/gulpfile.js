//https://github.com/ahmadawais/WPGulp/blob/master/gulpfile.js

var projectURL      		= 'site.stg',
	styleSrc         		= './src/scss/**/*.scss', // Path to all *.scss files inside css folder and inside them.
//var vendorJSWatchFiles      = './src/js/vendor/*.js'; // Path to all vendor JS files.
  jsCustomSrc             = './src/js/*.js', // Path to all custom JS files.
	jsVendorSrc      				= './src/js/vendor/*.js', // Path to all custom JS files.
	phpSrc    				= './**/*.php',
	imagesSRC               = './src/images/**/*.{png,jpg,gif,svg}';

var gulp 			= require('gulp'),
	rename       	= require('gulp-rename'),
  sass 			= require('gulp-ruby-sass'),
  autoprefixer 	= require('gulp-autoprefixer'),
  minifycss 		= require('gulp-minify-css'),
  concat 			= require('gulp-concat'),
  //jshint 			= require('gulp-jshint'),
  stripDebug 		= require('gulp-strip-debug'),
  //stylish 		= require('jshint-stylish'),
  uglify 			= require('gulp-uglify'),
  // newer 		= require('gulp-newer'),
  // imagemin 	= require('gulp-imagemin'),
  include         = require("gulp-include"),
  //livereload 		= require('gulp-livereload');
  browserSync  	= require('browser-sync').create(), // Reloads browser and injects CSS. Time-saving synchronised browser testing.
	reload       	= browserSync.reload; // For manual browser reload.

// var imgSrc = 'assets/images/originals/*';
// var imgDest = 'assets/images';

gulp.task( 'browser-sync', function() {
  browserSync.init( {
    proxy: projectURL,
    // `true` Automatically open the browser with BrowserSync live server.
    // `false` Stop the browser from automatically opening.
    open: false,
    notify: false,
    // Inject CSS changes.
    // Commnet it to reload browser for every CSS change.

    injectChanges: true,
    // Use a specific port (instead of the one auto-detected by Browsersync).
    // port: 7000,

  } );
});

gulp.task('styles', function(){
  return sass('./src/scss/**/**/*.scss', {
          trace: true,
          verbose: true
      }) 
    .on('error', function (err) {
      console.error('Error!', err.message);
    })
    .pipe(gulp.dest(''))
    .pipe(autoprefixer())
    //.pipe(minifycss())
    .pipe(gulp.dest(''))
    .pipe(browserSync.stream());
});

gulp.task('styles-dist', function(){
  return sass('./src/scss/**/**/*.scss', {
          trace: true,
          verbose: true
      }) 
    .on('error', function (err) {
      console.error('Error!', err.message);
    })
    .pipe(gulp.dest(''))
    .pipe(autoprefixer())
    .pipe(minifycss())
    .pipe(gulp.dest(''))
    .pipe(browserSync.stream());
});

// Task to concat, strip debugging and minify JS files
gulp.task('scriptsCustom', function() {
    gulp.src(['./src/js/*.js'])
      .pipe(concat('app.js'))
      .pipe(rename({suffix: '.min'}))
      //.pipe(stripDebug())//remove logs
      //.pipe(uglify())
      .pipe(gulp.dest('./assets/js/'));
});

gulp.task('scriptsCustom-dist', function() {
  gulp.src(['./src/js/*.js'])
    .pipe(concat('app.js'))
    .pipe(rename({suffix: '.min'}))
    .pipe(stripDebug())//remove logs
    .pipe(uglify())
    .pipe(gulp.dest('./assets/js/'));
});

gulp.task('scriptsVendor', function() {
    gulp.src(['./src/js/vendor/*.js'])
      .pipe(concat('vendor.js'))
      .pipe(rename({suffix: '.min'}))
      //.pipe(stripDebug())//remove logs
      .pipe(uglify())
      .pipe(gulp.dest('./assets/js/'));
});

/**
  * Task: `images`.
  *
  * Minifies PNG, JPEG, GIF and SVG images.
  *
  * This task does the following:
  *     1. Gets the source of images raw folder
  *     2. Minifies PNG, JPEG, GIF and SVG images
  *     3. Generates and saves the optimized images
  *
  * This task will run only once, if you want to run it
  * again, do it with the command `gulp images`.
  */
 gulp.task( 'images', function() {
  gulp.src( imagesSRC )
    .pipe( imagemin( {
          progressive: true,
          optimizationLevel: 3, // 0-7 low-high
          interlaced: true,
          svgoPlugins: [{removeViewBox: false}]
        } ) )
    .pipe(gulp.dest( imagesDestination ))
    .pipe( notify( { message: 'TASK: "images" Completed! 💯', onLast: true } ) );
 });


// Fonts
gulp.task('fonts', function() {
    return gulp.src([
      'src/fonts/*'])
            .pipe(gulp.dest('assets/fonts/'));
});

// Images
gulp.task('images', function() {
    return gulp.src([
      'src/images/*'])
            .pipe(gulp.dest('assets/images/'));
});

// Clean
gulp.task('clean', function () {
    return gulp.src(['assets/fonts', 'assets/js', 'assets/images'], { read: false }).pipe($.clean());
});

//gulp tpl --template template-name
gulp.task('tpl', function() {
  let template = process.argv[4]
  console.log(template);

    gulp.src(['page-sample.php'])
      .pipe(rename('page-'+template+'.php'))
      .pipe(gulp.dest('.'))
    gulp.src(['content-sample.php'])
      .pipe(rename('content-'+template+'.php'))
      .pipe(gulp.dest('.'))
    gulp.src(['src/scss/pages/_sample.scss'])
      .pipe(rename('_'+template+'.scss'))
      .pipe(gulp.dest('src/scss/pages'))
    gulp.src(['src/js/sample.js'])
      .pipe(rename(''+template+'-controller.js'))
      .pipe(gulp.dest('src/js/'))

  console.log("Done !!!")
});


/**
  * Watch Tasks.
  *
  * Watches for file changes and runs specific tasks.
  */
gulp.task( 'default', ['fonts', 'images', 'styles', 'scriptsCustom', 'scriptsVendor', 'browser-sync'], function () {
  gulp.watch( phpSrc, reload ); // Reload on PHP file changes.
  gulp.watch( styleSrc, [ 'styles' ] ); // Reload on SCSS file changes.
  gulp.watch( jsCustomSrc, [ 'scriptsCustom', reload ] ); // Reload on customJS file changes.
  gulp.watch( jsVendorSrc, [ 'scriptsVendor', reload ] ); // Reload on customJS file changes.
});

gulp.task( 'dist', ['fonts', 'images', 'styles-dist', 'scriptsCustom-dist', 'scriptsVendor', 'browser-sync'], function () {
  gulp.watch( phpSrc, reload ); // Reload on PHP file changes.
  gulp.watch( styleSrc, [ 'styles' ] ); // Reload on SCSS file changes.
  gulp.watch( jsCustomSrc, [ 'scriptsCustom', reload ] ); // Reload on customJS file changes.
  gulp.watch( jsVendorSrc, [ 'scriptsVendor', reload ] ); // Reload on customJS file changes.
});
