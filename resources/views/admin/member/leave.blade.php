@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/service/member'],
            ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => '/service/member/' . $member->id],
            ['item' => 'ลาออก', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลสมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ $member->profile->fullName }}</td>
                    </tr>
                    <tr>
                        <th>จำนวนทุนเรือนหุ้นสะสม:</th>
                        <td>{{ number_format($shareholdings, 2, '.', ',') }} บาท</td>
                    </tr>  
                    <tr>
                        <th>จำนวนเงินกู้ที่ต้องชำระทั้งหมด:</th>
                        <td>
                            {{ ($payments > 0) ?number_format($payments, 2, '.', ',') . ' บาท (รวมดอกเบี้ย)' : '-' }}
                            <span class="text-muted" style="cursor: pointer;" data-tooltip="true" title="สัญญาเงินกู้ {{ $loanCount }} สัญญา"><i class="fa fa-info-circle"></i></span>
                        </td>
                    </tr>
                    @if($shareholdings != $payments)
                        <tr>
                            <th class="{{ ($shareholdings > $payments) ? 'text-success' : 'text-danger' }}">{{ ($shareholdings > $payments) ? 'รับเงินค่าหุ้นคืน' : 'ชำระค่าเงินกู้เพิ่ม' }}:</th>
                            <td class="{{ ($shareholdings > $payments) ? 'text-success' : 'text-danger' }}">{{ number_format(abs($shareholdings - $payments), 2, '.', ',') }} บาท</td>
                        </tr>
                    @endif
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 

            <span class="pull-right text-danger small">* กรุณารับ/จ่ายเงินให้แก่สมาชิกก่อนกดปุ่มยืนยันการลาออก</span>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-user-times"></i> ยืนยันการลาออกของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }} (รหัสสมาชิก {{ $member->memberCode }})</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/service/member/' . $member->id . '/leave', 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group padding-l-md padding-r-md">
                        {{ Form::label('member_code', 'รหัสสมาชิกที่ต้องการลาออก', [
                            'class'=>'control-label']) 
                        }}
                        {{ Form::text('member_code', null, [
                            'id' => 'member_code',
                            'placeholder' => 'รหัสสมาชิก 5 หลัก',
                            'data-inputmask' => "'mask': '99999','placeholder': '0','autoUnmask': true,'removeMaskOnSubmit': true",
                            'data-mask',
                            'autocomplete'=>'off',
                            'class'=>'form-control'])
                        }}
                    </div>

                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    {{ Form::button('<i class="fa fa-times"></i> ลาออก', [
                        'id'=>'save',
                        'type' => 'submit', 
                        'class'=>'btn btn-danger btn-flat'])
                    }}
                    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
                        'class'=>'btn btn-default btn-flat', 
                        'onclick'=> 'javascript:history.go(-1);'])
                    }}
                </div>
                <!-- /.box-footer -->
            {{ Form::close() }}
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();

        $("[data-mask]").inputmask();
        // $('form').submit(function() {
        //    $("[data-mask]").inputmask('remove');
        //});
    });
    </script>
@endsection