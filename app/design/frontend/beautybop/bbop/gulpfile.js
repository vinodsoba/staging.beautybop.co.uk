'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var minifyCSS = require('gulp-clean-css');
var watch = require('gulp-watch');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var changed = require('gulp-changed');
var plumber = require('gulp-plumber');
var clean = require('gulp-clean');

var src_files = 'web/scss/**/*.scss';
var src_dest =  'web/css';

//compile SCSS
gulp.task('compile', function(){
    gulp.src('web/scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(minifyCSS())
        .pipe(clean())
        .pipe(rename({ suffix: '.min' }))
        .pipe(changed('web/css'))
        .pipe(gulp.dest('web/css'));
});

// detect changes in SCSS
gulp.task('watch', function(){
    gulp.watch(src_files, ['compile']);
});

// Run tasks
gulp.task('default', ['watch']);