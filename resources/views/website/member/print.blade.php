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
                            <h3 style="font-size: 18px; margin: 5px 0px;"><strong>สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</strong></h3><br>
                            <h3 style="font-size: 18px; margin: 0px;">ใบเสร็จรับเงินค่าหุ้น</h3>
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
                                <th class="block">เลขที่:</th>
                                <td class="block">{{ $billno }}</td>
                            </tr>
                            <tr>
                                <th class="block">ได้รับเงินจาก:</th>
                                <td class="block">{{ $member->profile->fullName }}</td>
                            </tr>
                            <tr>
                                <th class="block">หน่วยงาน:</th>
                                <td class="block">สหกรณ์การท่องเที่ยว</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-6">
                    <div class="table-responsive" style="border: 1px solid #ddd;">
                        <table class="table table-bordered" style="margin-bottom: 0px;">
                            <tr>
                                <th class="block">วันที่:</th>
                                <td class="block">{{ $date->thai_format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th class="block">เลขทะเบียน:</th>
                                <td class="block">{{ str_pad($member->member_code, 5, "0", STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <th class="block">ทุนเรือนหุ้นสะสม:</th>
                                <td class="block">{{ number_format($total_shareholding, 2,'.', ',') }}</td>
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
                                <td colspan="2">{{ Number::toBaht($shareholdings->sum('amount')) }}</td>
                                <td class="text-right">{{ number_format($shareholdings->sum('amount'), 2,'.', ',') }}</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tfoot>
                        <tbody>
                            @eval($result = $total_shareholding - $shareholdings->sum('amount'))
                            @foreach($shareholdings as $share)
                                @eval($result += $share->amount)
                                <tr>
                                    <td>รับ{{ $share->shareholding_type->name }}</td>
                                    <td class="text-center">{{ $date->thai_format('m/y') }}</td>
                                    <td class="text-right">{{ number_format($share->amount, 2,'.', ',') }}</td>
                                    <td class="text-right">{{ number_format($result, 2,'.', ',') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Officer row -->
            <div class="row" style="margin-top: 20px; margin-right: 0px;">
                <div class="col-xs-6" style="white-space: nowrap; overflow-x: hidden;">
                    ผู้จัดการ <span style="color: #bbb;">....................................................................................................................................................................................................................</span>
                </div>
                <!-- /.col -->
                <div class="col-xs-6" style="white-space: nowrap; overflow-x: hidden;">
                    เจ้าหน้าที่ผู้รับเงิน <span style="color: #bbb;">....................................................................................................................................................................................................................</span>          
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
    </div>
    <!-- ./wrapper -->
</body>
</html>