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
            ['item' => 'ชำระเงิน', 'link' => ''],
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
                        @php($pmt = ($loan->pmt == 0) ? LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period) : $loan->pmt)
                        <th>ชำระงวดละ:</th>
                        <td>{{ number_format($pmt, 2, '.', ',') }} บาท</td>
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
                    1. หากชำระค่าเงินกู้ระหว่างวันที่ 1-10 ระบบจะทำการปรับปรุงข้อมูลการนำส่งตัดบัญชีเงินเดือนใหม่ กรุณาตรวจสอบข้อมูลการนำส่งอีกครั้ง<br />
                    2. หากชำระค่าเงินกู้หลังวันที่ 10 ถึงสิ้นเดือน ระบบจะคำนวณเงินที่ต้องใช้ โดยทำการคำนวณดอกเบี้ยถึงวันที่ทำการปิดยอด และหักส่วนของเงินที่นำส่ง
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

       <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card"></i> เพิ่มการชำระเงินกู้</h3>
                <input type="hidden" id="loan_id" value="{{ $loan->id }}" />
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['action' => ['Admin\PaymentController@store', $loan->id], 'method' => 'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data']) }}
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
                            {{ Form::label('amount', 'เงินที่ต้องการชำระ', [
                                'class'=>'col-sm-2 control-label']) 
                            }}
    
                            <div class="col-sm-10">
                                {{ Form::text('amount', $pmt, [
                                    'class'=>'form-control', 
                                    'placeholder'=>'กรุณาป้อนจำนวนเงินที่ต้องการชำระ', 
                                    'autocomplete'=>'off',
                                    'onkeypress' => 'javascript:return isNumberKey(event);'])
                                }}  
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
                                <i class="fa fa-money"></i> <strong>เงินที่ต้องนำจ่ายรายงวด</strong>
                                <hr />
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="cal">ช่วงเวลาคำนวณดอกเบี้ย</label>
                                    <input type="text" id="cal" name="cal" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />  
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="period">งวดที่</label>
                                    <input type="text" id="period" name="period" readonly="readonly"
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
                                    <input type="text" id="total" readonly="readonly"
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
                                    <label for="routine_cal">ช่วงเวลาคำนวณดอกเบี้ย</label>
                                    <input type="text" id="routine_cal" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />  
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="routine_period">งวดที่</label>
                                    <input type="text" id="routine_period" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />  
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="routine_principle">จำนวนเงินต้น</label>
                                    <input type="text" id="routine_principle" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />  
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="routine_interest">จำนวนดอกเบี้ย</label>
                                    <input type="text" id="routine_interest" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />       
                                </div>
                                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                    <label for="routine_total">รวม</label>
                                    <input type="text" id="routine_total" readonly="readonly"
                                        placeholder="กรุณากดปุมคำนวณ..."
                                        class="form-control" />       
                                </div>
                            </div>
                        </div>
                        <!--/.col-->
                    </div>
                    <!--/.row-->
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
            var amount = $('#amount').val();

            if (date != '') {
                if (amount != '') {
                    var formData = new FormData();
                    formData.append('loan_id', $('#loan_id').val());
                    formData.append('lastpay_date', moment($('#lastpay_date').val()));
                    formData.append('pay_date', moment(date));
                    formData.append('period', $('#period').val());
                    formData.append('amount', amount);

                    $.ajax({
                        dataType: 'json',
                        url: '/ajax/calculate',
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
                            $('#period').val(result.period);
                            $('#principle').val($.number(result.principle, 2));
                            $('#interest').val($.number(result.interest, 2));
                            $('#total').val($.number(result.total, 2));

                            $('#routine_cal').val(result.routine_cal);
                            $('#routine_period').val(result.routine_period);
                            $('#routine_principle').val($.number(result.routine_principle, 2));
                            $('#routine_interest').val($.number(result.routine_interest, 2));
                            $('#routine_total').val($.number(result.routine_total, 2));

                            $('#save').removeAttr("disabled");       
                        }
                    });
                }
                else {
                    alert('กรุณาจำนวนเงินที่ต้องการชำระ');
                }
            }
            else {
                alert('กรุณาเลือกวันที่จากปฏิทิน');
            }
        }
        
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 8 && charCode != 127 && charCode != 45 && charCode != 46 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }   
    </script>
@endsection