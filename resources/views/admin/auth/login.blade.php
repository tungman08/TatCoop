@extends('admin.auth.layout')

@section('content')
<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>Administrator</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['url' => '/auth/login', 'role' => 'form']) }}
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                {{ Form::text('email', null, ['required', 'class'=>'form-control', 'placeholder'=>'E-mail', 'autocomplete'=>'off']) }}
            </div>
        </div>
        <div class="form-group has-feedback">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                {{ Form::password('password', ['id'=>'password', 'required', 'class'=>'form-control', 'placeholder'=>'Password', 'style'=>'position: initial;']) }}
                <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#password"></span>
            </div>
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
                {{ Form::button('Sign In&nbsp; <i class="glyphicon glyphicon-log-in"></i>', [
                    'type' => 'submit', 
                    'class'=>'btn btn-primary btn-block btn-flat']) 
                }}
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

@section('styles')
    @parent

    <style>
        .toggle-password {
            cursor: pointer;
            pointer-events: auto;
            color: #777;
        }
    </style>
@endsection

@section('scripts')
    @parent

    <script>
    $(document).ready(function () {
        $(".toggle-password").click(function() {
            $(this).toggleClass("glyphicon-eye-open glyphicon-eye-close");

            var input = $($(this).attr("toggle"));

            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } 
            else {
                input.attr("type", "password");
            }
        });
    });   
    </script>
@endsection
