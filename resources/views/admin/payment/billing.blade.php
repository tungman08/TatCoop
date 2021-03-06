@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => action('Admin\LoanController@getMember')],
            ['item' => 'การกู้ยืม', 'link' => action('Admin\LoanController@index', ['member_id'=>$member->id])],
            ['item' => 'สัญญากู้ยืม', 'link' => action('Admin\LoanController@show', ['member_id'=>$member->id, 'id'=>$loan->id])],
            ['item' => 'รายการผ่อนชำระ', 'link' => action('Admin\PaymentController@show', ['loan_id'=>$loan->id, 'id'=>$payment->id])],
            ['item' => 'ใบเสร็จรับเงิน', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ใบรับเงินค่างวด</h4>
            <p>ใบรับเงินค่างวดเดือน {{ $date->thai_format('F Y') }} ของ {{ $member->profile->fullname }}</p>
            
            <!-- this row will not appear when printing -->
            <div class="row no-print" style="margin-top: 30px;">
                <div class="col-xs-12">
                    <a href="{{ action('Admin\PaymentController@getPrintBilling', ['payment_id'=>$payment->id, 'paydate'=>$date->format('Y-n-j')]) }}" target="_blank" class="btn btn-default btn-flat"><i class="fa fa-print"></i> พิมพ์</a>
                    <button type="button"
                        class="btn btn-primary btn-flat pull-right"
                        style="margin-right: 5px;"
                        onclick="javascript:document.location.href  = '{{ action('Admin\PaymentController@getPdfBilling', ['payment_id'=>$payment->id, 'paydate'=>$date->format('Y-n-j')]) }}';">
                        <i class="fa fa-download"></i> บันทึกเป็น PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="invoice">

            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <i class="fa fa-file-text-o"></i> ใบรับเงินค่างวด
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
                            <h3 style="font-size: 18px; margin: 0px;">ใบรับเงินค่างวด {{ $loan->loanType->name }}</h3>
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
                                <td>{{ $billno }}</td>
                            </tr>
                            <tr>
                                <th>ได้รับเงินจาก:</th>
                                <td>{{ $member->profile->fullname }}</td>
                            </tr>
                            <tr>
                                <th>หน่วยงาน:</th>
                                <td>สอ.สรทท.</td>
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
                                <td>{{ $date->thai_format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>เลขทะเบียน:</th>
                                <td>{{ str_pad($member->member_code, 5, "0", STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <th>วงเงินที่กู้:</th>
                                <td>{{ number_format($loan->outstanding, 2,'.', ',') }}</td>
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
                                <th class="text-center" style="width: 25%">เงินต้นคงเหลือ</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="2">{{ Number::toBaht($payment->principle + $payment->interest) }}</td>
                                <td class="text-right">{{ number_format($payment->principle + $payment->interest, 2,'.', ',') }}</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td>สัญญาเงินกู้เลขที่ {{ $loan->code }}</td>
                                <td class="text-center">{{ $date->thai_format('m/y') }}</td>
                                <td class="text-right">
                                    <table class="table table-borderless" style="margin-bottom: 0px;">
                                        <tr>
                                            <td class="text-left">เงินต้น</td>
                                            <td>{{ number_format($payment->principle, 2,'.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">ดอกเบี้ย</td>
                                            <td>{{ number_format($payment->interest, 2,'.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="border-top: 1px solid #f4f4f4;">รวม</td>
                                            <td style="border-top: 1px solid #f4f4f4;">{{ number_format($payment->principle + $payment->interest, 2,'.', ',') }}</td>
                                        </tr>
                                    </table>                                   
                                </td>
                                <td class="text-right">{{ number_format($loan->outstanding - $loan->payments->sum('principle'), 2,'.', ',') }}</td>
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
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">{{ $billing->manager }}</td>
                        </tr>
                    </table>
                </div>
                <!-- /.col -->
                <div class="col-xs-6">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">เหรัญญิก</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">{{ $billing->treasurer }}</td>
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
                    <span>ใบรับเงินประจำเดือนจะสมบูรณ์ต่อเมื่อสหกรณ์ได้รับเงินที่เรียกเก็บครบถ้วนแล้ว</span>
                </div>
                <!-- /.col -->
            </div>
        </section>
        <!-- /.content -->
    </section>
    <!-- /.content -->
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection