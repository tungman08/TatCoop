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
        {{ Html::style(elixir('css/miscellaneous.css')) }}
        {{ Html::style(elixir('css/font-awesome.css')) }}

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

            body {
                font-size: 10px;
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

            <!-- header -->
            <div class="text-center">
                <h3 style="font-size: 14px; margin: 5px 0px;"><strong>หนังสือขอยืนยันยอดลูกหนี้  เงินรับฝากและทุนเรือนหุ้น</strong></h3><br />
            </div>
            <div class="row margin-t-md">
                <div class="col-md-6">
                    <strong>ส่วนบน สำหรับสมาชิกเก็บไว้เป็นหลักฐาน</strong>
                </div>
                <!-- /.col -->
                <div class="col-md-6 text-right">
                    ว.1/2562
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <div class="row margin-t-md margin-b-md">
                <div class="col-md-12 text-right">
                    วันที่ 31 ธันวาคม 2562
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <p>เรียน คุณ {{ $member->profile->name }} {{ $member->profile->lastname }} สมาชิกเลขทะเบียนที่ {{ $member->memberCode }}</p>
            <p>สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด ขอเรียนว่า ณ วันที่ 31 ธันวาคม 2562</p>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <table class="table table-borderless">
                        <tr>
                            <td colspan="3">1. ท่านเป็นหนี้ต่อสหกรณ์ ดังนี้</td>
                        </tr>
                        @foreach ($debts as $debt)
                        <tr>
                            <td style="padding-left: 30px;">{{ $debt->name }}</td>
                            <td>คงเหลือ</td>
                            <td class="text-right">{{ number_format($debt->balance, 2, '.', ',') }} บาท</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td>2. ทุนเรือนหุ้น</td>
                            <td>จำนวนเงิน</td>
                            <td class="text-right">{{ number_format($shareholding, 2, '.', ',') }} บาท</td>
                        </tr>
                    </table>
                    <!--/.table-->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <p>ขอได้โปรดแจ้งไปยัง นายสุพัฒน์ ศานติรัตน์ ผู้สอบบัญชีว่ารายการดังกล่าวข้างต้นถูกต้องหรือมีข้อทักท้วงประการใด (ขอให้ตอบทุกกรณีด้วย) ตามหนังสือตอบยืนยันยอดที่แนบมานี้ <strong>กรุณาส่งกลับคืนด่วน ภายในวันที่ 9 กุมภาพันธ์ 2563 ณ ห้องสหกรณ์ (ชั้น 5)</strong></p>
            <div class="text-center margin-t-lg"><strong>ฉีกตามรอยนี้ส่วนล่างส่งกลับ ห้องสหกรณ์ ชั้น 5 รบกวนสมาชิกช่วยส่งด้วย</strong></div>

            <hr style="border-top: 1px dashed;" />
            <div style="margin-top: -28px; margin-left: 30px;"><i class="fa fa-cut"></i></div>

            </section>
        <!-- /.invoice -->
    </div>
    <!-- ./wrapper -->
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice">

            <div class="text-right margin-t-md margin-b-md">ว.1/2562</div>
            <!-- header -->
            <div class="text-center">
                <h3 style="font-size: 14px; margin: 5px 0px;"><strong>หนังสือตอบยืนยันยอด สำหรับส่งคืนผู้สอบบัญชี</strong></h3><br />
            </div>
            <p>เรียน นายสุพัฒน์ ศานติรัตน์ ผู้สอบบัญชีสหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</p>
            <p>ข้าพเจ้าขอยืนยันจำนวนเงินหนี้และทุนเรือนหุ้น ระหว่างข้าพเจ้ากับสหกรณ์ออมทรัพย์ ณ วันที่ 31 ธันวาคม 2562</p>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <table class="table table-borderless">
                        <tr>
                            <td colspan="2">1. จำนวนเงินเป็นหนี้ต่อสหกรณ์</td>
                        </tr>
                        @foreach ($debts as $debt)
                        <tr>
                            <td style="width: 50%; padding-left: 30px;">{{ $debt->name }}คงเหลือ</td>
                            <td style="width: 50%;" class="text-right">{{ number_format($debt->balance, 2, '.', ',') }} บาท</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 30px;"><i class="fa fa-square-o"></i> ถูกต้อง</td>
                            <td><i class="fa fa-square-o"></i> ไม่ถูกต้อง</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td>2. จำนวนเงินค่าหุ้น</td>
                            <td class="text-right">{{ number_format($shareholding, 2, '.', ',') }} บาท</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 30px;"><i class="fa fa-square-o"></i> ถูกต้อง</td>
                            <td><i class="fa fa-square-o"></i> ไม่ถูกต้อง</td>
                        </tr>
                    </table>
                    <!--/.table-->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <table class="margin-t-md" style="width: 100%;">
                <tr>
                    <td style="white-space: nowrap; width: 1%;">คำชี้แจง (ถ้าไม่ถูกต้อง)</td>
                    <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">&nbsp;</td>
                </tr>
            </table>
            <div class="row margin-b-md" style="margin-top: 20px; margin-right: 0px;">
                <div class="col-xs-6">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">ชื่อตัวบรรจง</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">&nbsp;</td>
                        </tr>
                    </table>
                </div>
                <!-- /.col -->
                <div class="col-xs-6">
                    <table style="width: 100%;">
                        <tr>
                            <td style="white-space: nowrap; width: 1%;">สมาชิกเลขทะเบียนที่</td>
                            <td style="border-bottom: 2px dotted #bbb; padding-left: 50px;">&nbsp;</td>
                        </tr>
                    </table>      
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            </section>
            <!-- /.invoice -->

        </section>
    </div>
    <!-- ./wrapper -->
</body>
</html>