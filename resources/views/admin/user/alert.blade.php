@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อความแจ้งเตือน
        <small>ข้อความแจ้งเตือนทั้งหมด</small>
    </h1>

    @include('admin.user.breadcrumb', ['breadcrumb' => 'ข้อความแจ้งเตือน'])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->


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