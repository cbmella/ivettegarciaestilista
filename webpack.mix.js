const mix = require("laravel-mix");

mix
  .js("resources/js/app.js", "theme")
  .sass("resources/sass/style.scss", "theme", {
    processUrls: false,
  })
  .sourceMaps();
