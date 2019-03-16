@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            บัญชีผู้ใช้งานระบบฯ
            <small>รายชื่อบัญชีของสมาชิกที่ใช้งานระบบบริการอิเล็กทรอนิกส์</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'บัญชีผู้ใช้งานระบบ', 'link' => '/admin/account'],
            ['item' => $user->member->profile->fullname, 'link' => '/admin/account/' . $user->member->id ],
            ['item' => 'แก้ไขบัญชีผู้ใช้', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>บัญชีผู้ใช้งานระบบบริการอิเล็กทรอนิกส์</h4>
            <p>แสดงรายชื่อบัญชีของสมาชิกที่ได้ลงทะเบียนเข้าใช้งานระบบบริการอิเล็กทรอนิกส์</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-edit"></i> แก้ไขบัญชีผู้ใช้งานระบบบริการอิเล็กทรอนิกส์</h3>
            </div>
            <!-- /.box-header -->

            {{ Form::model($user, ['route' => ['admin.account.update', $user->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('email', 'อีเมลบัญชีผู้ใช้เดิม', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('email', $user->email, [
                                'placeholder' => 'ตัวอย่าง: user@email.com',
                                'autocomplete'=>'off',
                                'readonly'=>true,
                                'class'=>'form-control'])
                            }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('new_email', 'อีเมลบัญชีผู้ใช้ใหม่', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('new_email', null, [
                                'placeholder' => 'ตัวอย่าง: user@email.com',
                                'autocomplete'=>'off',
                                'class'=>'form-control'])
                            }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('new_email_confirmation', 'ยืนยันอีเมลบัญชีผู้ใช้ใหม่', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('new_email_confirmation', null, [
                                'placeholder' => 'ตัวอย่าง: user@email.com',
                                'autocomplete'=>'off',
                                'class'=>'form-control'])
                            }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('remark', 'หมายเหตุ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::textarea('remark', null, [
                                'placeholder' => 'สาเหตุที่ต้องเปลี่ยน',
                                'class'=>'form-control textarea'])
                            }} 
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
                        'id'=>'save',
                        'type' => 'submit', 
                        'class'=>'btn btn-primary btn-flat'])
                    }}
                    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
                        'class'=>'btn btn-default btn-flat', 
                        'onclick'=> 'javascript:history.go(-1);'])
                    }}
                </div>
                <!-- /.box-footer -->
            {{ Form::close() }}
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection