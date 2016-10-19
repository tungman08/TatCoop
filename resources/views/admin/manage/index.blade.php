@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        สรุปข้อมูลของ สอ.สรทท.
        <small>สรุปข้อมูลรายละเอียดของ สอ.สรทท.</small>
    </h1>

    @include('admin.manage.breadcrumb')

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">จำนวนสมาชิกปัจจุบัน</span>
                        <span class="info-box-number">{{ number_format($member_amount, 0, '.', ',') }} คน</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-baht"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">ยอดหุ้นรวม ณ {{ Diamond::parse(App\ShareHolding::max('pay_date'))->thai_format('F Y') }}</span>
                        <span class="info-box-number">{{ number_format($member_shareholding, 2, '.', ',') }} บาท</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-baht"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">ผลประกอบปี {{ Diamond::today()->thai_format('Y') }}</span>
                        <span class="info-box-number">#,### บาท</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-baht"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">กำไรปี {{ Diamond::today()->thai_format('Y') }}</span>
                        <span class="info-box-number">#,### บาท</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

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