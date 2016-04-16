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
    {!! Html::style(elixir('css/bootstrap.css')) !!}

    <!-- My Custom CSS -->
    {!! Html::style(elixir('css/login.css')) !!}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="vertical-center">
        <div class="container">
            <div class="row" id="pwd-container">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <section class="login-form">
                        {!! Form::open(['url' => '/auth/login', 'role' => 'form']) !!}
                            <img src="{{ asset('images/logo-coop.png') }}" class="img-responsive" alt="Co-op logo" />
                            {!! Form::text('email', null, ['required', 'class'=>'form-control input-lg', 'placeholder'=>'อีเมล', 'autocomplete'=>'off']) !!}
                            {!! Form::password('password', ['required', 'class'=>'form-control input-lg', 'placeholder'=>'รหัสผ่าน']) !!}
                            @if ($errors->count() > 0)
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                            @endif
                            {!! Form::submit('เข้าสู่ระบบ', ['class'=>'btn btn-lg btn-primary btn-block']) !!}
                        {!! Form::close() !!}
                    </section>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    {!! Html::script(elixir('js/jquery.js')) !!}

    <!-- Bootstrap Core JavaScript -->
    {!! Html::script(elixir('js/bootstrap.js')) !!}

</body>

</html>
