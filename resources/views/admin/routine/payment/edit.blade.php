@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ชำระเงินกู้ปกติ
            <small>ชำระเงินกู้ปกติ สำหรับสมาชิกประเภทพนักงาน/ลูกจ้าง ททท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ชำระเงินกู้ปกติ', 'link' => action('Admin\RoutinePaymentController@index')],
            ['item' => Diamond::parse($detail->routine->calculated_date)->thai_format('M Y'), 'link' => action('Admin\RoutineShareholdingController@show', ['id' => $detail->routine->id])],
            ['item' => 'แก้ไข', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ชำระเงินกู้ปกติ</h4>    
            <p>ยอดเงินเงินกู้รายเดือนของ {{ $detail->loan->member->profile->fullname }} ประจำเดือน {{ Diamond::parse($detail->routine->calculated_date)->thai_format('M Y') }} ที่ระบบคำนวณได้</p>  
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
                <h3 class="box-title"><i class="fa fa-gbp"></i> รายการชำระเงินกู้ปกติ</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($detail, ['action' => ['Admin\RoutinePaymentController@updateDetail', $detail->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('period', 'งวดที่', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('period', null, [
                                'class'=>'form-control',
                                'placeholder'=>'งวดที่',
                                'autocomplete'=>'off',
                                'onkeypress' => 'javascript:return isNumberKey(event);'])
                            }}        
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('principle', 'เงินต้น', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('principle', null, [
                                'class'=>'form-control',
                                'placeholder'=>'เงินต้น',
                                'autocomplete'=>'off',
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
                                'class'=>'form-control',
                                'placeholder'=>'ดอกเบี้ย',
                                'autocomplete'=>'off',
                                'onkeypress' => 'javascript:return isNumberKey(event);'])
                            }}        
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
                        'type' => 'submit', 
                        'class'=>'btn btn-primary btn-flat'])
                    }}
                    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
                        'class'=>'btn btn-default btn-flat', 
                        'onclick'=>'javascript:history.go(-1);'])
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

    <script>
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 8 && charCode != 127 && charCode != 45 && charCode != 46 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }    
    </script>
@endsection