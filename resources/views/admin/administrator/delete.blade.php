@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการผู้ดูแลระบบฯ
            <small>เพิ่ม ลบ แก้ไข บัญชีของผู้ดูแลระบบ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการผู้ดูแลระบบ', 'link' => '/admin/administrator'],
            ['item' => 'แก้ไขผู้ดูแลระบบ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="well">
            <h4>การลบผู้ใช้งานระบบ</h4>
            <p>ลบข้อมูลบัญชีผู้ใช้นี้ ออกจากฐานข้อมูล</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-trash fa-fw"></i> ลบบัญชีผู้ใช้งานระบบ</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                {{ Form::open(['route' => ['admin.administrator.destroy', $admins->id], 'method' => 'delete', 'role' => 'form']) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('email', 'บัญชีผู้ใช้งาน [' . $admins->email . ']') }}
                                    {{ Form::text('email', null, [
                                        'required',
                                        'class'=>'form-control', 
                                        'placeholder'=>'ยืนยันบัญชีผู้ใช้งานที่ต้องการลบ', 
                                        'autocomplete'=>'off']) }}
                                </div>
                            </div>
                            <!-- /.col-lg-6 (nested) -->

                        </div>
                        <!-- /.row (nested) -->
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        @if ($errors->count() > 0)
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                                {{ Html::ul($errors->all()) }}
                            </div>
                        @endif

                        {{ Form::button('<i class="fa fa-trash"></i> ลบ', [
                            'type' => 'submit', 
                            'class'=>'btn btn-danger',
                            'onclick'=>'javascript:return confirm("คุณต้องการลบบัญชีผู้ใช้นี้ ?");' ]) }}
                        {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
                            'class'=>'btn btn-default', 
                            'onclick'=>'javascript:history.go(-1);']) }}
                    </div>
                    <!-- /.box-footer -->
                {{ Form::close() }}
            </div>
        </div>
    </section>
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection