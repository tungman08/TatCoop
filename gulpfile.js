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
        .sass('announce.scss')
        .sass('toggle-switch.scss');

    mix.less('bootstrap-datetimepicker.less')
        .less('admin-lte.less')
        .less('sb-admin-2.less')
        .less('admin-carousel.less');

    mix.scripts('./bower_components/bootstrap-sass/assets/javascripts/bootstrap.js')
        .scripts('./bower_components/jquery/dist/jquery.js')
        .scripts('./bower_components/jquery-ui/jquery-ui.js')
        .scripts('./bower_components/metisMenu/src/metisMenu.js')
        .scripts(['./bower_components/AdminLTE/dist/js/app.js', 'admin-lte-addon.js'], 'public/js/admin-lte.js')
        .scripts('./bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js')
        .scripts('./bower_components/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js')
        .scripts('./bower_components/waitForImages/src/jquery.waitforimages.js')
        .scripts('./bower_components/moment/min/moment-with-locales.js', 'public/js/moment.js')
        //.scripts('./bower_components/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')
        .scripts('bootstrap-datetimepicker.js')
        .scripts('./bower_components/datatables/media/js/jquery.dataTables.js')
        .scripts('./bower_components/datatables/media/js/dataTables.bootstrap.js')
        .scripts('./bower_components/datatables-responsive/js/dataTables.responsive.js')
        .scripts('./bower_components/datatables-plugins/sorting/formatted-numbers.js')
        .scripts('./bower_components/flot/jquery.flot.js')
        .scripts('./bower_components/flot/jquery.flot.resize.js')
        .scripts('./bower_components/flot.tooltip/js/jquery.flot.tooltip.js')
        .scripts('./bower_components/jquery-circle-progress/dist/circle-progress.js')
        .scripts('./bower_components/magnific-popup/dist/jquery.magnific-popup.js')
        .scripts('./bower_components/jquery-number/jquery.number.js')
        .scripts(['jquery.inputmask.js', 'jquery.inputmask.date.extensions.js', 'jquery.inputmask.extensions.js'], 'public/js/jquery.inputmask.js', './bower_components/AdminLTE/plugins/input-mask')
        .scripts('./bower_components/jquery-confirm2/js/jquery-confirm.js')
        .scripts('./bower_components/jQuery-SlotMachine/dist/slotmachine.js', 'public/js/jquery.slotmachine.js')
        .scripts('jquery.easing.js')
        .scripts('admin-statistics.js')
        .scripts('admin-form.js')
        .scripts('admin-document.js')
        .scripts('admin-carousel.js')
        .scripts('admin-attachment.js')
        .scripts('member-form.js')
        .scripts('auth.js')
        .scripts('homepage.js');

    mix.styles('./bower_components/metisMenu/src/metisMenu.css')
        .styles('./bower_components/datatables/media/css/dataTables.bootstrap.css')
        .styles('./bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.css')
        .styles('./bower_components/magnific-popup/dist/magnific-popup.css')
        .styles('./bower_components/jquery-confirm2/css/jquery-confirm.css')
        .styles(['./bower_components/jQuery-SlotMachine/dist/jquery.slotmachine.css', 'slotmachine.css'], 'public/css/jquery.slotmachine.css')
        .styles('auth.css')
        .styles('stepwizard.css')
        .styles('jquery-circle-progress.css')
        .styles('black-ribbon.css');

    mix.copy('bower_components/bootstrap-sass/assets/fonts/bootstrap', 'public/fonts')
        .copy('bower_components/font-awesome/fonts', 'public/fonts')
        .copy('resources/assets/fonts', 'public/fonts')
        .copy('resources/assets/images', 'public/images');

    mix.version([
        'js/bootstrap.js',
        'js/jquery.js',
        'js/jquery-ui.js',
        'js/jquery.easing.js',
        'js/jquery.inputmask.js',
        'js/jquery.number.js',
        'js/admin-lte.js',
        'js/bootstrap3-wysihtml5.all.js',
        'js/admin-form.js',
        'js/admin-document.js',
        'js/admin-carousel.js',
        'js/admin-attachment.js',
        'js/member-form.js',
        'js/sb-admin-2.js',
        'js/jquery.waitforimages.js',
        'js/jquery.magnific-popup.js',
        'js/circle-progress.js',
        'js/metisMenu.js',
        'js/moment.js',
        'js/bootstrap-datetimepicker.js',
        'js/jquery.dataTables.js',
        'js/formatted-numbers.js',
        'js/dataTables.bootstrap.js',
        'js/dataTables.responsive.js',
        'js/jquery.flot.js',
        'js/jquery.flot.resize.js',
        'js/jquery.flot.tooltip.js',
        'js/jquery-confirm.js',
        'js/jquery.slotmachine.js',
        'js/admin-statistics.js',
        'js/auth.js',
        'js/homepage.js',
        'css/bootstrap.css',
        'css/miscellaneous.css',
        'css/font-awesome.css',
        'css/bootstrap3-wysihtml5.css',
        'css/bootstrap-datetimepicker.css',
        'css/dataTables.bootstrap.css',
        'css/jquery-circle-progress.css',
        'css/magnific-popup.css',
        'css/metisMenu.css',
        'css/admin-lte.css',
        'css/sb-admin-2.css',
        'css/homepage.css',
        'css/announce.css',
        'css/auth.css',
        'css/stepwizard.css',
        'css/admin-carousel.css',
        'css/black-ribbon.css',
        'css/toggle-switch.css',
        'css/jquery-confirm.css',
        'css/jquery.slotmachine.css',
    ]);
});
