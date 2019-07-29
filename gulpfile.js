var gulp = require('gulp');
var sass = require('gulp-sass');
var changed = require('gulp-changed');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');

var paths = {
  HERE: './',
  CSS: './public/assets/css/',
  SCSS: './public/assets/scss/**/*.scss',
  SCSS_TOOLKIT_SOURCES: './public/assets/scss/material-kit.scss'
};

gulp.task('sass', function() {
  return gulp.src(paths.SCSS_TOOLKIT_SOURCES)
    .pipe(changed(paths.CSS))
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write(paths.HERE))
    .pipe(gulp.dest(paths.CSS));
});

gulp.task('watch', function() {
  gulp.watch(paths.SCSS, ['sass']);
});

gulp.task('default', ['watch', 'sass']);