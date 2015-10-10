// Javascript Task Runner
'use strict';

var gulp = require('gulp');
var browserSync = require('browser-sync').create();

function handleError(error) {
    console.log(error);
    this.emit('end');
}

// Refresh browser on changes
gulp.task('browsersync', function(gulpCallback) {
    browserSync.init({
        open: false,
        proxy: "spacexstats.dev"
    }, function callback() {

        gulp.watch('app/resources/assets/css/**/*.scss', ['styles']);
        gulp.watch('app/resources/assets/javascript/**/*.*', ['scripts']);

        gulpCallback();
    });
});

// Clean media folder occasionally
gulp.task('clean', function() {
    var del = require('del');
    del(['public/media/small/*', 'public/media/large/*', 'public/media/full/*', 'public/media/twitter/*',
    '!public/media/**/audio.png']);
});

// Scripts Task. Concat and minify.
gulp.task('scripts', function() {
    var uglify = require('gulp-uglify');
    var concat = require('gulp-concat');
    var rename = require('gulp-rename');

    // Move angular module definition
    gulp.src('app/resources/assets/javascript/angular/apps/app.js')
        .pipe(gulp.dest('public/js'));

    // Move angular stuff
    gulp.src(['app/resources/assets/javascript/angular/**/*.js', '!app/resources/assets/javascript/angular/apps/app.js'])
        .pipe(concat('spacexstatsApp.js')).on('error', handleError)
        //.pipe(uglify()).on('error', handleError)
        .pipe(gulp.dest('public/js')).on('error', handleError)
        .pipe(browserSync.stream());

    // Move templates
    gulp.src('app/resources/assets/javascript/angular/**/*.html')
        .pipe(rename({ dirname: ''}))
        .pipe(gulp.dest('public/js/templates'));

    // Move library
    gulp.src('app/resources/assets/javascript/lib/**/*.js')
        .pipe(gulp.dest('public/js'));
});

// Styles task. Compile all the styles together, autoprefix them, and convert them from SASS to CSS
gulp.task('styles', function() {
    var autoprefixer = require('gulp-autoprefixer');
    var sass = require('gulp-sass');

    gulp.src('app/resources/assets/javascript/css/styles.scss')
        .pipe(sass()).on('error', handleError)
        .pipe(autoprefixer())
        .pipe(gulp.dest('public/css'))
        .pipe(browserSync.stream());

});

// Images Task. Minify all images in the src/images folder using imagemin
gulp.task('images', function() {
    var imagemin = require('gulp-imagemin');

    gulp.src('app/resources/assets/javascript/images/**/*.{jpg,jpeg,png}')
        .pipe(imagemin())
        .pipe(gulp.dest('public/images'));
});

// Fonts Task.
gulp.task('fonts', function() {
   gulp.src('app/resources/assets/javascript/fonts/*')
       .pipe(gulp.dest('public/fonts'));
});

// Watch task. Watch for changes automatically and recompile the SASS.
gulp.task('watch', function() {
    gulp.watch('app/resources/assets/css/**/*.scss', ['styles']);
    gulp.watch('app/resources/assets/javascript/**/*.*', ['scripts']);
});

gulp.task('default', ['styles', 'watch', 'browsersync']);