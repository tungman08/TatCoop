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
        <div class="form-group has-feedback">
            {{ Form::text('email', isset($email) ? $email : old('email'), ['required', 'class'=>'form-control', 'placeholder'=>'อีเมล', 'autocomplete'=>'off']) }}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::password('password', ['required', 'class'=>'form-control', 'placeholder'=>'รหัสผ่าน']) }}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::password('password_confirmation', ['required', 'class'=>'form-control', 'placeholder'=>'ยืนยันรหัสผ่าน']) }}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
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