@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ชำระค่าหุ้นปกติ
            <small>ชำระค่าหุ้นปกติ สำหรับสมาชิกประเภทพนักงาน/ลูกจ้าง ททท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ชำระค่าหุ้นปกติ', 'link' => action('Admin\RoutineShareholdingController@index')],
            ['item' => Diamond::parse($detail->routine->calculated_date)->thai_format('M Y'), 'link' => action('Admin\RoutineShareholdingController@show', ['id' => $detail->routine->id])],
            ['item' => 'แก้ไข', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ชำระค่าหุ้นปกติ</h4>    
            <p>ยอดเงินค่าหุ้นรายเดือนของ {{ $detail->member->profile->fullname }} ประจำเดือน {{ Diamond::parse($detail->routine->calculated_date)->thai_format('M Y') }} ที่ระบบคำนวณได้</p>  
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
                <h3 class="box-title"><i class="fa fa-eur"></i> รายการชำระค่าหุ้นปกติ</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($detail, ['action' => ['Admin\RoutineShareholdingController@updateDetail', $detail->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
            <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('shareholding', 'จำนวนหุ้น', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('shareholding', $detail->member->shareholding, [
                                'class'=>'form-control',
                                'readonly'=>true])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('amount', 'ค่าหุ้น', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('amount', null, [
                                'class'=>'form-control',
                                'placeholder'=>'ค่าหุ้นรายเดือน',
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