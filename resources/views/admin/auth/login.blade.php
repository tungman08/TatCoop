@extends('website.auth.layout')

@section('content')
<div class="login-logo">
    <a href="{{ url(env('APP_URL', 'http://www.tatcoop.dev')) }}"><b>TAT Coopperative</b></a>
</div>

<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>Administrator</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['url' => '/auth/login', 'role' => 'form']) }}
        <div class="form-group has-feedback">
            {{ Form::text('email', null, ['required', 'class'=>'form-control', 'placeholder'=>'E-mail', 'autocomplete'=>'off']) }}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::password('password', ['required', 'class'=>'form-control', 'placeholder'=>'Password']) }}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('remember', (1 or true), null) }} Remember Me
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                {{ Form::button('<i class="glyphicon glyphicon-log-in"></i>&nbsp; Sign In', ['type' => 'submit', 'class'=>'btn btn-primary btn-block btn-flat']) }}
            </div>
            <!-- /.col -->
        </div>
    {{ Form::close() }}

    @if ($errors->count() > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">×</button>
            <h4>Authentication failed!</h4>
            {{ Html::ul($errors->all()) }}
        </div>
    @endif

</div>
<!-- /.login-box-body -->
@endsection
