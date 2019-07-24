<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <!-- Theme style -->
    {{ Html::style(elixir('css/admin-lte.css')) }}

    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ asset('fonts/THSarabunNew.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ asset('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: normal;
            src: url("{{ asset('fonts/THSarabunNew-Italic.ttf') }}") format('truetype');
        }    

        @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: bold;
            src: url("{{ asset('fonts/THSarabunNew-BoldItalic.ttf') }}") format('truetype');
        }

        * {
            font-family: "THSarabunNew";
            font-size: 16px;
        }

        h3 {
            line-height: 0.6;
        }

        table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        .table-bordered {
            border: 2px solid #ddd;
        }

        .table tr th {
            background-color: #fcfcfc;
            font-style: bold;    
        }

        .table tr th, .table tr td {
            padding: 3px 8px;
        }

        .table tr th {
            vertical-align: middle;
        }

        .table tr td {
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div style="background: #fff; border: 1px solid #f4f4f4; padding: 20px;">
        <table>
            <tr>
                <td style="text-align: center;">
                    <h3 style="font-size: 24px; font-weight: bold;">สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</h3>
                    <h3 style="font-size: 24px;">หลักฐานการแสดงการชำระค่าหุ้นใน สอ.สรทท.</h3>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 10px;">
                    <table>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <th style="width: 25%;">เลขที่</th>
                        </tr>
                        <tr>
                            <th style="width: 15%;">ชื่อสมาชิก</th>
                            <td style="width: 45%;">{{ $member->profile->fullname }}</td>
                            <th style="width: 15%; ">เลขทะเบียนสมาชิก</th>
                            <td>{{ $member->memberCode }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table table-bordered">
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
                                    <td>{{ ($shareholding->shareholding_type_id == 2) ? (!empty($shareholding->remark)) ? $shareholding->remark : '&nbsp;' : '&nbsp;' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 20px; padding-left: 50%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">ผู้รับเงิน/เหรัญญิก</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">&nbsp;</td>
                        </tr>
                    </table> 
                </td>
            </tr>
            <tr>
                <td style="padding-top: 80px; text-align: right;">พิมพ์เอกสารวันที่: {{ Diamond::today()->thai_format('d M Y') }}</td>
            </tr>
        </table>
    </div>

    @foreach ($loans as $loan)
        <div style="page-break-before: always"></div>
        <div style="background: #fff; border: 1px solid #f4f4f4; padding: 20px;">
            <table>
                <tr>
                    <td style="text-align: center;">
                        <h3 style="font-size: 24px; font-weight: bold;">สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</h3>
                        <h3 style="font-size: 24px;">หลักฐานการแสดงการชำระหนี้เงินกู้</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                                <th style="width: 25%;">เลขที่</th>
                            </tr>
                            <tr>
                                <th style="width: 15%;">ชื่อสมาชิก</th>
                                <td style="width: 45%;">{{ $member->profile->fullname }}</td>
                                <th style="width: 15%; ">เลขทะเบียนสมาชิก</th>
                                <td>{{ $member->memberCode }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <table class="table table-bordered">
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
                                        <td>{{ ($payment->payment_method_id == 2) ? (!empty($payment->remark)) ? $payment->remark : '&nbsp;' : '&nbsp;' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>                    
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px; padding-left: 50%;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="white-space: nowrap; width: 1%;">ผู้รับเงิน/เหรัญญิก</td>
                                <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">&nbsp;</td>
                            </tr>
                        </table> 
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 80px; text-align: right;">พิมพ์เอกสารวันที่: {{ Diamond::today()->thai_format('d M Y') }}</td>
                </tr>
            </table>
        </div>
    @endforeach
</body>
</html>