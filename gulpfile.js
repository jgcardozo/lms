let gulp = require('gulp');
let sass = require('gulp-sass');
let postcss = require('gulp-postcss');
let flexbugs = require('postcss-flexbugs-fixes');
let autoprefixer = require('autoprefixer');
let sourcemaps = require('gulp-sourcemaps');
let clean = require('gulp-clean');

let fs = require('fs');
let randomStr = require('randomstring');
const { kill } = require('process');
const { doesNotMatch } = require('assert');

let SOURCE_DIR = 'public/scss/**/[^_]*.scss';
let ALL_SASS_FILES = 'public/scss/**/*.scss';
let DEST_DIR = 'public/resources-bank/';

let autoprefixerBrowsers = [
  'last 2 versions',
  'ie 11',
  '> 2%'
];

gulp.task('compile', () => {
  console.log("Compiling");

  let plugins = [
    autoprefixer({
      browsers: autoprefixerBrowsers
    }),
    flexbugs()
  ];

  return gulp.src(SOURCE_DIR)
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: 'compact' }).on('error', sass.logError))
    .pipe(postcss(plugins))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(() => DEST_DIR));
});


gulp.task('dist', () => {
  console.log("Preparing for Dist");

  let plugins = [
    autoprefixer({
      browsers: autoprefixerBrowsers
    }),
    flexbugs()
  ];

  return gulp.src(SOURCE_DIR)
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
    .pipe(postcss(plugins))
    //.pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(() => DEST_DIR));
});







let destinationDir = '.';


function done() {
  console.log("Erick");
  return;
}
function helloWorldTask(callback) {
  console.log("Im done!");
  callback();
}


gulp.task('clean-cache-buster', function () {
  return gulp.src([destinationDir + '/cache.buster'])
    .pipe(clean({ force: true }));
});
gulp.task('bust-cache', gulp.series('clean-cache-buster', function () {
  console.log("Writing cache buster");
  let randomNumber = Math.floor(Math.random() * 6) + 5;
  let randomString = randomStr.generate(randomNumber);
  fs.writeFileSync(destinationDir + '/cache.buster', randomString);

  return gulp.src([destinationDir + '/cache.buster'])
  //.pipe(clean({ force: true }));

}));



gulp.task('watch', function () {
  console.log("Starting the watcher");
  //gulp.watch(ALL_SASS_FILES, gulp.series('bust-cache', 'compile'));
  gulp.watch(ALL_SASS_FILES, gulp.series('compile'));
});



gulp.task('default', gulp.parallel('compile', 'watch'));
gulp.task('dist', gulp.parallel('dist'));
gulp.task('cache', gulp.parallel('bust-cache'));