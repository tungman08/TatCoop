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

    <title>:: สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย ::</title>

    <!-- Bootstrap Core CSS -->
    {{ Html::style(elixir('css/bootstrap.css')) }}
    {{ Html::style(elixir('css/miscellaneous.css')) }}

    <!-- Font-Awesome Fonts -->
    {{ Html::style(elixir('css/font-awesome.css')) }}

    <!-- Custom CSS -->
    {{ Html::style(elixir('css/metisMenu.css')) }}
    {{ Html::style(elixir('css/announce.css')) }}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-inverse" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ url('/') }}">สอ.สรทท.</a>
            </div>
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <div class="col-md-3 sidebar">
                @include('website.homepage.menu')
            </div>

            <div class="col-md-9">
                @yield('content')
            </div>

        </div>

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer>
        @include('website.documents.footer')
    </footer>

    <!-- jQuery -->
    {{ Html::script(elixir('js/jquery.js')) }}

    <!-- Bootstrap Core JavaScript -->
    {{ Html::script(elixir('js/bootstrap.js')) }}

    <!-- Custom JavaScript -->
    {{ Html::script(elixir('js/sb-admin-2.js')) }}
    {{ Html::script(elixir('js/metisMenu.js')) }}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();
    });
    </script>
</body>

</html>
