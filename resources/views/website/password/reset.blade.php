@extends('website.auth.layout')

@section('content')
<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>ตั้งค่ารหัสผ่านใหม่</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['url' => '/password/reset', 'role' => 'form']) }}
        {{ Form::hidden('token', $token) }}
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                {{ Form::text('email', isset($email) ? $email : old('email'), ['required', 'class'=>'form-control', 'placeholder'=>'อีเมล', 'autocomplete'=>'off']) }}
            </div>
        </div>
        <div class="form-group has-feedback">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                {{ Form::password('password', ['id'=>'password', 'required', 'class'=>'form-control', 'placeholder'=>'รหัสผ่าน', 'style'=>'position: initial;']) }}
                <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#password"></span>
            </div>
        </div>
        <div class="form-group has-feedback">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                {{ Form::password('password_confirmation', ['id'=>'password_confirmation', 'required', 'class'=>'form-control', 'placeholder'=>'ยืนยันรหัสผ่าน', 'style'=>'position: initial;']) }}
                <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#password_confirmation"></span>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-7">
            </div>
            <!-- /.col -->
            <div class="col-xs-5">
                {{ Form::button('<i class="glyphicon glyphicon-refresh margin-r-sm"></i>ตั้งค่าใหม่', ['type' => 'submit',
                    'class'=>'btn btn-primary btn-block btn-flat']) }}
            </div>
            <!-- /.col -->
        </div>
    {{ Form::close() }}

    <!-- error messages -->
    @if ($errors->count() > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>เกิดข้อผิดพลาด!</h4>
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
