/**
 * Imports
 */
const {src, dest, watch, parallel, task} = require('gulp');
const sass    = require('gulp-sass');
const plumber = require('gulp-plumber');
const rename  = require('gulp-rename');
const postcss = require('gulp-postcss');
const color   = require('gulp-color');
const autoprefixer = require('autoprefixer');

var cssDir  = './public/assets/css',
    sassDir = './public/assets/scss';

task('sass', function () {
    return compile_scss();
});

function compile_scss() {
  return _compile_scss(sassDir + '/**/*.scss', null, false, true);
}

function _compile_scss(path, onEnd, log=true, ret=false) {
  if(log)
    _log('[SCSS] Compiling:' + path, 'GREEN');

  let compile_scss = src(path)
  .pipe(plumber())
  .pipe(sass({
    errorLogToConsole: true
  }))
  .on('error', console.error.bind(console))
  .on('end', () => {
    if(onEnd)
      onEnd.call(this);

    if(log)
      _log('[SCSS] Finished', 'GREEN');
  })
  .pipe(rename({
    dirname: '',
    extname: '.css'
  }))
  .pipe(postcss([autoprefixer()]))
  .pipe(dest(cssDir))
  .pipe(plumber.stop());

  if(ret) return compile_scss;
}

function _log(str, clr) {
  if(!clr) clr = 'WHITE';
  console.log(color(str, clr));
}