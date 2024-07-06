var gulp = require("gulp"),
  sass = require("gulp-sass"),
  sourcemaps = require("gulp-sourcemaps"),
  autoprefixer = require("gulp-autoprefixer"),
  rename = require("gulp-rename"),
  concat = require("gulp-concat"),
  cleanCSS = require("gulp-clean-css"),
  include = require("gulp-include"),
  uglify = require("gulp-uglify-es").default;
function swallowError(error) {
  // If you want details of the error in the console
  console.log(error.toString());
  this.emit("end");
}

var sassOptions = {
  errLogToConsole: true,
  outputStyle: "compressed",
};

//SASS
gulp.task("sass", function () {
  return gulp
    .src([
      "public/src/sass/pages/*.scss",
      "public/src/sass/pages/layout/global.scss",
    ], {allowEmpty: true})
    .pipe(sass(sassOptions).on("error", sass.logError))
    .pipe(
      autoprefixer({
        overrideBrowserslist: ["last 4 versions"],
        cascade: false,
      })
    )
    .pipe(rename({ suffix: ".min" }))
    .pipe(cleanCSS({ compatibility: "ie8" }))
    .pipe(
      sourcemaps.write("/", {
        includeContent: false,
        sourceRoot: "../../assets/css/sass",
      })
    )
    .pipe(gulp.dest("public/assets/css/"));
});

gulp.task("scripts", function () {
  return gulp
    .src(["public/src/js/pages/*.js"])
    .pipe(rename({ suffix: ".min" }))
    .pipe(include())
    .on("error", swallowError)
    .pipe(uglify())
    .pipe(gulp.dest("public/assets/js/"));
});

gulp.task("global_scripts", function () {
  //LISTA DE LIBS QUE SERÃO GLOBAIS
  return gulp
    .src([
      // The order that you list the files in this array IS IMPORTANT!!
      "public/src/js/libs/jquery-3.2.1.min.js",
      "public/src/js/libs/aos.js",
      //'public/src/js/libs/js/bootstrap.min.js',
      //'public/src/js/libs/js/popper.js',
      "public/src/js/global.js",
    ], {allowEmpty: true})
    .pipe(include())
    .pipe(concat("global.js"))
    .pipe(rename({ suffix: ".min" }))
    .pipe(uglify())
    .on("error", swallowError)
    .pipe(gulp.dest("public/assets/js/"));
});

//Watch Task
gulp.task("watch", function () {
  gulp.watch(["public/src/js/pages/*.js"], {interval: 1000, usePolling: true}, gulp.series("scripts"));
  gulp.watch(["public/src/js/global.js"], {interval: 1000, usePolling: true}, gulp.series("global_scripts"));
  gulp.watch(
    [
      "public/src/sass/*.scss",
      "public/src/sass/*/*.scss",
      "public/src/sass/*/*/*.scss",
    ],
    {interval: 1000, usePolling: true},
    gulp.series("sass")
  );
});

//Default task
gulp.task("default", gulp.series("global_scripts", "scripts", "sass", "watch"));
