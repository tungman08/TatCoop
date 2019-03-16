@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงื่อนไขการค้ำประกัน
            <small>แก้ไข รายละเอียดเงื่อนไขการค้ำประกันเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงื่อนไขการค้ำประกัน', 'link' => action('Admin\BailsmanController@index')],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดรายละเอียดเงื่อนไขการค้ำประกันของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ แก้ไข รายละเอียดเงื่อนไขการค้ำประกันเงินกู้ของสหกรณ์ เพื่อใช้ในการตรวจสอบความสามารถในการค้ำประกันของผู้ค้ำ</p>
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
                <h3 class="box-title"><i class="fa fa-male"></i> แก้ไขเงื่อนไขประเภท{{ $bailsman->employeeType->name }}</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($bailsman, ['route' => ['database.bailsman.update', $bailsman->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <strong>กรณีค้ำประกันตนเอง ({{ ($bailsman->self_type == 'shareholding') ? 'ใช้ทุนเรือนหุ้น' : 'ใช้เงินเดือน' }})</strong>
                    {{ Form::hidden('self_type', $bailsman->self_type) }}  
                    <br /><br />
                    <div class="form-group">
                        {{ Form::label('self_rate', ($bailsman->self_type == 'shareholding') ? 'จำนวนหุ้นที่ต้องใช้ (%)' : 'จำนวนเงินเดือนที่สามารถค้ำได้ (เท่า)', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('self_rate', ($bailsman->self_type == 'shareholding') ? $bailsman->self_rate * 100 : $bailsman->self_rate, [
                                'class'=>'form-control',
                                'placeholder'=>'ตัวอย่าง: 50',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('self_maxguaruntee', 'วงเงินสูงสุดที่สามารถค้ำได้ (บาท)', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('self_maxguaruntee', $bailsman->self_maxguaruntee, [
                                'class'=>'form-control',
                                'placeholder'=>'ตัวอย่าง: 1200000',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    @if ($bailsman->self_type == 'salary')
                        <div class="form-group">
                            {{ Form::label('self_netsalary', 'เงินเดือนสุทธิต่ำสุดหลังหักค่างวด (บาท)', [
                                'class'=>'col-sm-2 control-label']) 
                            }}

                            <div class="col-sm-10">
                                {{ Form::text('self_netsalary', $bailsman->self_netsalary, [
                                    'class'=>'form-control',
                                    'placeholder'=>'ตัวอย่าง: 3000',
                                    'autocomplete'=>'off'])
                                }}        
                            </div>
                        </div>
                    @else
                        {{ Form::hidden('self_netsalary', $bailsman->self_netsalary) }}  
                    @endif

                    <hr />

                    <strong>กรณีค้ำประกันผู้อื่น ({{ ($bailsman->other_type == 'shareholding') ? 'ใช้ทุนเรือนหุ้น' : 'ใช้เงินเดือน' }})</strong>
                    {{ Form::hidden('other_type', $bailsman->other_type) }}  
                    <br /><br />
                    <div class="form-group">
                        {{ Form::label('other_rate', ($bailsman->other_type == 'shareholding') ? 'จำนวนหุ้นที่ต้องใช้ (%)' : 'จำนวนเงินเดือนที่สามารถค้ำได้ (เท่า)', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('other_rate', ($bailsman->other_type == 'shareholding') ? $bailsman->other_rate * 100 : $bailsman->other_rate, [
                                'class'=>'form-control',
                                'placeholder'=>'ตัวอย่าง: 90',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('other_maxguaruntee', 'วงเงินสูงสุดที่สามารถค้ำได้ (บาท)', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('other_maxguaruntee', $bailsman->other_maxguaruntee, [
                                'class'=>'form-control',
                                'placeholder'=>'ตัวอย่าง: 1200000',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    @if ($bailsman->other_type == 'salary')
                        <div class="form-group">
                            {{ Form::label('other_netsalary', 'เงินเดือนสุทธิต่ำสุดหลังหักค่างวด (บาท)', [
                                'class'=>'col-sm-2 control-label']) 
                            }}

                            <div class="col-sm-10">
                                {{ Form::text('other_netsalary', $bailsman->other_netsalary, [
                                    'class'=>'form-control',
                                    'placeholder'=>'ตัวอย่าง: 3000',
                                    'autocomplete'=>'off'])
                                }}        
                            </div>
                        </div>
                    @else
                        {{ Form::hidden('other_netsalary', $bailsman->self_netsalary) }}  
                    @endif
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
@endsection