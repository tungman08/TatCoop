@extends('website.auth.layout')

@section('content')
<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>บริการอิเล็กทรอนิกส์</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['action' => 'Website\AuthController@postLogin', 'method'=>'post', 'role' => 'form']) }}
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                {{ Form::text('email', old('email'), ['required', 'class'=>'form-control', 'placeholder'=>'อีเมล', 'autocomplete'=>'off']) }}
            </div>
        </div>
        <div class="form-group has-feedback">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                {{ Form::password('password', ['id'=>'password', 'required', 'class'=>'form-control', 'placeholder'=>'รหัสผ่าน', 'style'=>'position: initial;']) }}
                <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#password"></span>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-7">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('remember', (1 or true), null) }} จดจำฉันไว้ในระบบ
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-5">
                {{ Form::button('เข้าใช้งาน<i class="glyphicon glyphicon-log-in margin-l-sm"></i>', ['type' => 'submit', 'class'=>'btn btn-primary btn-block btn-flat']) }}
            </div>
            <!-- /.col -->
        </div>
    {{ Form::close() }}

    <!-- verified flash session data -->
    @if (session('verified'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>ยืนยันอีเมลเรียบร้อย</h4>
            {{ session('verified') }}
        </div>
    @endif

   <!-- recovery password flash session data -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>การตั้งค่ารหัสผ่านใหม่</h4>
            {{ session('status') }}
        </div>
    @endif 

    <!-- error flash session data -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>เกิดข้อผิดพลาด!</h4>
            {{ session('error') }}
        </div>
    @endif

    <!-- error messages -->
    @if ($errors->count() > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>การเข้าใช้งานผิดพลาด!</h4>
            {{ Html::ul($errors->all()) }}
        </div>
    @endif

    <a href="{{ action('Website\PasswordController@getEmail') }}">ลืมรหัสผ่าน ?</a><br>
    <a href="{{ action('Website\AuthController@getRegister') }}" class="text-center">ลงทะเบียนใช้บริการอิเล็กทรอนิกส์</a>
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
