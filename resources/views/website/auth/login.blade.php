@extends('website.auth.layout')

@section('content')
<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>บริการอิเล็กทรอนิกส์</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['url' => '/auth/login', 'role' => 'form']) }}
        <div class="form-group has-feedback">
            {{ Form::text('email', null, ['required', 'class'=>'form-control', 'placeholder'=>'อีเมล', 'autocomplete'=>'off']) }}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::password('password', ['required', 'class'=>'form-control', 'placeholder'=>'รหัสผ่าน']) }}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
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
                {{ Form::button('<i class="glyphicon glyphicon-log-in"></i>&nbsp; เข้าใช้งาน', ['type' => 'submit', 'class'=>'btn btn-primary btn-block btn-flat']) }}
            </div>
            <!-- /.col -->
        </div>
    {{ Form::close() }}

    <!-- registed flash session data -->
    @if (session('registed'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">×</button>
            <h4>ลงทะเบียนเสร็จสิ้น</h4>
            {{ session('registed') }}
        </div>
    @endif

    <!-- error messages -->
    @if ($errors->count() > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">×</button>
            <h4>การเข้าใช้งานผิดพลาด!</h4>
            {{ Html::ul($errors->all()) }}
        </div>
    @endif

    <a href="{{ url('/password/recovery') }}">ลืมรหัสผ่าน ?</a><br>
    <a href="{{ url('/auth/register') }}" class="text-center">ลงทะเบียนใช้บริการอิเล็กทรอนิกส์</a>
</div>
<!-- /.login-box-body -->
@endsection