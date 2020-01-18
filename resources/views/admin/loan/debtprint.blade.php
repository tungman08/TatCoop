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
                <div class="col-xs-12 text-center">
                    <h3 style="font-size: 15px; margin: 10px 0px;"><strong>สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</strong></h3><br>
                    <h3 style="font-size: 15px; margin: 0px;">หลักฐานการแสดงการชำระค่าหุ้นใน สอ.สรทท.</h3>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- info row -->
            <div class="row" style="margin-top: 30px;">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td colspan="3" style="border-top: none;">&nbsp;</td>
                                <th style="width: 25%; border-top: none;">เลขที่</th>
                            </tr>
                            <tr>
                                <th style="width: 15%; border-top: none;">ชื่อสมาชิก</th>
                                <td style="width: 45%; border-top: none;">{{ $member->profile->fullname }}</td>
                                <th style="width: 15%; border-top: none;">เลขทะเบียนสมาชิก</th>
                                <td style="border-top: none;">{{ $member->memberCode }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-xs-12 table-responsive" style="border: 1px solid #ddd; padding: 0px;">
                    <table class="table billing-table-bordered" style="margin-bottom: 0px;">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="7">หุ้น</th>
                                <th class="text-center" rowspan="3" style="width: 12%; vertical-align: middle;">หมายเหตุ</th>
                            </tr>
                            <tr>
                                <th class="text-center" rowspan="2" style="width: 12%; vertical-align: middle;">วันที่</th>
                                <th class="text-center" colspan="3">การถือหุ้น</th>
                                <th class="text-center" colspan="2">รวมหุ้นที่ถือ</th>
                                <th class="text-center" rowspan="2" style="width: 12%; vertical-align: middle;">ใบรับเลขที่</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 12%;">งวดที่</th>
                                <th class="text-center" style="width: 13%;">จำนวนหุ้น</th>
                                <th class="text-center" style="width: 13%;">จำนวนเงิน</th>
                                <th class="text-center" style="width: 13%;">จำนวนหุ้น</th>
                                <th class="text-center" style="width: 13%;">จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $forward = $shareholdings->filter(function ($value, $key) { return Diamond::parse($value->pay_date)->year < Diamond::today()->year; });
                                $presents = $shareholdings->filter(function ($value, $key) { return Diamond::parse($value->pay_date)->year == Diamond::today()->year; });
                            @endphp
                            <tr>
                                <td class="text-center">ยอดยกมา</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="text-center">{{ number_format($forward->sum('amount') / 10, 0, '.', ',') }}</td>
                                <td class="text-right">{{ number_format($forward->sum('amount'), 2, '.', ',') }}</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            @php
                                $count = 0;
                                $summary = $forward->sum('amount');
                            @endphp
                            @foreach ($presents->sortBy('pay_date') as $shareholding)
                                @php
                                    $count++;
                                    $summary += $shareholding->amount;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ Diamond::parse($shareholding->pay_date)->thai_format('j M y') }}</td>
                                    <td class="text-center">{{ number_format($forward->count() + $count, 0, '.', ',') }}</td>
                                    <td class="text-center">{{ number_format($shareholding->amount / 10, 0, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($shareholding->amount, 2, '.', ',') }}</td>
                                    <td class="text-center">{{ number_format($summary / 10, 0, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($summary, 2, '.', ',') }}</td>
                                    <td>&nbsp;</td>
                                    <td>{{ ($shareholding->shareholding_type_id == 2) ? (!empty($shareholding->remark)) ? $shareholding->remark : '&nbsp;' : '&nbsp;' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Officer row -->
            <div class="row" style="margin-top: 30px; margin-right: 0px; margin-bottom: 30px;">
                <div class="col-xs-6 col-xs-offset-6">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">ผู้รับเงิน/เหรัญญิก</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">&nbsp;</td>
                        </tr>
                    </table>      
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Box row -->
            <div class="row" style="margin-top: 15px; margin-left: 0px; margin-right: 0px;">
                <div class="col-lg-12 text-right" style="padding-top: 80px;">
                    <span>พิมพ์เอกสารวันที่: {{ Diamond::today()->thai_format('d M Y') }}</span>
                </div>
                <!-- /.col -->
            </div>
        </section>    
    </div>
    <!-- ./wrapper -->

    @foreach ($loans as $loan)
        <div style="page-break-before: always"></div>
        <div class="wrapper">
            <!-- Main content -->
            <section class="invoice">

                <!-- header row -->
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <h3 style="font-size: 15px; margin: 10px 0px;"><strong>สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</strong></h3><br>
                        <h3 style="font-size: 15px; margin: 0px;">หลักฐานการแสดงการชำระหนี้เงินกู้</h3>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- info row -->
                <div class="row" style="margin-top: 30px;">
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td colspan="3" style="border-top: none;">&nbsp;</td>
                                    <th style="width: 25%; border-top: none;">เลขที่</th>
                                </tr>
                                <tr>
                                    <th style="width: 15%; border-top: none;">ชื่อสมาชิก</th>
                                    <td style="width: 45%; border-top: none;">{{ $member->profile->fullname }}</td>
                                    <th style="width: 15%; border-top: none;">เลขทะเบียนสมาชิก</th>
                                    <td style="border-top: none;">{{ $member->memberCode }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- Table row -->
                <div class="row">
                    <div class="col-xs-12 table-responsive" style="border: 1px solid #ddd; padding: 0px;">
                        <table class="table billing-table-bordered" style="margin-bottom: 0px;">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="9">{{ $loan->loanType->name }}</th>
                                    <th class="text-center" rowspan="3" style="width: 10%; vertical-align: middle;">หมายเหตุ</th>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="2" style="width: 10%; vertical-align: middle;">วันที่</th>
                                    <th class="text-center" rowspan="2" style="width: 10%; vertical-align: middle;">หนังสือกู้ที่</th>
                                    <th class="text-center" rowspan="2" style="width: 10%; vertical-align: middle;">เงินกู้ (กู้เพิ่ม)</th>
                                    <th class="text-center" colspan="4">การชำระหนี้</th>
                                    <th class="text-center" rowspan="2" style="width: 10%; vertical-align: middle;">เงินกู้คงเหลือ</th>
                                    <th class="text-center" rowspan="2" style="width: 10%; vertical-align: middle;">ใบรับเลขที่</th>
                                </tr>
                                <tr>
                                    <th class="text-center" style="width: 10%;">งวดที่</th>
                                    <th class="text-center" style="width: 10%;">ชำระเงิน</th>
                                    <th class="text-center" style="width: 10%;">เงินต้น</th>
                                    <th class="text-center" style="width: 10%;">ดอกเบี้ย</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $forward = $loan->payments->filter(function ($value, $key) { return Diamond::parse($value->pay_date)->year < Diamond::today()->year; });
                                    $presents = $loan->payments->filter(function ($value, $key) { return Diamond::parse($value->pay_date)->year == Diamond::today()->year; });
                                @endphp
                                <tr>
                                    <td class="text-center">ยอดยกมา</td>
                                    <td class="text-center">{{ $loan->code }}</td>
                                    <td class="text-right">{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="text-right">{{ number_format($loan->outstanding - $forward->sum('principle'), 2, '.', ',') }}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                @php
                                    $count = 0;                    
                                    $balance = $loan->outstanding - $forward->sum('principle');
                                @endphp
                                @foreach ($presents->sortBy('pay_date') as $payment)
                                    @php
                                        $count++;
                                        $balance -= $payment->principle;
                                    @endphp  
                                    <tr>
                                        <td class="text-center">{{ Diamond::parse($payment->pay_date)->thai_format('j M y') }}</td>

                                        @if ($count == 1)
                                            <td class="text-center">{{ $loan->period }} งวด</td>
                                        @else
                                            <td>&nbsp;</td>
                                        @endif

                                        <td>&nbsp;</td>
                                        <td class="text-center">{{ $payment->period }}</td>
                                        <td class="text-right">{{ number_format($payment->principle + $payment->interest, 2, '.', ',') }}</td>
                                        <td class="text-right">{{ number_format($payment->principle, 2, '.', ',') }}</td>
                                        <td class="text-right">{{ number_format($payment->interest, 2, '.', ',') }}</td>
                                        <td class="text-right">{{ number_format($balance, 2, '.', ',') }}</td>
                                        <td>&nbsp;</td>
                                        <td>{{ ($payment->payment_method_id == 2) ? (!empty($payment->remark)) ? $payment->remark : '&nbsp;' : '&nbsp;' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- Officer row -->
                <div class="row" style="margin-top: 30px; margin-right: 0px; margin-bottom: 30px;">
                    <div class="col-xs-6 col-xs-offset-6">
                        <table style="width: 100%;">
                            <tr>
                                <td style="white-space: nowrap; width: 1%;">ผู้รับเงิน/เหรัญญิก</td>
                                <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">&nbsp;</td>
                            </tr>
                        </table>      
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- Box row -->
                <div class="row" style="margin-top: 15px; margin-left: 0px; margin-right: 0px;">
                    <div class="col-lg-12 text-right" style="padding-top: 80px;">
                        <span>พิมพ์เอกสารวันที่: {{ Diamond::today()->thai_format('d M Y') }}</span>
                    </div>
                    <!-- /.col -->
                </div>
            </section>
        </div>
        <!-- ./wrapper -->
    @endforeach
</body>
</html>