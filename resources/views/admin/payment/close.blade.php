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
            ['item' => 'ปิดยอดเงินกู้', 'link' => ''],
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
                        <td>{{ number_format($loan->period, 0, '.', ',') }} งวด (ชำระงวดละ {{ number_format(LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period), 2, '.', ',') }} บาท)</td>
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
                    1. หากปิดยอดเงินกู้ระหว่างวันที่ 1-9 ระบบจะทำการลบข้อมูลการนำส่งตัดบัญชีเงินเดือนออก กรุณาตรวจสอบข้อมูลการนำส่งอีกครั้ง<br />
                    2. หากปิดยอดเงินกู้ตั้งแต่วันที่ 10 ถึงสิ้นเดือน ระบบจะคำนวณเงินที่ต้องใช้ โดยทำการคำนวณดอกเบี้ยถึงวันที่ทำการปิดยอด และหักส่วนของเงินที่นำส่ง
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
            @php
                $start = $loan->payments->count() > 0 ? Diamond::parse($loan->payments->max('pay_date'))->thai_format('d M Y') : Diamond::parse($loan->loaned_at)->thai_format('d M Y');
                $header = $start . ' ถึงวันที่มาชำระ';
            @endphp

            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card"></i> ปิดยอดเงินกู้ (คำนวณดอกเบี้ยตั้งแต่ {{ $header }})</h3>
                <input type="hidden" id="loan_id" value="{{ $loan->id }}" />
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['action' => ['Admin\PaymentController@postClose', $loan->id], 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('pay_date', 'วันที่ชำระ', [
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
                            {{ Form::button('<i class="fa fa-calculator"></i> คำนวณ', [
                                'id'=>'calculate',
                                'type' => 'button', 
                                'data-id' => $loan->id,
                                'class'=>'btn btn-default btn-flat'])
                            }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('total', 'จำนวนเงินที่ต้องชำระ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('total', null, [
                                'readonly' => true,
                                'class'=>'form-control', 
                                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                                'autocomplete'=>'off'])
                            }}  
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('principle', 'เงินต้น', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('principle', null, [
                                'readonly' => true,
                                'class'=>'form-control', 
                                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('interest', 'ดอกเบี้ย', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('interest', null, [
                                'readonly' => true,
                                'class'=>'form-control', 
                                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                                'autocomplete'=>'off'])
                            }}        
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
                    formData.append('pay_date', moment(date));

                $.ajax({
                    dataType: 'json',
                    url: '/ajax/closecalculate',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $(".ajax-loading").css("display", "block");
                    },
                    success: function(result) {
                        $(".ajax-loading").css("display", "none");

                        $('#principle').val($.number(result.principle, 2));
                        $('#interest').val($.number(result.interest, 2));
                        $('#total').val($.number(result.total, 2));

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