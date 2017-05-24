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

    @section('styles')
        <!-- Bootstrap Core CSS -->
        {{ Html::style(elixir('css/bootstrap.css')) }}
        {{ Html::style(elixir('css/miscellaneous.css')) }}

        <!-- Font-Awesome Fonts -->
        {{ Html::style(elixir('css/font-awesome.css')) }}

        <!-- Theme style -->
        {{ Html::style(elixir('css/admin-lte.css')) }}
    @show

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition {{ $user->theme->code }} sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <!-- Header Brand -->
            @include('website.member.layouts.brand')

            <!-- Header Navbar: style can be found in header.less -->
            @include('website.member.layouts.navbar')
        </header>

        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            @include('website.member.layouts.menu')
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            @include('website.member.layouts.footer')
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            @include('website.member.layouts.sidebar')
        </aside>
        <!-- /.control-sidebar -->

        <!-- Add the sidebar's background. This div must be placedimmediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>

    </div>
    <!-- ./wrapper -->

    @section('scripts')
        <!-- jQuery -->
        {{ Html::script(elixir('js/jquery.js')) }}

        <!-- Bootstrap Core JavaScript -->
        {{ Html::script(elixir('js/bootstrap.js')) }}

        <!-- AdminLTE App -->
        {{ Html::script(elixir('js/admin-lte.js')) }}
    @show
</body>
</html>
