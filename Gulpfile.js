var gulp = require('gulp');

var sourcemaps = require('gulp-sourcemaps');
var sass = require('gulp-sass');
var postcss = require('gulp-postcss');

var autoprefixer = require('autoprefixer');
var cssnano = require('cssnano');

gulp.task('sass', function () {
  var processors = [
    autoprefixer,
    cssnano
  ];

  return gulp.src('./sass/robot-pages.sass')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss(processors))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./'));
});

gulp.task('sass:watch', function() {
  gulp.watch('**/*.s*ss', {cwd: 'sass'}, ['sass']);
});