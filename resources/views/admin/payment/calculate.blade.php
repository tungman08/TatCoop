@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => '/service/loan/member'],
            ['item' => 'การกู้ยืม', 'link' => 'service/' . $member->id . '/loan'],
            ['item' => 'สัญญากู้ยืม', 'link' => 'service/' . $member->id . '/loan/' . $loan->id],
            ['item' => 'คำนวณยอดเงินที่ต้องการปิดยอดเงินกู้', 'link' => '']
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
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-calculator"></i> คำนวณยอดเงินที่ต้องการปิดยอดเงินกู้</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <!-- form start -->
                <form class="form-horizontal">
                    <input type="hidden" id="loan_id" value="{{ $loan->id }}" />

                    <div class="form-group">
                        <label for="pay_date" class="col-sm-2 control-label">วันที่ชำระ</label>
                        <div class="col-sm-10 input-group" style="padding: 0 5px;">
                            <input type="text" name="pay_date" id="pay_date"
                                placeholder="กรุณาเลือกจากปฏิทิน..."
                                autocomplete="off" class="form-control" />  
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 padding-l-xs">
                            <button type="button" id="calculate" class="btn btn-default btn-flat">
                                <i class="fa fa-calculator"></i> คำนวณ
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="principle" class="col-sm-2 control-label">จำนวนเงินต้น</label>
                        <div class="col-sm-10">
                            <input type="text" id="principle" readonly="readonly"
                                placeholder="กรุณากดปุมคำนวณ..."
                                class="form-control" />  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="interest" class="col-sm-2 control-label">จำนวนดอกเบี้ย</label>
                        <div class="col-sm-10">
                            <input type="text" id="interest" readonly="readonly"
                                placeholder="กรุณากดปุมคำนวณ..."
                                class="form-control" />       
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="total" class="col-sm-2 control-label">ยอดรวมที่ต้องชำระ</label>
                        <div class="col-sm-10">
                            <input type="text" id="total" readonly="readonly"
                                placeholder="กรุณากดปุมคำนวณ..."
                                class="form-control" />      
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">หมายเหตุ</label>
                        <div class="col-sm-10">
                            <input type="text" id="remark" readonly="readonly"
                                placeholder="กรุณากดปุมคำนวณ..."
                                class="form-control" />        
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.box-body -->
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

        $('[data-tooltip="true"]').tooltip();
        $(".ajax-loading").css("display", "none"); 

        $('#pay_date').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-D',
            useCurrent: false,
            focusOnShow: false,
            buddhism: true
        }).on('dp.hide', function(e){
            setTimeout(function() {
                $('#pay_date').data('DateTimePicker').viewMode('days');
            }, 1);
        });

        $('#calculate').click(function () {
            var date = $("#pay_date").val();

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
                        $('#remark').val(result.remark);
                    }
                });
            }
            else {
                alert('กรุณาเลือกวันที่จากปฏิทิน');
            }
        });
    });
    </script>
@endsection