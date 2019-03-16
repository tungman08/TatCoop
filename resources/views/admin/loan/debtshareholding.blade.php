<!-- Main content -->
<section class="invoice">

    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-file-text-o"></i> การชำระเงินค่าหุ้น
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
            <h3 style="font-size: 18px; margin: 0px;">หลักฐานการแสดงการชำระค่าหุ้นใน สอ.สรทท.</h3>
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
                            <td class="text-center">{{ Diamond::parse($shareholding->pay_date)->thai_format('j M Y') }}</td>
                            <td class="text-center">{{ number_format($forward->count() + $count, 0, '.', ',') }}</td>
                            <td class="text-center">{{ number_format($shareholding->amount / 10, 0, '.', ',') }}</td>
                            <td class="text-right">{{ number_format($shareholding->amount, 2, '.', ',') }}</td>
                            <td class="text-center">{{ number_format($summary / 10, 0, '.', ',') }}</td>
                            <td class="text-right">{{ number_format($summary, 2, '.', ',') }}</td>
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