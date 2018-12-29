<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>:: สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด ::</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    @section('styles')
        <!-- Bootstrap Core CSS -->
        {{ Html::style(elixir('css/bootstrap.css')) }}

        <!-- Theme style -->
        {{ Html::style(elixir('css/admin-lte.css')) }}

        <style>
            .table-borderless > tbody > tr > td,
            .table-borderless > tbody > tr > th,
            .table-borderless > tfoot > tr > td,
            .table-borderless > tfoot > tr > th,
            .table-borderless > thead > tr > td,
            .table-borderless > thead > tr > th {
                border: none;
                padding: 0px 0px 8px 0px;
            }
        </style>
    @show

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body onload="window.print();">
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice">

            <!-- header row -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-2">
                            <img src="{{ asset('images/logo-coop.png') }}" style="width: 100px; height: 100px;" alt="tat-logo" />
                        </div>
                        <div class="col-xs-10 text-center">
                            <h3 style="font-size: 15px; margin: 10px 0px;"><strong>สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</strong></h3><br>
                            <h3 style="font-size: 15px; margin: 0px;">ใบรับเงินค่างวด {{ $loan->loanType->name }}</h3>
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
                                <td>{{ $member->profile->fullName }}</td>
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
                <div class="col-lg-12 text-right" style="padding-top: 80px;">
                    <span>พิมพ์เอกสารวันที่: {{ Diamond::today()->thai_format('d M Y') }}</span>
                </div>
                <!-- /.col -->
            </div>

        </section>
    </div>
    <!-- ./wrapper -->
</body>
</html>