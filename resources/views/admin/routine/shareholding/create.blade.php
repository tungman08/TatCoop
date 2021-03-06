@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ชำระค่าหุ้นปกติ
            <small>ชำระค่าหุ้นปกติ สำหรับสมาชิกประเภทพนักงาน/ลูกจ้าง ททท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ชำระค่าหุ้นปกติ', 'link' => action('Admin\RoutinePaymentController@index')],
            ['item' => Diamond::parse($routine->calculated_date)->thai_format('M Y'), 'link' => action('Admin\RoutineShareholdingController@show', ['id' => $routine->id])],
            ['item' => 'เพิ่ม', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ชำระค่าหุ้นปกติ</h4>    
            <p>ยอดเงินค่าหุ้น ประจำเดือน {{ Diamond::parse($routine->calculated_date)->thai_format('M Y') }} ที่ต้องป้อนเอง</p>  
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
                <h3 class="box-title"><i class="fa fa-sticky-note-o"></i> รายการชำระค่าหุ้นปกติ</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['action' => ['Admin\RoutineShareholdingController@storeDetail', $routine->id], 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('pay_date', 'วันที่ชำระ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('pay_date', Diamond::parse($routine->calculated_date)->copy()->endOfMonth()->format('Y-m-j'), [
                                'id'=>'pay_date',
                                'readonly' => true,
                                'class'=>'form-control']) 
                            }}     
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('member_id', 'รหัสสมาชิก', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('member_id', null, [
                                'id'=>'member_id',
                                'placeholder' => 'รหัสสมาชิก 5 หลัก...',
                                'data-inputmask'=>"'mask': '99999','placeholder': '0','autoUnmask': true,'removeMaskOnSubmit': true",
                                'data-mask',
                                'autocomplete'=>'off',
                                'class'=>'form-control']) 
                            }}     
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 padding-l-xs">
                            {{ Form::button('<i class="fa fa-calculator"></i> คำนวณ', [
                                'id'=>'calculate',
                                'type' => 'button', 
                                'class'=>'btn btn-default btn-flat'])
                            }}
                        </div>
                    </div>
                    <hr />
                    <div class="form-group">
                        {{ Form::label('fullname', 'ชื่อ-นามสกุล', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('fullname', null, [
                                'id' => 'fullname',
                                'readonly' => true,
                                'class'=>'form-control', 
                                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('shareholding', 'จำนวนหุ้น', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('shareholding', null, [
                                'id' => 'shareholding',
                                'readonly' => true,
                                'class'=>'form-control', 
                                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('amount', 'ค่าหุ้นที่ต้องชำระ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('amount', null, [
                                'id' => 'amount',
                                'readonly' => false,
                                'class'=>'form-control', 
                                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                                'autocomplete'=>'off',
                                'onkeypress' => 'javascript:return isNumberKey(event);'])
                            }}        
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                
                <div class="box-footer">
                    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
                        'id'=>'save',
                        'disabled' => false,
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
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $("[data-mask]").inputmask();
            $('form').submit(function() {
                $("[data-mask]").inputmask('remove');
            });

            $('#calculate').click(function () {
                calculate();
            });
        });

        function calculate() {
            var member_id = parseInt($('#member_id').val());

            if (member_id != '') {
                var formData = new FormData();
                    formData.append('member_id', member_id);

                $.ajax({
                    dataType: 'json',
                    url: '/routine/shareholding/ajax/calculate',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $(".ajax-loading").css("display", "block");
                    },
                    success: function(result) {
                        $(".ajax-loading").css("display", "none");

                        if (result.error == null) {
                            $('#fullname').val(result.fullname);
                            $('#shareholding').val(result.shareholding);
                            $('#amount').val(result.amount);
                        }
                        else {
                            alert(result.error);
                        }
                    }
                });
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