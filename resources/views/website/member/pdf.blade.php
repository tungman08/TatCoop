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
            font-size: 18px;
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
            vertical-align: top;
            text-align: left;
        }

    </style>
</head>
<body>
    <div style="background: #fff; border: 1px solid #f4f4f4; padding: 20px;">
        <table>
            <tr>
                <td colspan="2">
                    <table>
                        <tr>
                            <td style="width: 101px;"><img src="{{ asset('images/logo-coop.png') }}" style="width: 100px; height: 100px;" alt="tat-logo" /></td>
                            <td style="text-align: center;">
                                <h3 style="font-size: 24px; font-weight: bold;">สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</h3>
                                <h3 style="font-size: 24px;">ใบเสร็จรับเงินค่าหุ้น</h3>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <table class="table table-bordered">
                        <tr>
                            <th>เลขที่:</th>
                            <td>{{ $billno }}</td>
                        </tr>
                        <tr>
                            <th>ได้รับเงินจาก:</th>
                            <td>{{ $member->profile->fullName }}</td>
                        </tr>
                        <tr>
                            <th>หน่วยงาน:</th>
                            <td>สหกรณ์การท่องเที่ยว</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table class="table table-bordered">
                        <tr>
                            <th>วันที่:</th>
                            <td>{{ $date->thai_format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>เลขทะเบียน:</th>
                            <td>{{ str_pad($member->member_code, 5, "0", STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <th>ทุนเรือนหุ้นสะสม:</th>
                            <td>{{ number_format($total_shareholding, 2,'.', ',') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 20px 0px;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40%">รายการ/สัญญา</th>
                                <th class="text-center" style="width: 10%">เดือนที่</th>
                                <th class="text-center" style="width: 25%">จำนวนเงิน</th>
                                <th class="text-center" style="width: 25%">ทุนเรือนหุ้นสะสม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($result = $total_shareholding - $shareholdings->sum('amount'))
                            @foreach($shareholdings as $share)
                                @php($result += $share->amount)
                                <tr>
                                    <td>รับ{{ $share->shareholding_type->name }}</td>
                                    <td class="text-center">{{ $date->thai_format('m/y') }}</td>
                                    <td class="text-right">{{ number_format($share->amount, 2,'.', ',') }}</td>
                                    <td class="text-right">{{ number_format($result, 2,'.', ',') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">{{ Number::toBaht($shareholdings->sum('amount')) }}</td>
                                <td class="text-right">{{ number_format($shareholdings->sum('amount'), 2,'.', ',') }}</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 20px; padding-right: 0px; white-space: nowrap; overflow-x: hidden;">
                    ผู้จัดการ <span style="color: #bbb;">...................................................................................................</span>
                </td>
                <td style="padding-bottom: 20px; padding-left: 0px; white-space: nowrap; overflow-x: hidden;">
                    เจ้าหน้าที่ผู้รับเงิน <span style="color: #bbb;">.....................................................................................</span> 
                </td>
            </tr>
            <tr>
                <td colspan="2" style="height: 80px; border: 2px solid #ddd; background-color: #fcfcfc;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 20px; text-align: center;">ใบรับเงินประจำเดือนจะสมบูรณ์ต่อเมื่อสหกรณ์ได้รับเงินที่เรียกเก็บครบถ้วนแล้ว</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 80px; text-align: right;">พิมพ์เอกสารวันที่: {{ Diamond::today()->thai_format('d M Y') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>