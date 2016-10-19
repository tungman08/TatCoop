@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลการค้ำประกัน
        <small>รายละเอียดข้อมูลทุนเรือนหุ้นของสมาชิก</small>
    </h1>
    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'ข้อมูลสมาชิก', 'link' => '/member'],
        ['item' => 'การค้ำประกัน', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลการค้ำประกัน</h4>
            <p>แสดงการค้ำประกันการกู้ยืม ของ {{ $member->profile->fullName }}</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลการค้ำประกัน</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                ข้อมูลการค้ำประกัน
            </div>
            <!-- /.box-body -->
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