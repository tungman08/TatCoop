@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผล/เฉลี่ยคืนประจำปีของสมาชิกสหกรณ์ฯ
            <small>คำนวณเงินปันผล/เฉลี่ยคืนประจำปีให้กับสมาชิกสหกรณ์ฯ</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลเงินปันผล/เฉลี่ยคืน', 'link' => action('Admin\DividendController@getMember')],
            ['item' => 'เงินปันผล/เฉลี่ยคืน', 'link' => action('Admin\DividendController@getMemberDividend', ['member_id'=>$member->id, 'year'=>$year])],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>แก้ไขเงินปันผล/เฉลี่ยคืนประจำปี</h4>
            <p>ให้ผู้ดูแลระบบสามารถแก้ไขข้อมูลเงินปันผล/เฉลี่ยคืนประจำปี ที่ระบบได้คำนวณอัตโฯมัติ</p>
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
                <h3 class="box-title"><i class="fa fa-baht"></i> แก้ไขเงินปันผล/เฉลี่ยคืนประจำปี {{ $year + 543 }} ของ {{ $member->profile->fullname }}</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($dividend, ['action' => ['Admin\DividendController@postMemberUpdate', $member->id, $dividend->id], 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('dividend_name', 'ชื่อ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('dividend_name', $dividend->dividend_name, [
                                'class'=>'form-control',
                                'readonly'=>'true',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('shareholding', 'เงินค่าหุ้น', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('shareholding', $dividend->shareholding, [
                                'class'=>'form-control',
                                'placeholder'=>'ป้อนเงินค่าหุ้น',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    
                    <div class="form-group">
                        {{ Form::label('shareholding_dividend', 'เงินปันผล', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('shareholding_dividend', $dividend->shareholding_dividend, [
                                'class'=>'form-control',
                                'placeholder'=>'ป้อนเงินปันผล',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('interest', 'ดอกเบี้ยเงินกู้', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('interest', $dividend->interest, [
                                'class'=>'form-control',
                                'placeholder'=>'ป้อนดอกเบี้ยเงินกู้',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('interest_dividend', 'เงินเฉลี่ยคืน', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('interest_dividend', $dividend->interest_dividend, [
                                'class'=>'form-control',
                                'placeholder'=>'ป้อนเงินเฉลี่ยคืน',
                                'autocomplete'=>'off'])
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
@endsection