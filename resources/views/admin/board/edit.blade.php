@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการคณะกรรมการ
            <small>เพิ่ม ลบ แก้ไข บัญชีของคณะกรรมการ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการคณะกรรมการ', 'link' => action('Admin\BoardController@index')],
            ['item' => 'ข้อมูลคณะกรรมการ', 'link' => action('Admin\BoardController@show', ['id'=>$board->id])],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>แก้ไขบัญชีคณะกรรมการ</h4>
            <p>สามารถแก้ไขชื่อคณะกรรมการ และตั้งค่ารหัสผ่านใหม่ได้</p>
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
                <h3 class="box-title">แก้ไขบัญชีคณะกรรมการ</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($board, ['action' => ['Admin\BoardController@update', $board->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                {{ Form::hidden('board_id', $board->id, ['id'=>'board_id']) }}

                @include('admin.board.form', ['edit' => true, 'id' => $board->id])
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