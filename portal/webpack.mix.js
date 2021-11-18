const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

mix.copy('node_modules/bootstrap-icons', 'public/bootstrap-icons');
mix.copy('node_modules/bootstrap', 'public/bootstrap');

mix.styles([
    'resources/css/login.css',
    'resources/css/sidebar.css',
], 'public/css/style.css');
