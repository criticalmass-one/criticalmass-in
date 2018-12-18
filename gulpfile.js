let gulp = require('gulp');
let minify = require('gulp-minify');
let cleanCSS = require('gulp-clean-css');
let concat = require('gulp-concat');
let urlAdjuster = require('gulp-css-replace-url');

let sass = require('gulp-sass');
sass.compiler = require('node-sass');

/* Leaflet */

gulp.task('leaflet-images', function () {
/*    return gulp.src('node_modules/leaflet/dist/images/*')
        .pipe(gulp.dest('public/img/leaflet'));*/
});

gulp.task('leaflet-css', [], function() {
/*    return gulp.src('node_modules/leaflet/dist/leaflet.css')
        .pipe(urlAdjuster({
            replace: ['images/','/img/leaflet/'],
        }))
        .pipe(gulp.dest('assets/css'));*/
});

gulp.task('build-leaflet', ['leaflet-images', 'leaflet-css']);


/* Leaflet-Extramarkers */

gulp.task('extramarkers-images', function () {
/*    return gulp.src('node_modules/leaflet-extra-markers/dist/img/*')
        .pipe(gulp.dest('public/img/leaflet-extra-markers'));*/
});

gulp.task('extramarkers-css', [], function() {
/*
    return gulp.src('node_modules/leaflet-extra-markers/dist/css/leaflet.extra-markers.min.css')
        .pipe(urlAdjuster({
            replace: ['../img/','/img/leaflet-extra-markers/'],
        }))
        .pipe(gulp.dest('assets/css')); */
});

gulp.task('build-leaflet-extramarkers', ['extramarkers-images', 'extramarkers-css']);


/* Assets */

gulp.task('copy-colorpicker-images', function () {
    return gulp.src('node_modules/bootstrap-colorpicker/dist/img/bootstrap-colorpicker/*')
        .pipe(gulp.dest('public/images/'));
});

gulp.task('copy-asset-images', function () {
    return gulp.src('assets/images/*/*')
        .pipe(gulp.src('node_modules/bootstrap-colorpicker/dist/img/bootstrap-colorpicker'))
        .pipe(gulp.dest('public/images/'));
});

gulp.task('copy-fonts', function () {
/*    return gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('public/fonts'));*/
});

gulp.task('build-assets', ['copy-asset-images', 'copy-fonts', 'copy-colorpicker-images']);


/* CSS */

gulp.task('sass', function () {
/*    return gulp.src('assets/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css'));*/
});

gulp.task('compress-css', ['leaflet-css', 'sass'], function () {
    return gulp.src([
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
            'node_modules/dropzone/dist/min/dropzone.min.css',
            'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css',
        ])
        .pipe(cleanCSS())
        .pipe(concat('criticalmass.min.css'))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('build-css', ['sass', 'compress-css']);


/* Javascript */

gulp.task('compress-js', function () {
    return gulp.src([
        'assets/js/*',
    ])
        .pipe(minify({
            ext: {
                min:'.min.js'
            },
            noSource: true,
        }))
        .pipe(gulp.dest('public/js/'));
});

gulp.task('copy-js-external', function () {
    return gulp.src([
        //'node_modules/jquery/dist/jquery.min.js',
        //'node_modules/popper.js/dist/popper.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
        'node_modules/dropzone/dist/min/dropzone-amd-module.min.js',
        'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        //'node_modules/leaflet/dist/leaflet.js',
        //'node_modules/leaflet-extra-markers/dist/js/leaflet.extra-markers.min.js',
        //'node_modules/typeahead.js/dist/bloodhound.min.js',
        //'node_modules/typeahead.js/dist/typeahead.jquery.min.js',
        //'node_modules/calendar_heatmap/calendar_heatmap.bundle.js',
        //'node_modules/chart.js/dist/Chart.bundle.min.js',
    ])
        .pipe(gulp.dest('public/js/'));
});

gulp.task('build-js', ['compress-js', 'copy-js-external']);

gulp.task('build', ['build-leaflet', 'build-leaflet-extramarkers', 'build-assets', 'build-js', 'build-css'], function () {});

gulp.task('default', ['build']);
