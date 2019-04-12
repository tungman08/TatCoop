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
                        <td>{{ $member->profile->fullname }}</td>
                    </tr>
                    <tr>
                        <th>วันที่ลาออก:</th>
                        <td>{{ Diamond::parse($leave_date)->thai_format('j F Y') }}</td>
                    </tr>  
                    <tr>
                        <th>จำนวนทุนเรือนหุ้นสะสม:</th>
                        <td>{{ number_format($shareholdings, 2, '.', ',') }} บาท</td>
                    </tr>  
                    <tr>
                        <th>จำนวนเงินกู้ที่ต้องชำระทั้งหมด:</th>
                        <td>
                            {{ ($payments > 0) ? number_format($payments, 2, '.', ',') . ' บาท (รวมดอกเบี้ย)' : '-' }}
                        </td>
                    </tr>
                    @if($payments > 0)
                        <tr>
                            <th>&nbsp;</th>
                            <td>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>เลขที่สัญญา</th>
                                            <th>ประเภทเงินกู้</th>
                                            <th>วงเงินที่กู้</th>
                                            <th>เงินต้นคงเหลือ</th>
                                            <th>ดอกเบี้ย ณ ปัจจุบัน</th>
                                            <th>รวมที่ต้องชำระ</th>
                                        <tr>
                                    </thead>
                                    <tbody>
                                    @foreach($loans as $index => $loan)
                                        @php($payment = MemberProperty::getClosePayment($loan))
                                        <tr>
                                            <td>{{ $index + 1 }}.</td>
                                            <td>{{ $loan->code }}</td>
                                            <td>{{ $loan->loanType->name }}</td>
                                            <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                            <td>{{ number_format($loan->outstanding - $payment->principle, 2, '.', ',') }}</td>
                                            <td>{{ number_format($payment->interest, 2, '.', ',') }}</td>
                                            <td>{{ number_format(($loan->outstanding - $payment->principle) + $payment->interest, 2, '.', ',') }}
                                    @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
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

            <span class="pull-right text-danger small">* ระบบจะทำการเคลียร์ยอดหุ้นและเงินกู้จนเป็นศูนย์ กรุณารับ/จ่ายเงินให้แก่สมาชิกก่อนกดปุ่มยืนยันการลาออก</span>
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
                <h3 class="box-title"><i class="fa fa-user-times"></i> ยืนยันการลาออกของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullname }} (รหัสสมาชิก {{ $member->memberCode }})</h3>
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

                    <div class="form-group padding-l-md padding-r-md">
                        {{ Form::label('leave_date', 'วันที่ลาออก', [
                            'class'=>'control-label']) 
                        }}
                        {{ Form::text('leave_date', null, [
                            'id' => 'leave_date',
                            'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
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
    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();

        $("[data-mask]").inputmask();
        // $('form').submit(function() {
        //    $("[data-mask]").inputmask('remove');
        //});

        $('#leave_date').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: false,
            focusOnShow: false,
            buddhism: true
        });
    });
    </script>
@endsection