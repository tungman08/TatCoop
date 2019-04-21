@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            คำนำหน้านาม
            <small>จัดการคำนำหน้านามที่ใช้ในระบบ</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'คำนำหน้านาม', 'link' => action('Admin\PrefixController@index')],
            ['item' => 'เพิ่ม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>คำนำหน้านาม</h4>
            <p>ให้ผู้ดูแลระบบสามารถเพิ่ม ลบ แก้ไข คำนำหน้านามที่ใช้ในระบบได้</p>
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
                <h3 class="box-title"><i class="fa fa-plus-circle"></i> เพิ่มคำนำหน้านาม</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['action' => 'Admin\PrefixController@store', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.prefix.form')
            {{ Form::close() }}
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection