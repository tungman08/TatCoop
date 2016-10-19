process.env.DISABLE_NOTIFIER = true;

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
        .sass('miscellaneous.scss')
        .sass('font-awesome.scss')
        .sass('homepage.scss')
        .sass('announce.scss');

    mix.less('bootstrap-datetimepicker.less')
        .less('admin-lte.less')
        .less('sb-admin-2.less');

    mix.scripts('./bower_components/bootstrap-sass/assets/javascripts/bootstrap.js')
        .scripts('./bower_components/jquery/dist/jquery.js')
        .scripts('./bower_components/metisMenu/src/metisMenu.js')
        .scripts(['./bower_components/AdminLTE/dist/js/app.js', 'admin-lte-addon.js'], 'public/js/admin-lte.js')
        .scripts('./bower_components/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js')
        .scripts('./bower_components/waitForImages/src/jquery.waitforimages.js')
        .scripts('./bower_components/moment/min/moment-with-locales.js', 'public/js/moment.js')
        .scripts('./bower_components/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')
        .scripts('./bower_components/datatables/media/js/jquery.dataTables.js')
        .scripts('./bower_components/datatables/media/js/dataTables.bootstrap.js')
        .scripts('./bower_components/datatables-responsive/js/dataTables.responsive.js')
        .scripts('./bower_components/flot/jquery.flot.js')
        .scripts('./bower_components/flot/jquery.flot.resize.js')
        .scripts('./bower_components/flot.tooltip/js/jquery.flot.tooltip.js')
        .scripts(['jquery.inputmask.js', 'jquery.inputmask.date.extensions.js', 'jquery.inputmask.extensions.js'], 'public/js/jquery.inputmask.js', './bower_components/AdminLTE/plugins/input-mask')
        .scripts('jquery.easing.js')
        .scripts('admin-statistics.js')
        .scripts('admin-form.js')
        .scripts('member-form.js')
        .scripts('auth.js')
        .scripts('homepage.js');

    mix.styles('./bower_components/metisMenu/src/metisMenu.css')
        .styles('./bower_components/datatables/media/css/dataTables.bootstrap.css')
        .styles('auth.css');

    mix.copy('bower_components/bootstrap-sass/assets/fonts/bootstrap', 'public/fonts')
        .copy('bower_components/font-awesome/fonts', 'public/fonts')
        .copy('resources/assets/fonts', 'public/fonts')
        .copy('resources/assets/images', 'public/images');

    mix.version([
        'js/bootstrap.js',
        'js/jquery.js',
        'js/jquery.easing.js',
        'js/jquery.inputmask.js',
        'js/admin-lte.js',
        'js/admin-form.js',
        'js/member-form.js',
        'js/sb-admin-2.js',
        'js/jquery.waitforimages.js',
        'js/metisMenu.js',
        'js/moment.js',
        'js/bootstrap-datetimepicker.js',
        'js/jquery.dataTables.js',
        'js/dataTables.bootstrap.js',
        'js/dataTables.responsive.js',
        'js/jquery.flot.js',
        'js/jquery.flot.resize.js',
        'js/jquery.flot.tooltip.js',
        'js/admin-statistics.js',
        'js/auth.js',
        'js/homepage.js',
        'css/bootstrap.css',
        'css/miscellaneous.css',
        'css/font-awesome.css',
        'css/bootstrap-datetimepicker.css',
        'css/dataTables.bootstrap.css',
        'css/metisMenu.css',
        'css/admin-lte.css',
        'css/sb-admin-2.css',
        'css/homepage.css',
        'css/announce.css',
        'css/auth.css',
    ]);
});
