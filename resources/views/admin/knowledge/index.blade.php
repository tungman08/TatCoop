@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสาระน่ารู้
        <small>การจัดการสาระน่ารู้ในหน้าเว็บไซต์ สอ.สรทท.</small>
    </h1>

    @include('admin.knowledge.breadcrumb')

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการสาระน่ารู้</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข สาระน่ารู้</p>
        </div>

        <!-- Main row -->
        <div class="row">

        </div>
        <!-- /.row -->
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