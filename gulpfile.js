var gulp = require("gulp"),
  sass = require("gulp-sass"),
  sourcemaps = require("gulp-sourcemaps"),
  autoprefixer = require("gulp-autoprefixer"),
  rename = require("gulp-rename"),
  concat = require("gulp-concat"),
  cleanCSS = require("gulp-clean-css"),
  uglify = require("gulp-uglify");

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
      "assets/css/sass/bootstrap/scss/bootstrap.scss",
      "assets/css/sass/site.scss",
    ])
    .pipe(sass(sassOptions).on("error", sass.logError))
    .pipe(gulp.dest("assets/css/"));
});

// CSS
gulp.task("css", function () {
  return gulp
    .src(["assets/css/bootstrap.css", "assets/css/site.css"])
    .pipe(concat("all.css"))
    .pipe(
      autoprefixer({
        overrideBrowserslist: ["last 2 versions"],
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
    .pipe(gulp.dest("assets/css/"));
});

gulp.task("scripts", function () {
  return gulp
    .src([
      // The order that you list the files in this array IS IMPORTANT!!
      "assets/js/jquery-3.2.1.min.js",
      //'assets/js/bootstrap.min.js',
      //'assets/js/popper.js',
      "assets/js/main.js",
    ])
    .pipe(concat("all.js"))
    .pipe(rename({ suffix: ".min" }))
    .pipe(uglify())
    .on("error", swallowError)
    .pipe(gulp.dest("assets/js/"));
});

//Watch Task
gulp.task("watch", function () {
  gulp.watch("assets/js/main.js", gulp.series("scripts"));
  gulp.watch("assets/css/sass/site.scss", gulp.series("sass"));
  gulp.watch("assets/css/site.css", gulp.series("css"));
});

//Default task
gulp.task("default", gulp.series("scripts", "sass", "css", "watch"));
