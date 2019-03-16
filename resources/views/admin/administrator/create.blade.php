@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเจ้าหน้าที่สหกรณ์
            <small>เพิ่ม ลบ แก้ไข บัญชีของเจ้าหน้าที่สหกรณ์ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเจ้าหน้าที่สหกรณ์', 'link' => '/admin/administrator'],
            ['item' => 'เพิ่ม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>เพิ่มบัญชีเจ้าหน้าที่สหกรณ์</h4>
            <p>เมื่อกดปุ่มบันทึกข้อมูลแล้ว ระบบจะส่งรหัสผ่านไปถึง e-mail ที่ป้อนเพื่อแจ้งเจ้าหน้าที่สหกรณ์คนใหม่ให้ทราบ</p>
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
                <h3 class="box-title">เพิ่มบัญชีเจ้าหน้าที่สหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/admin/administrator', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.administrator.form', ['edit' => false])
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