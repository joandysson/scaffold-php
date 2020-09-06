var gulp = require("gulp")
const sass = require("gulp-sass");
const sourcemaps = require("gulp-sourcemaps");
const autoprefixer = require("gulp-autoprefixer");
const rename = require("gulp-rename");
const concat = require("gulp-concat");
const cleanCSS = require("gulp-clean-css");
const uglify = require("gulp-uglify");

function swallowError(error) {
  // If you want details of the error in the console
  console.log(error.toString());
  this.emit("end");
}

const sassOptions = {
  errLogToConsole: true,
  outputStyle: "compressed",
};

//SASS
gulp.task("sass", function () {
  return gulp
    .src([
      "public/assets/css/sass/bootstrap/scss/*.scss",
    ])
    .pipe(sass(sassOptions).on("error", sass.logError))
    .pipe(gulp.dest("public/assets/css/"));
});

// CSS
gulp.task("css", function () {
  return gulp
    .src(["public/assets/css/*.css"])
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
        sourceRoot: "../../public/assets/css/sass",
      })
    )
    .pipe(gulp.dest("public/assets/css/"));
});

gulp.task("scripts", function () {
  return gulp
    .src([
      "public/assets/js/*.js",
    ])
    .pipe(concat("all.js"))
    .pipe(rename({ suffix: ".min" }))
    .pipe(uglify())
    .on("error", swallowError)
    .pipe(gulp.dest("public/assets/js/"));
});

//Watch Task
gulp.task("watch", function () {
  gulp.watch("public/assets/js/main.js", gulp.series("scripts"));
  gulp.watch("public/assets/css/sass/site.scss", gulp.series("sass"));
  gulp.watch("public/assets/css/site.css", gulp.series("css"));
});

//Default task
gulp.task("default", gulp.series("scripts", "sass", "css", "watch"));
