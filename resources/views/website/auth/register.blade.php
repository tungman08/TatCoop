@extends('website.auth.layout')

@section('content')
<div class="login-box-body">
    <div class="login-logo">
        <img src="{{ asset('images/logo-coop.png') }}" class="img-circle img-responsive" alt="Co-op logo" />
        <b>ลงทะเบียนเข้าใช้งาน</b>
    </div>
    <!-- /.login-logo -->

    {{ Form::open(['url' => '/auth/register', 'role' => 'form']) }}
        <div class="form-group has-feedback">
            {{ Form::text('email', null, ['required', 'class'=>'form-control', 'placeholder'=>'อีเมล', 'autocomplete'=>'off']) }}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::password('password', ['required', 'class'=>'form-control', 'placeholder'=>'รหัสผ่าน']) }}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::password('password_confirmation', ['required', 'class'=>'form-control', 'placeholder'=>'ยืนยันรหัสผ่าน']) }}
            <span class="glyphicon glyphicon-flash form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::text('citizen_code', null, ['required', 'class'=>'form-control', 'placeholder'=>'เลขประจำตัวประชาชน',
                'data-inputmask'=>'\'mask\': \'9-9999-99999-99-9\',\'placeholder\':\'0\',\'removeMaskOnSubmit\':true',
                'data-mask',
                'autocomplete'=>'off']) }}
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {{ Form::text('member_id', null, ['required', 'id'=>'member_id', 'class'=>'form-control', 'placeholder'=>'หมายเลขสมาชิกสหกรณ์',
                'data-inputmask'=>'\'mask\': \'99999\',\'placeholder\':\'0\',\'removeMaskOnSubmit\':true',
                'data-mask',
                'autocomplete'=>'off']) }}
            <span class="glyphicon glyphicon-flash form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-7">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('terms', (1 or true), null, [
                            'onclick'=>'javascript:$("button:submit").attr("disabled", !this.checked);']) }}
                        ยินยอมตาม <a href="{{ url('/auth/terms')}}">ข้อกำหนด</a>
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-5">
                {{ Form::button('<i class="glyphicon glyphicon-plus"></i>&nbsp; ลงทะเบียน', ['type' => 'submit',
                    'disabled', 'class'=>'btn btn-primary btn-block btn-flat']) }}
            </div>
            <!-- /.col -->
        </div>
    {{ Form::close() }}

    <!-- error messages -->
    @if ($errors->count() > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-tooltip="true" title="Close">×</button>
            <h4>การลงทะเบียนผิดพลาด!</h4>
            {{ Html::ul($errors->all()) }}
        </div>
    @endif

    <a href="{{ url('/auth/login') }}">ลงทะเบียนไว้แล้ว</a><br>
</div>
<!-- /.login-box-body -->
@endsection
