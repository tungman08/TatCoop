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
            ['item' => $prefix->name, 'link' => ''],
            ['item' => 'แก้ไข', 'link' => ''],
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
                <h3 class="box-title"><i class="fa fa-pencil"></i> แก้ไขคำนำหน้านาม</h3>

                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-danger btn-xs"
                        onclick="javascript:var result = confirm('คุณต้องการลบรายการนี้ใช่ไหม ?'); if (result) { $('#delete_form').submit(); }">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($prefix, ['action' => ['Admin\PrefixController@update', $prefix->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                @include('admin.prefix.form')
            {{ Form::close() }}
        </div>
        <!-- /.box -->

        {{ Form::open(['action' => ['Admin\PrefixController@destroy', $prefix->id], 'id' => 'delete_form', 'method' => 'delete']) }}
        {{ Form::close() }}
    </section>
    <!-- /.content -->
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection