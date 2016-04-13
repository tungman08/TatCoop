var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.config.production = true;
elixir.config.sourcemaps = false;

elixir(function(mix) {
    mix.sass('bootstrap.scss')
        .sass('font-awesome.scss')
        .sass('homepage.scss')
        .sass('announce.scss');

    mix.less('admin-lte.less')
        .less('sb-admin-2.less');

    mix.scripts('./bower_components/bootstrap-sass/assets/javascripts/bootstrap.js')
        .scripts('./bower_components/jquery/dist/jquery.js')
        .scripts('./bower_components/metisMenu/src/metisMenu.js')
        .scripts('./bower_components/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js')
        .scripts('jquery.easing.js')
        .scripts('homepage.js');

    mix.styles('./bower_components/metisMenu/src/metisMenu.css')
        .styles('login.css');

    mix.copy('bower_components/bootstrap-sass/assets/fonts/bootstrap', 'public/fonts')
        .copy('bower_components/font-awesome/fonts', 'public/fonts')
        .copy('resources/assets/fonts', 'public/fonts')
        .copy('resources/assets/images', 'public/images');

    mix.version([
        'js/bootstrap.js',
        'js/jquery.js',
        'js/jquery.easing.js',
        'js/sb-admin-2.js',
        'js/metisMenu.js',
        'js/homepage.js',
        'css/bootstrap.css',
        'css/font-awesome.css',
        'css/metisMenu.css',
        'css/admin-lte.css',
        'css/sb-admin-2.css',
        'css/homepage.css',
        'css/announce.css',
        'css/login.css',
    ]);
});
