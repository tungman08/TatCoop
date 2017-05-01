<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8" />
    <meta http-equiv='content-language' content='th' /> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name='distribution' content='global' /> 
    <meta name="description" content="เว็บไซต์สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย" />
    <meta name="keywords" content="tatcoop,สหกรณ์ออมทรัพย์,การท่องเที่ยวแห่งประเทศไทย,สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย,สอ.สรทท.,เงินกู้,เงินฝาก,เงินปันผล" />
    <meta name="robots" content="index,nofollow,noarchive,noimageindex" />
    <meta name='revisit-after' content='7 days' />
    <meta name="author" content="Tungm@n" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>:: สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด ::</title>

    <!-- Bootstrap Core CSS -->
    {{ Html::style(elixir('css/bootstrap.css')) }}
    {{ Html::style(elixir('css/miscellaneous.css')) }}

    <!-- Font-Awesome Fonts -->
    {{ Html::style(elixir('css/font-awesome.css')) }}

    <!-- Admin-LTE -->
    {{ Html::style(elixir('css/admin-lte.css')) }}

    <!-- My Custom CSS -->
    {{ Html::style(elixir('css/auth.css')) }}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="hold-transition login-page" style="height: initial;">

    <header>
        <div class="progress progress-xxs active">
            <div id="progress" class="progress-bar progress-bar-primary progress-bar-striped"
                style="width: 100%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar">
                <span class="sr-only">100% Complete</span>
            </div>
        </div>
    </header>
    <!-- /header -->

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url(env('APP_URL', 'http://www.tatcoop.dev')) }}"><b>TAT Coopperative</b></a>
        </div>
        <!-- /.login-logo -->

        @yield('content')
    </div>
    <!-- /.login-box -->

    <div class="btn-group selector">
        <button id="previous" type="button" data-selected="0" class="btn btn-flat">
            <i class="fa fa-angle-left"></i>
        </button>
        <button id="next" type="button" data-selected="0" class="btn btn-flat">
            <i class="fa fa-angle-right"></i>
        </button>
    </div>

    <footer class="footer">
        <p class="muted credit text-right">
            <a id ="copyrightlink" target="_blank">
                <i class="fa fa-camera fa-fw"></i>&nbsp; <span id="copyright"></span>
            </a>
        </p>
    </footer>
    <!-- /footer -->

    <!-- jQuery -->
    {{ Html::script(elixir('js/jquery.js')) }}

    <!-- Bootstrap Core JavaScript -->
    {{ Html::script(elixir('js/bootstrap.js')) }}

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}
    
    <!-- jQuery waitforimages JavaScript -->
    {{ Html::script(elixir('js/jquery.waitforimages.js')) }}

    <!-- Custom JavaScript -->
    {{ Html::script(elixir('js/auth.js')) }}
    {{ Html::script(elixir('js/moment.js')) }}
</body>

</html>
