@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => action('Admin\LoanController@getMember')],
            ['item' => 'การกู้ยืม', 'link' => action('Admin\LoanController@index', ['member_id'=>$member->id])],
            ['item' => 'สัญญากู้ยืม', 'link' => action('Admin\LoanController@show', ['member_id'=>$member->id, 'id'=>$loan->id])],
            ['item' => 'ปิดยอดเงินกู้เพื่อกู้ใหม่', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดสัญญากู้ยืมเลขที่ {{ $loan->code }}</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ $member->profile->fullname }}</td>
                    </tr>
                    <tr>
                        <th>ประเภทเงินกู้:</th>
                        <td>{{ $loan->loanType->name }}</td>
                    </tr>  
                    <tr>
                        <th>วงเงินที่กู้:</th>
                        <td>{{ number_format($loan->outstanding, 2, '.', ',') }} บาท</td>
                    </tr>  
                    <tr>
                        <th>จำนวนงวดผ่อนชำระ:</th>
                        <td>{{ number_format($loan->period, 0, '.', ',') }} งวด</td>
                    </tr>
                    <tr>
                        <th>ชำระงวดละ:</th>
                        <td>{{ ($loan->pmt == 0) ? number_format(LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period), 2, '.', ',') : number_format($loan->pmt, 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>เงินต้นคงเหลือ:</th>
                        <td>{{ number_format($loan->outstanding - $loan->payments->sum('principle'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>ดอกเบี้ยสะสม:</th>
                        <td>{{ number_format($loan->payments->sum('interest'), 2, '.', ',') }} บาท</td>
                    </tr>
                    @if ($loan->sureties->count() > 0)
                        <tr>
                            <th>ผู้ค้ำประกัน:</th>
                            <td>
                                <ul class="list-info">
                                    @foreach($loan->sureties as $item)
                                        <li>{{ $item->profile->fullname }} (ค้ำประกันจำนวน {{ number_format($item->pivot->amount, 2, '.', ',')  }}  บาท)</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endif
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 

            @if ($member->profile->employee->employee_type_id == 1)
                <p>
                    <strong>หมายเหตุ:</strong> (สำหรับสมาชิกที่เป็นพนักงาน/ลูกจ้าง ททท. ที่นำส่งตัดบัญชีเงินเดือน)<br />
                    1. หากปิดยอดเงินกู้ระหว่างวันที่ 1-10 ระบบจะทำการลบข้อมูลการนำส่งตัดบัญชีเงินเดือนออก กรุณาตรวจสอบข้อมูลการนำส่งอีกครั้ง<br />
                    2. หากปิดยอดเงินกู้หลังวันที่ 10 ถึงสิ้นเดือน ระบบจะคำนวณเงินที่ต้องใช้ โดยทำการคำนวณดอกเบี้ยถึงวันที่ทำการปิดยอด และหักส่วนของเงินที่นำส่ง
                </p>
            @endif
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <!-- form start -->
        {{ Form::open(['action' => ['Admin\PaymentController@postPrintRefinance', $loan->id], 'method' => 'post', 'target' => '_blank', 'id' => 'printrefinance']) }}
            {{ Form::hidden('hidden_cal', null, ['id'=>'hidden_cal']) }}
            {{ Form::hidden('hidden_principle', null, ['id'=>'hidden_principle']) }}
            {{ Form::hidden('hidden_interest', null, ['id'=>'hidden_interest']) }}
            {{ Form::hidden('hidden_total', null, ['id'=>'hidden_total']) }}
        {{ Form::close() }}
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card"></i> ปิดยอดเงินกู้เพื่อกู้ใหม่</h3>
                <input type="hidden" id="loan_id" value="{{ $loan->id }}" />
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['action' => ['Admin\PaymentController@postRefinance', $loan->id], 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('lastpay_date', 'วันที่ชำระล่าสุด', [
                            'class'=>'col-sm-2 control-label']) 
                        }}
                        <div class="col-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                {{ Form::text('lastpay_date', $lastpay_date, [
                                    'id'=>'lastpay_date',
                                    'readonly'=>true,
                                    'class'=>'form-control'])
                                }}    
                            </div>   
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('pay_date', 'วันที่ต้องการชำระ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}
                        <div class="col-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                {{ Form::text('pay_date', Diamond::today()->format('Y-m-d'), [
                                    'id'=>'pay_date',
                                    'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                                    'autocomplete'=>'off',
                                    'class'=>'form-control'])
                                }} 
                            </div>      
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 padding-l-xs">
                            <button type="button" id="calculate" class="btn btn-default btn-flat">
                                <i class="fa fa-calculator"></i> คำนวณ
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-5 padding-l-none">
                            <div class="well">
                                <i class="fa fa-money"></i> <strong>เงินที่ต้องนำมาปิดยอด</strong>
                                {{--<div class="pull-right"><a href="javascript:void(0);" onclick="javascript:$('#printclose').submit();"
                                    class="btn btn-default btn-flat"><i class="fa fa-print"></i></a></div>--}}
                                <hr />
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="cal">ช่วงเวลาคำนวณดอกเบี้ย</label>
                                    <input type="text" id="cal" name="cal" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />  
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="principle">จำนวนเงินต้น</label>
                                    <input type="text" id="principle" name="principle" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />  
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="interest">จำนวนดอกเบี้ย</label>
                                    <input type="text" id="interest" name="interest" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />       
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="total">รวม</label>
                                    <input type="text" id="total" name="total" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />       
                                </div>
                            </div>
                        </div>
                        <!--/.col-->

                        <div class="col-sm-5 padding-r-none">
                            <div class="well">
                                <i class="fa fa-money"></i> <strong>เงินที่หักนำส่งตัดบัญชีเงินเดือน</strong>
                                <hr />
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="routine_cal">จำนวนเงินที่ต้องคืน</label>
                                    <input type="text" id="refund" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />  
                                </div>
                            </div>
                        </div>
                        <!--/.col-->
                    </div>
                    <!--/.row-->
                    <div class="form-group">
                        <label for="summary" class="col-sm-2 control-label">ยอดรวมที่ต้องชำระ</label>
                        <div class="col-sm-10">
                            <input type="text" id="summary" readonly="readonly"
                                placeholder="กรุณากดปุมคำนวณ..."
                                class="form-control" />      
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
                        'id'=>'save',
                        'disabled' => true,
                        'type' => 'submit', 
                        'class'=>'btn btn-primary btn-flat'])
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

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $("#save").attr("disabled", true);

            $('#pay_date').datetimepicker({
                locale: moment.locale('th'),
                viewMode: 'days',
                format: 'YYYY-MM-DD',
                useCurrent: false,
                focusOnShow: false,
                buddhismEra: true
            });

            $('#calculate').click(function () {
                calculateLoan($(this).data('id'));
            });
        });

        function calculateLoan(id) {
            var date = $('#pay_date').val();

            if (date != '') {
                var formData = new FormData();
                    formData.append('loan_id', $('#loan_id').val());
                    formData.append('lastpay_date', moment($('#lastpay_date').val()));
                    formData.append('pay_date', moment(date));

                $.ajax({
                    dataType: 'json',
                    url: '/ajax/refinancecalculate',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $(".ajax-loading").css("display", "block");
                    },
                    success: function(result) {
                        $(".ajax-loading").css("display", "none");

                        $('#cal').val(result.cal);
                        $('#principle').val($.number(result.principle, 2));
                        $('#interest').val($.number(result.interest, 2));
                        $('#total').val($.number(result.total, 2));

                        // $('#hidden_cal').val(result.cal);
                        // $('#hidden_principle').val($.number(result.principle, 2));
                        // $('#hidden_interest').val($.number(result.interest, 2));
                        // $('#hidden_total').val($.number(result.total, 2));

                        $('#refund').val($.number(result.refund, 2));

                        $('#save').removeAttr("disabled");
                    }
                });
            }
            else {
                alert('กรุณาเลือกวันที่จากปฏิทิน');
            }
        }
    </script>
@endsection