@extends('website.auth.layout')

@section('content')
<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>ลืมรหัสผ่าน</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['url' => '/password/email', 'role' => 'form']) }}
        <div class="form-group has-feedback">
            {{ Form::text('email', null, ['required', 'class'=>'form-control', 'placeholder'=>'อีเมลที่ใช้ลงทะเบียน', 'autocomplete'=>'off']) }}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-7">
            </div>
            <!-- /.col -->
            <div class="col-xs-5">
                {{ Form::button('ส่งอีเมล<i class="glyphicon glyphicon-send margin-l-sm"></i>', ['type' => 'submit',
                    'class'=>'btn btn-primary btn-block btn-flat']) }}
            </div>
            <!-- /.col -->
        </div>
    {{ Form::close() }}

    <!-- recovery password flash session data -->
    @if (session('status'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">&times;</button>
            <h4>การตั้งค่ารหัสผ่านใหม่</h4>
            {{ session('status') }}
        </div>
    @endif

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
