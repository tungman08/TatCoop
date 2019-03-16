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
                <td colspan="2">
                    <table>
                        <tr>
                            <td style="width: 101px;"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACgCAYAAACLz2ctAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6REY2MTJENjZGQTY0MTFFNUI1NTQ4NEE4MzdEQTg4NkMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6REY2MTJENjdGQTY0MTFFNUI1NTQ4NEE4MzdEQTg4NkMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpERjYxMkQ2NEZBNjQxMUU1QjU1NDg0QTgzN0RBODg2QyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpERjYxMkQ2NUZBNjQxMUU1QjU1NDg0QTgzN0RBODg2QyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PolD9voAAFOlSURBVHja7J0HuF1VlcfPSycQQu8JgVAFA4iAAYN0EESlOSIIUiYMSmiOAyJRFAQENIbBAUGQoWsIiHQUUBFQWugQQhuDhF5Mb+/M+q17/ifr7ZxXknfz8l5gf9/Ovbnv3nP22fu/V19rN+SNs7I8zzNanjf4++7de2WVLS9eu2VduhWPmzU0zPussdH/v4r1QfbfgdbXsr6G9dWsr2x9RevLWe9nfWnrva33LH4+2/pM61OtT7b+gfV3rb9t/Y2pU6e/3rt379e6d+/2D7v+q/bZW9kS3JjLllpDw1TrDQ6ohjyf64gCeAJiQ0O3+YAXFytrWCLmaX173E0bG/NNbDI2tufbaO7cxvVnz57db6mlerfrwjNnzrZFaMzmzp2bLbNM3/LzuXNzPp/cs2f3Cfbf560/Z/0Z609bn7CkAhJYadODsYaGuQ7AhgZ739g4x950L/+YItiRah9369blAbih9a2tf9r6lgaGLQwgfbt3724Uf94DzZ49NzOAtOtGusasWXOyXr16ZNOnz8z69OltrzOyvn37VP1kmvVx1h+1/oj1h6yPXxKBWMOUANZYA+C8L8zPYxsK0tcQSKDt467yzMOKvp31bQsWWgPJnNn+2rNHz2zO3DnZtGnTsh49emR9l+rb7ptOnjI567dMv2zGzBnlZ31698mmTptqgOzl92ylwcIfsH6/9fuK3nWAVspq1a1bNm+DFxSwYb6vVCNYEM478/MDtJ2KvmNzEzRnzpwSCJPemJS99tpr2bLLLpsNHjw469G9R7sHcePvbsxOOOGE7M0338wOO+yw7Ic//GG20korFQxkgVnIvdbvKfoDXRGA84gb8KnNr3NVyX5dqSNf8Tp9+nR/Nblt4NSpU4fb+xsMWFNmzpyZ0z744INczb7rr//617/81b7nr48++mhuwGB28m7duuXf/va3c7t+/uGHH+bGov177733Xj5r1qzyd3xOmzx5sr/qezQbh3+XaxlFzXv27JnbBs+PPPLIPLYpU6aU16QxZq5jz+L3nzFjht+X92p27Sk8o/Xh9jwD7bv+/GwmvUfu7PRr2Di31tE/uiIANdn2OtQW6Ex7/yQLpwU1dpoLhBEoxxxzTH7XXXfl77//vv//7bffzo3iOfj69++fF3p+/uKLL/rfjSrm5557br7iiivmf/nLX/y6t99+e77DDjvkv/nNb/w7F198cb7//vvn77zzTv6HP/wh/5//+R8H+4QJE/L/+q//yvfdd9+8X79++bbbblsCjOsIuAJfbBGYbAQaQATk2gD2/yftY559KBsREDIn9tuPAdgBfQ/W3hbzrUiFoG6iGCwif7v66qvzrbfeOjcW6NToxhtvLBd30qRJ+VlnnZXff//9Dk6AcuyxxzpI/vjHPzplhIpBza699tr87rvvznv37p1vscUWubHr/MILL3TA9u3bNz/55JP9+vyfa37605/298stt1y+6qqr5m+88YZf93//93/zyy+/3MFPe/fdd5tQVDrfg5LyLFBK/U3f49kAbfH6llHLi4s5yez7XWD9auDLG/MuB8C9rF9uu30qi8MCRGohVsgCihJCnQACbPDf/u3ffOH+7//+L//yl7+cDxo0yAH217/+1b+zwQYb+Ov111+f77bbbg42fmeacr7UUkvlRx99dL7rrrv6tQHg8OHD8x//+Mf+m3POOcev16dPn/y+++7LN9lkE/8tFHHppZd2gMOGxerXXXddHwfjefDBB516qomdq8GO4zPSIsUXhy7mZq+PAVj/vpP1SxCdRAEAoIDG/8XWWCxRDxbypZdeyk855RQHjCkFTlGgSoALMAAoqN4KK6zgv91qq62cxQIQ/g7wABDvYcmiTmussUa+zz77+P8B2o477ujX4HsAkNcf/OAHzo4BnMAHlWUsq6++ev6Tn/wk32WXXXwT8Ld11lknHzduXMmSRd2hklG+jMDTc/JZMR9TirnaqSsAsLP7NIZYP9f61daPLDwQmbG6UnOXMsJnKCcGrMxYYvbcc89lJqdlBqTMqFBm7DkzeS079NBDM1NOMlvkbLXVVsuM8rhphPfYBA1w2dNPP50ZOPz9Zz7zmcyAUZqikLG4vgEp++c//+nXMYBlf/7znzNj9f43Y6+ZgTYzQPrY+N1GG22Uff7zn88M5D5mY//Z8ssv79fB/GObwNnniSee6OMzYGZf+tKXsmeeecavg9zLtblXY2GoRe7jelyf5+c69reli7m6upi7IZ3cONgpd0l/68dZH5dXNNiQBHqxJBoKwi233OJUDuUBdjhmzBinQLyHlSLL7bfffqXSAfV64IEHXDH59a9/XSoiyHu8GjjzvfbaK19llVXyO++806kkFGrTTTd1WZD/872DDjoo/93vfufvf/GLX/jv7r33Xpf1+EzKD7IgMuEyyyyT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" style="width: 100px; height: 100px;" alt="tat-logo" /></td>
                            <td style="text-align: center;">
                                <h3 style="font-size: 24px; font-weight: bold;">สหกรณ์ออมทรัพย์ สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</h3>
                                <h3 style="font-size: 24px;">ใบรับเงินค่าหุ้น</h3>
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
                            <td>{{ $member->profile->fullname }}</td>
                        </tr>
                        <tr>
                            <th>หน่วยงาน:</th>
                            <td>สอ.สรทท.</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table class="table table-bordered">
                        <tr>
                            <th>วันที่ชำระ:</th>
                            <td>{{ Diamond::parse($shareholding->pay_date)->thai_format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>เลขทะเบียน:</th>
                            <td>{{ str_pad($member->member_code, 5, "0", STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <th>ทุนเรือนหุ้นสะสม:</th>
                            <td>{{ number_format($total_shareholding + $shareholding->amount, 2,'.', ',') }}</td>
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
                            <tr>
                                <td>รับ{{ $shareholding->shareholding_type->name }}</td>
                                <td class="text-center">{{ Diamond::parse($shareholding->pay_date)->thai_format('m/y') }}</td>
                                <td class="text-right">{{ number_format($shareholding->amount, 2,'.', ',') }}</td>
                                <td class="text-right">{{ number_format($total_shareholding + $shareholding->amount, 2,'.', ',') }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">{{ Number::toBaht($shareholding->amount) }}</td>
                                <td class="text-right">{{ number_format($shareholding->amount, 2,'.', ',') }}</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 20px; padding-right: 0px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">ผู้จัดการ</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">{{ $billing->manager }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding-bottom: 20px; padding-left: 0px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">เหรัญญิก</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">{{ $billing->treasurer }}</td>
                        </tr>
                    </table> 
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