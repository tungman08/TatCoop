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
            ['item' => 'การกู้ยืม', 'link' => '/service/' . $member->id . '/loan'],
            ['item' => 'สัญญากู้ยืม', 'link' => '/service/' . $member->id . '/loan/' . $loan->id],
            ['item' => 'รายการผ่อนชำระ', 'link' => '/service/' . $member->id . '/loan/' . $loan->id . '/payment/' . $payment->id],
            ['item' => 'แก้ไข', 'link' => ''],
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

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

       <div class="box box-primary">
            <div class="box-header with-border">
                <input type="hidden" id="loan_id" value="{{ $loan->id }}" />

                {{ Form::open(['url' => '/service/' . $member->id . '/loan/' . $loan->id . '/payment/' . $payment->id, 'method' => 'delete']) }}
                    <h3 class="box-title"><i class="fa fa-credit-card"></i> แก้ไขรายการผ่อนชำระ</h3>
    
                    {{ Form::button('<i class="fa fa-times"></i>', [
                        'type'=>'submit',
                        'data-tooltip'=>"true",
                        'title'=>"ลบ",
                        'class'=>'btn btn-danger btn-xs btn-flat pull-right', 
                        'onclick'=>'javascript:return confirm(\'คุณต้องการลบรายการนี้ใช่ไหม ?\');'])
                    }}
                {{ Form::close() }}
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($payment, ['url' => '/service/' . $member->id . '/loan/' . $loan->id . '/payment/' . $payment->id, 'method' => 'put', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('period', 'งวดที่', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('period', null, [
                                'class'=>'form-control', 
                                'placeholder'=>'ตัวอย่าง: 0', 
                                'autocomplete'=>'off',
                                'onkeypress' => 'javascript:return isNumberKey(event);'])
                            }}  
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('pay_date', 'วันที่ชำระ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                {{ Form::text('pay_date', null, [
                                    'id'=>'pay_date',
                                    'readonly' => false,
                                    'placeholder' => 'กรุณาเลือกจากปฏิทิน...', 
                                    'autocomplete'=>'off',
                                    'class' => 'form-control'])
                                }}   
                            </div>    
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('principle', 'เงินต้น', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('principle', null, [
                                'readonly' => false,
                                'class'=>'form-control', 
                                'placeholder'=>'ตัวอย่าง: 100000', 
                                'autocomplete' =>'off',
                                'onkeypress' => 'javascript:return isNumberKey(event);'])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('interest', 'ดอกเบี้ย', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('interest', null, [
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
                buddhism: true
            });
        });

        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 8 && charCode != 127 && charCode != 45 && charCode != 46 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }    
    </script>
@endsection