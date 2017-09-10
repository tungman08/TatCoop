@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการใบรับเงินค่าหุ้น
            <small>รายละเอียดข้อมูลใบเสร็จรับเงินค่าหุ้นของสมาชิก</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการใบรับเงินค่าหุ้น', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>จัดการใบรับเงินค่าหุ้น</h4>
            <p>ให้ผู้ดูแลระบบสามารถ แก้ไข ชื่อผู้จัดการและเหรัญญิกได้</p>
        </div>

        @if(Session::has('flash_message'))
            <div class="callout {{ Session::get('callout_class') }}">
                <h4>แจ้งข้อความ!</h4>
                <p>
                    {{ Session::get('flash_message') }}

                    @if(Session::has('flash_link'))
                        <a href="{{ Session::get('flash_link') }}">Undo</a>
                    @endif
                </p>
            </div>
        @endif

        <!-- Main content -->
        <section class="invoice">

            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <i class="fa fa-file-text-o"></i> ตัวอย่างใบรับเงินค่าหุ้น
                        <small class="pull-right">วันที่: {{ Diamond::today()->thai_format('d M Y') }}</small>
                    </h2>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- header row -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-2">
                            <img src="{{ asset('images/logo-coop.png') }}" style="width: 100px; height: 100px;" alt="tat-logo" />
                        </div>
                        <div class="col-xs-10 text-center">
                            <h3 style="font-size: 18px; margin: 5px 0px;"><strong>สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</strong></h3><br>
                            <h3 style="font-size: 18px; margin: 0px;">ใบรับเงินค่าหุ้น</h3>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- info row -->
            <div class="row" style="margin-top: 5px;">
                <div class="col-xs-6">
                    <div class="table-responsive" style="border: 1px solid #ddd;">
                        <table class="table table-bordered" style="margin-bottom: 0px;">
                            <tr>
                                <th style="width:20%">เลขที่:</th>
                                <td>000000000000</td>
                            </tr>
                            <tr>
                                <th>ได้รับเงินจาก:</th>
                                <td>ร.ต.อ.วศิน มีปรีชา</td>
                            </tr>
                            <tr>
                                <th>หน่วยงาน:</th>
                                <td>สหกรณ์การท่องเที่ยว</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-6">
                    <div class="table-responsive" style="border: 1px solid #ddd;">
                        <table class="table table-bordered" style="margin-bottom: 0px;">
                            <tr>
                                <th style="width:20%">วันที่:</th>
                                <td>{{ Diamond::today()->thai_format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>เลขทะเบียน:</th>
                                <td>00000</td>
                            </tr>
                            <tr>
                                <th>ทุนเรือนหุ้นสะสม:</th>
                                <td>0.00</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row" style="margin: 20px 0px;">
                <div class="col-xs-12 table-responsive" style="border: 1px solid #ddd; padding: 0px;">
                    <table class="table billing-table-bordered" style="margin-bottom: 0px;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40%">รายการ/สัญญา</th>
                                <th class="text-center" style="width: 10%">เดือนที่</th>
                                <th class="text-center" style="width: 25%">จำนวนเงิน</th>
                                <th class="text-center" style="width: 25%">ทุนเรือนหุ้นสะสม</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="2">ศุนย์บาทถ้วน</td>
                                <td class="text-right">0.00</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td>รับเงินค่าหุ้น</td>
                                <td class="text-center">{{ Diamond::today()->thai_format('m/y') }}</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Officer row -->
            <div class="row" style="margin-top: 20px; margin-right: 0px;">
                <div class="col-xs-6">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">ผู้จัดการ</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">{{ !empty($billing) ? $billing->manager : '-' }}</td>
                        </tr>
                    </table>
                </div>
                <!-- /.col -->
                <div class="col-xs-6">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">เหรัญญิก</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">{{ !empty($billing) ? $billing->treasurer : '-' }}</td>
                        </tr>
                    </table>      
                </div>
                <!-- /.col -->
            </div>

            <!-- Box row -->
            <div class="row" style="margin-top: 15px; margin-left: 0px; margin-right: 0px;">
                <div class="col-lg-12" style="height: 80px; border: 1px solid #ddd; background-color: #fcfcfc;">
                </div>
                <!-- /.col -->
                <div class="col-lg-12 text-center" style="padding-top: 20px;">
                    @if (empty($billing))
                        <button class="btn btn-primary btn-flat" type="button" data-tooltip="true" title="เพิ่มชื่อผู้จัดการ/เหรัญญิก"
                            onclick="javascript:window.location.href='{{ url('/admin/billing/create') }}';">
                            <i class="fa fa-plus-circle"></i> เพิ่มชื่อผู้จัดการ/เหรัญญิก
                        </button>
                    @else
                        <button class="btn btn-primary btn-flat" type="button" data-tooltip="true" title="แก้ไขชื่อผู้จัดการ/เหรัญญิก"
                            onclick="javascript:window.location.href='{{ url('/admin/billing/' . $billing->id . '/edit') }}';">
                            <i class="fa fa-edit"></i> แก้ไขชื่อผู้จัดการ/เหรัญญิก
                        </button>
                    @endif
                </div>
                <!-- /.col -->
            </div>
        </section>
        <!-- /.content -->
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