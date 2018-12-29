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
                    <h3 style="font-size: 18px; margin: 0px;">ใบรับเงินค่างวด</h3>
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
                        <td>000000000000</td>
                    </tr>
                    <tr>
                        <th>ได้รับเงินจาก:</th>
                        <td>ร.ต.อ.วศิน มีปรีชา</td>
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
                        <td>{{ Diamond::today()->thai_format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>เลขทะเบียน:</th>
                        <td>00000</td>
                    </tr>
                    <tr>
                        <th>วงเงินที่กู้:</th>
                        <td>0.00</td>
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
                        <td colspan="2">ศุนย์บาทถ้วน</td>
                        <td class="text-right">0.00</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td>สัญญาเงินกู้เลขที่ 0000/{{ Diamond::today()->thai_format('Y') }}</td>
                        <td class="text-center">{{ Diamond::today()->thai_format('m/y') }}</td>
                        <td class="text-right">
                            <table class="table table-borderless" style="margin-bottom: 0px;">
                                <tr>
                                    <td class="text-left">เงินต้น</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-left" style="border-top: 1px solid #f4f4f4;">ดอกเบี้ย</td>
                                    <td style="border-top: 1px solid #f4f4f4;">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-left" style="border-top: 1px solid #f4f4f4;">รวม</td>
                                    <td style="border-top: 1px solid #f4f4f4;">0.00</td>
                                </tr>
                            </table>                                   
                        </td>
                        <td class="text-right">0.00</td>
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
	</div>
</section>
<!-- /.invoice -->