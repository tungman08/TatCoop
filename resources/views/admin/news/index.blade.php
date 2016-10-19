@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการข่าวประชาสัมพันธ์
        <small>การจัดการข่าวประชาสัมพันธ์ของ สอ.สรทท.</small>
    </h1>

    @include('admin.news.breadcrumb')

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข่าวประชาสัมพันธ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ข่าวประชาสัมพันธ์</p>
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