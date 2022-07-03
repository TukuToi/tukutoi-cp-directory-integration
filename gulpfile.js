const gulp  = require('gulp');
const wpPot = require('gulp-wp-pot');

function watch() {
	gulp.watch('./*.php', wp_pot);
	gulp.watch('./includes/*.php', wp_pot);
}

function wp_pot() {
    return gulp.src('./**/**/*.php')
        .pipe(wpPot( {
            domain: 'cp-plgn-drctry',
            package: 'Cp_Plgn_Drctry'
        } ))
        .pipe(gulp.dest('./languages/cp-plgn-drctry.pot'));
}

exports.watch = watch;
exports.wp_pot = wp_pot;