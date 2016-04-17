<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>:: สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด ::</title>

    <!-- Bootstrap Core CSS -->
    {{ Html::style(elixir('css/bootstrap.css')) }}

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

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url(env('APP_URL', 'http://www.tatcoop.dev')) }}"><b>สอ.สรทท.</b></a>
        </div>
        <!-- /.login-logo -->
        
        @yield('content')
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    {{ Html::script(elixir('js/jquery.js')) }}

    <!-- Bootstrap Core JavaScript -->
    {{ Html::script(elixir('js/bootstrap.js')) }}

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <!-- Custom JavaScript -->
    {{ Html::script(elixir('js/auth.js')) }}

</body>

</html>
