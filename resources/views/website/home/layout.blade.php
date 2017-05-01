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

    <!-- Custom CSS -->
    {{ Html::style(elixir('css/metisMenu.css')) }}
    {{ Html::style(elixir('css/homepage.css')) }}

    @php($ribbon = Diamond::parse('2017-10-13')->gt(Diamond::today()))
    @if ($ribbon)
        <!-- Black Ribbon CSS -->
        {{ Html::style(elixir('css/black-ribbon.css')) }}
    @endif

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<!-- The #page-top ID is part of the scrolling feature - the data-spy and data-target are part of the built-in Bootstrap scrollspy function -->

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    @if ($ribbon)
        <!-- Black Ribbon Top Left -->
        <img src="{{ asset('images/black_ribbon_top_left.png') }}" class="black-ribbon stick-top-left"/>
    @endif

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        @include('website.home.navbar')
    </nav>

    <!-- Intro Section -->
    <section id="intro" class="intro-section">
        <div class="container">
            <div class="row">

                <div class="col-md-3">
                    @include('website.home.menu')
                </div>

                <div class="col-md-9">
                    @yield('content')
                </div>

            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        @include('website.home.services')
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        @include('website.home.knowledge')
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        @include('website.home.contact')
    </section>

    <!-- Footer -->
    <footer>
        @include('website.home.footer')
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
