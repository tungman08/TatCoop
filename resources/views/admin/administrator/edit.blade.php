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
            ['item' => 'ข้อมูลผู้ดูแลระบบ', 'link' => ''],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>แก้ไขบัญชีผู้ดูแลระบบ</h4>
            <p>สามารถแก้ไขชื่อผู้ดูและระบบ และตั้งค่ารหัสผ่านใหม่ได้</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <!-- Horizontal Form -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">แก้ไขบัญชีผู้ดูแลระบบ</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($admins, ['route' => ['admin.administrator.update', $admins->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                {{ Form::hidden('admin_id', $admins->id, ['id'=>'admin_id']) }}

                @include('admin.administrator.form', ['edit' => true, 'id' => $admins->id])
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

    <!-- Custom JavaScript -->
    {!! Html::script(elixir('js/admin-form.js')) !!}
@endsection