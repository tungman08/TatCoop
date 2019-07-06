@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการทุนเรือนหุ้นของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข ทุนเรือนหุ้นของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการทุนเรือนหุ้น', 'link' => action('Admin\ShareholdingController@getMember')],
            ['item' => 'ทุนเรือนหุ้น', 'link' => action('Admin\ShareholdingController@index', ['member_id'=>$member->id])],
            ['item' => 'ปรับปรุง', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>
            <p>ปรับข้อมูลจำนวนหุ้นรายเดือนของ {{ $member->profile->fullname }}</p>
            <p>
                <strong>หมายเหตุ:</strong> (สำหรับสมาชิกที่เป็นพนักงาน/ลูกจ้าง ททท. ที่นำส่งตัดบัญชีเงินเดือน)<br />
                1. หากปรับปรุงจำนวนหุ้นระหว่างวันที่ 1-9 ระบบจะทำการปรับปรุงข้อมูลการนำส่งตัดบัญชีเงินเดือนใหม่ กรุณาตรวจสอบข้อมูลการนำส่งอีกครั้ง
                2. หากปรับปรุงจำนวนหุ้นตั้งแต่วันที่ 10 ถึงสิ้นเดือน ระบบจะส่งยอดหุ้นใหม่เพื่อตัดบัญชีเงินเดือนในเดือนหน้า
            </p>
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
                <h3 class="box-title"><i class="fa fa-edit"></i> แก้ไขข้อมูลจำนวนหุ้นรายเดือน</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($member, ['action' => ['Admin\ShareholdingController@putAdjust', $member->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('shareholding', 'จำนวนหุ้นต่อเดือน (หุ้น)', [
                            'class'=>'col-sm-2 control-label']) 
                        }}
    
                        <div class="col-sm-10">
                            {{ Form::text('shareholding', null, [
                                'id' => 'shareholding',
                                'class'=>'form-control', 
                                'placeholder'=>'ตัวอย่าง: 100', 
                                'autocomplete'=>'off'])
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
    @parent
@endsection

@section('scripts')
    @parent

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();
    });
    </script>
@endsection