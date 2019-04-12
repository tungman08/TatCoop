<!-- Main content -->
<section class="invoice">

    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-file-text-o"></i> การชำระหนี้{{ $loan->loanType->name }}
                <small class="pull-right">วันที่: {{ Diamond::today()->thai_format('d M Y') }}</small>
            </h2>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- header row -->
    <div class="row">
        <div class="col-xs-12 text-center">
            <h3 style="font-size: 18px; margin: 5px 0px;"><strong>สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</strong></h3><br>
            <h3 style="font-size: 18px; margin: 0px;">หลักฐานการแสดงการชำระหนี้เงินกู้</h3>
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
                            <td class="text-center">{{ Diamond::parse($payment->pay_date)->thai_format('j M Y') }}</td>

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
                            <td>&nbsp;</td>
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
</section>
<!-- /.invoice -->