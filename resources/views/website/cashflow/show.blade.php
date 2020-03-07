@extends('website.member.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        สรุปข้อมูล
        <small>สรุปข้อมูลสมาชิก สอ.สรทท.</small>
    </h1>
    @include('website.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'ข้อมูลยอดลูกหนี้ เงินรับฝากและทุนเรือนหุ้น', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>สมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">รหัสสมาชิก:</th>
                        <td>{{ $member->memberCode }}</td>
                    </tr> 
                    <tr>
                        <th>ชื่อสมาชิก:</th>
                        <td>{{ $member->profile->fullname }}</td>
                    </tr>  
                    <tr>
                        <th>ข้อมูลปี:</th>
                        <td>{{ $year + 543 }}</td>
                    </tr> 
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 

            <a href="{{ action('Website\CashflowController@getPrintCashflow', ['year'=>$year]) }}" target="_blank" class="btn btn-default btn-flat"><i class="fa fa-print"></i> พิมพ์</a>
            <button type="button"
                class="btn btn-primary btn-flat pull-right"
                style="margin-right: 5px;"
                onclick="javascript:document.location.href  = '{{ action('Website\CashflowController@getPrintPdfCashflow', ['year'=>$year]) }}';">
                <i class="fa fa-download"></i> บันทึกเป็น PDF
            </button>
        </div>


        <!-- Main content -->
        <section class="invoice">

            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <i class="fa fa-file-text-o"></i> หนังสือขอยืนยันยอดลูกหนี้  เงินรับฝากและทุนเรือนหุ้น
                        <small class="pull-right">วันที่: {{ Diamond::today()->thai_format('d M Y') }}</small>
                    </h2>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- header -->
            <div class="text-center">
                <h3 style="font-size: 18px; margin: 5px 0px;"><strong>หนังสือขอยืนยันยอดลูกหนี้  เงินรับฝากและทุนเรือนหุ้น</strong></h3><br />
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
            <div style="margin-top: -30px; margin-left: 30px;"><i class="fa fa-cut"></i></div>
            <div class="text-right margin-t-md margin-b-md">ว.1/2562</div>
            <!-- header -->
            <div class="text-center">
                <h3 style="font-size: 18px; margin: 5px 0px;"><strong>หนังสือตอบยืนยันยอด สำหรับส่งคืนผู้สอบบัญชี</strong></h3><br />
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
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent

    <style>
        .table-borderless > tbody > tr > td,
        .table-borderless > tbody > tr > th,
        .table-borderless > tfoot > tr > td,
        .table-borderless > tfoot > tr > th,
        .table-borderless > thead > tr > td,
        .table-borderless > thead > tr > th {
            border: none;
        }
    </style>
@endsection

@section('scripts')
    @parent
@endsection