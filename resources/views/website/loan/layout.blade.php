<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="">
    <meta name="author" content="">

    <title>:: สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย ::</title>

    @section('styles')
        <!-- Bootstrap Core CSS -->
        {{ Html::style(elixir('css/bootstrap.css')) }}
        {{ Html::style(elixir('css/miscellaneous.css')) }}

        <!-- Font-Awesome Fonts -->
        {{ Html::style(elixir('css/font-awesome.css')) }}

        <!-- Bootstrap DataTable CSS -->
        {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

        <!-- Custom CSS -->
        {{ Html::style(elixir('css/metisMenu.css')) }}
        {{ Html::style(elixir('css/announce.css')) }}

        <style>
            .ajax-loading {
                position: absolute;
                left: 50%;
                top: 50%;
                background-color:rgba(0, 0, 0, 0.3);
                padding: 20px;
                border-radius: 5px;
                margin-left: -32px; /* -1 * image width / 2 */
                margin-top: -32px; /* -1 * image height / 2 */
            }
        </style>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    @show

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
            <div class="col-md-12">
                @yield('content')
            </div>
        </div>

    </div>
    <!-- /.container -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>

    <!-- Footer -->
    <footer>
        @include('website.documents.footer')
    </footer>

    @section('scripts')
        <!-- jQuery -->
        {{ Html::script(elixir('js/jquery.js')) }}

        <!-- Bootstrap Core JavaScript -->
        {{ Html::script(elixir('js/bootstrap.js')) }}

        <!-- Bootstrap DataTable JavaScript -->
        {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
        {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
        {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

        <!-- Custom JavaScript -->
        {{ Html::script(elixir('js/sb-admin-2.js')) }}
        {{ Html::script(elixir('js/metisMenu.js')) }}

        <script>
        $(document).ready(function () {
            $('[data-tooltip="true"]').tooltip();    
        });   
        </script>
    @show
</body>

</html>
