@extends('website.member.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        รหัสผ่าน
        <small>เปลี่ยนรหัสผ่าน</small>
    </h1>

   <ol class="breadcrumb">
        <li><a href="{{ action('Website\MemberController@index') }}"><i class="fa fa-home"></i> หน้าหลัก</a></li>
        <li><a href="{{ action('Website\ProfileController@getIndex') }}">ข้อมูลสมาชิก</a></li>
        <li class="active">เปลี่ยนรหัสผ่าน</li>
    </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        @if (!$user->confirmed)
            <div class="callout callout-warning">
                <h4>คำแนะนำ!</h4>
                <p>เนื่องจากรหัสผ่านที่ท่านใช้ ณ ตอนนี้ เป็นรหัสผ่านที่ถูกสร้างขึ้นจากระบบ กรุณาเปลี่ยนรหัสผ่านใหม่เพื่อความปลอดภัยของข้อมูลที่ท่านรับผิดชอบ</p>
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">เปลี่ยนรหัสผ่าน</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Close" onclick="javascript:location.href='{{ action('Website\ProfileController@getIndex') }}';">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-title -->

            {{ Form::open(['action' => 'Website\ProfileController@getPassword', 'method' => 'post', 'role' => 'form']) }}
                <div class="box-body">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-lg-3 align-center">
                                <img class="profile-user-img img-circle img-responsive" style="width: 200px; height: 200px; margin-bottom: 30px;" src="{{ asset('images/user.png') }}" alt="User Pic">
                            </div>
                            <div class=" col-md-9 col-lg-9 ">
                                <div class="form-group has-feedback">
                                    {{ Form::label('password', 'รหัสผ่านปัจจุบัน') }}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        {{ Form::password('password', ['id'=>'password', 'required', 'class'=>'form-control', 'placeholder'=>'Current Password', 'style'=>'position: initial;']) }}
                                        <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#password"></span>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    {{ Form::label('new_password', 'รหัสผ่านใหม่') }}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        {{ Form::password('new_password', ['id'=>'new_password', 'required', 'class'=>'form-control', 'placeholder'=>'New Password', 'style'=>'position: initial;']) }}
                                        <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#new_password"></span>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    {{ Form::label('new_password_confirmation', 'ยืนยันรหัสผ่านใหม่') }}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        {{ Form::password('new_password_confirmation', ['id'=>'new_password_confirmation', 'required', 'class'=>'form-control', 'placeholder'=>'Confirm Password', 'style'=>'position: initial;']) }}
                                        <span class="glyphicon glyphicon-eye-open form-control-feedback toggle-password" toggle="#new_password_confirmation"></span>
                                    </div>
                                </div>
                                @if ($errors->count() > 0)
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                                        <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                                        {{ Html::ul($errors->all()) }}
                                    </div>
                                @endif
                                <hr />
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3" align="center">
                                            {{ Form::submit('เปลี่ยนรหัสผ่าน', ['class'=>'btn btn-primary btn-block']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>          
                <!-- /.box-body-->

            {{ Form::close() }}
             
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
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