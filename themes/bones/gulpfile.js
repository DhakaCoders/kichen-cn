var gulp            = require('gulp');
var concat          = require('gulp-concat');
var sass            = require('gulp-sass');
var autoprefixer    = require('gulp-autoprefixer');
var sourcemaps      = require('gulp-sourcemaps');
var uglify          = require('gulp-uglify');
var rename          = require('gulp-rename');
var order           = require('gulp-order');
var cssmin          = require('gulp-cssmin');
var addsrc          = require('gulp-add-src');

gulp.task('sass_main', function () {
  return gulp.src(['library/scss/**/*.scss'])
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 7', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
        .pipe(cssmin())
    .pipe(gulp.dest(''));
});

gulp.task('js', function() {
  return gulp.src('library/jslib/**/*.js')
    //.pipe(addsrc('library/lib/**/*.js'))
    .pipe(order([
        //'jquery.js',
        'scripts.js'
    ], { base: 'library/jslib/' }))
    //.pipe(sourcemaps.init())
        .pipe(concat('scripts.js'))
    //.pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('library/js'))
    //.pipe(sourcemaps.init())
        .pipe(rename('scripts.min.js'))
        .pipe(uglify())
    //.pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('library/js'));
});

gulp.task('watch', function(){
    gulp.watch(['library/scss/**/*.scss'], ['sass_main']);
    gulp.watch('library/jslib/**/*.js', ['js']);
});
