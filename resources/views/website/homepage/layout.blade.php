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
    {{ Html::style(elixir('css/miscellaneous.css')) }}

    <!-- Font-Awesome Fonts -->
    {{ Html::style(elixir('css/font-awesome.css')) }}

    <!-- Custom CSS -->
    {{ Html::style(elixir('css/metisMenu.css')) }}
    {{ Html::style(elixir('css/homepage.css')) }}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<!-- The #page-top ID is part of the scrolling feature - the data-spy and data-target are part of the built-in Bootstrap scrollspy function -->

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        @include('website.homepage.navbar')
    </nav>

    <!-- Intro Section -->
    <section id="intro" class="intro-section">
        <div class="container">
            <div class="row">

                <div class="col-md-3">
                    @include('website.homepage.menu')
                </div>

                <div class="col-md-9">
                    @yield('content')
                </div>

            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        @include('website.homepage.services')
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        @include('website.homepage.about')
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        @include('website.homepage.contact')
    </section>

    <!-- Footer -->
    <footer>
        @include('website.homepage.footer')
    </footer>

    <!-- jQuery -->
    {{ Html::script(elixir('js/jquery.js')) }}

    <!-- Bootstrap Core JavaScript -->
    {{ Html::script(elixir('js/bootstrap.js')) }}

    <!-- Homepage JavaScript -->
    {{ Html::script(elixir('js/sb-admin-2.js')) }}
    {{ Html::script(elixir('js/jquery.easing.js')) }}
    {{ Html::script(elixir('js/metisMenu.js')) }}
    {{ Html::script(elixir('js/homepage.js')) }}

</body>

</html>
