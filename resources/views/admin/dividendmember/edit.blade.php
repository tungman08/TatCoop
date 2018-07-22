@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผลประจำปีของสมาชิกสหกรณ์ฯ
            <small>คำนวณเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงินปันผลประจำปีของสมาชิก', 'link' => url('/admin/dividendmember/year')],
            ['item' => 'ปี ' . strval($dividend->rate_year + 543), 'link' => url('/admin/dividendmember/' . $dividend->id )],
            ['item' => $member->fullname, 'link' => url('/admin/dividendmember/' . $dividend->id . '/' . $member->id )],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การคำนวณเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ เงินปันปี ของ {{ $member->fullname }}</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">อัตราเงินปันผล</th>
                        <td>{{ $dividend->shareholding_rate }}%</td>
                    </tr>
                    <tr>
                        <th>เงินปันผลรวม:</th>
                        <td>{{ number_format($member->dividends->sum('shareholding_dividend'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>อัตราเงินเฉลี่ยคืน</th>
                        <td>{{ $dividend->loan_rate }}%</td>
                    </tr>
                    <tr>
                        <th>เงินเฉลี่ยคืนรวม</td>
                        <td>{{ number_format($member->dividends->sum('interest_dividend'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>รวมทั้งสิ้น</td>
                        <td>{{ number_format($member->dividends->sum('shareholding_dividend') + $member->dividends->sum('interest_dividend'), 2, '.', ',') }} บาท</td>
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
                <h3 class="box-title"><i class="fa fa-baht"></i> แก้ไขเงินปันผลประจำปี {{ $dividend->rate_year + 543 }} ของ {{ $member->fullname }}</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($m_dividend, ['route' => ['admin.dividendmember.update', $dividend->id, $member->id, $m_dividend->id], 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('dividend_name', 'ชื่อ', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('dividend_name', $m_dividend->dividend_name, [
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
                            {{ Form::text('shareholding', $m_dividend->shareholding, [
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
                            {{ Form::text('shareholding_dividend', $m_dividend->shareholding_dividend, [
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
                            {{ Form::text('interest', $m_dividend->interest, [
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
                            {{ Form::text('interest_dividend', $m_dividend->interest_dividend, [
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