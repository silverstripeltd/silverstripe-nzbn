let mix = require('laravel-mix');

if (!mix.inProduction()) {
  mix.webpackConfig({
    devtool: 'source-map'
  }).sourceMaps()
}

mix.options({
  processCssUrls: false
})

mix.js('client/src/js/nzbn.js', 'client/dist/js');
mix.sass('client/src/scss/nzbn.scss', 'client/dist/css');
mix.copyDirectory('client/src/images', 'client/dist/images');
