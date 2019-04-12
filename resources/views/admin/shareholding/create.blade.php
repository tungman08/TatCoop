@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการทุนเรือนหุ้นของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข ทุนเรือนหุ้นของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการทุนเรือนหุ้น', 'link' => '/service/shareholding/member'],
            ['item' => 'ทุนเรือนหุ้น', 'link' => '/service/' . $member->id . '/shareholding'],
            ['item' => 'ชำระค่าหุ้น', 'link' => '']
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>
            <p>เพิ่มการชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullname }}</p>
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
                <h3 class="box-title"><i class="fa fa-asterisk"></i> กรอกการชำระค่าหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/service/' . $member->id . '/shareholding', 'method' => 'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data']) }}
            <div class="box-body">
                <div class="form-group">
                    {{ Form::label('pay_date', 'วันที่ชำระ', [
                        'class'=>'col-sm-2 control-label']) 
                    }}

                    <div class="col-sm-10" style="padding: 0 5px;">
                        {{ Form::text('pay_date', null, [
                            'id'=>'pay_date',
                            'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                            'autocomplete'=>'off',
                            'class'=>'form-control'])
                        }}       
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('shareholding_type_id', 'ประเภท', [
                        'class'=>'col-sm-2 control-label']) 
                    }}

                    <div class="col-sm-10">
                        {{ Form::select('shareholding_type_id', $shareholding_types->lists('name', 'id'), 2, [
                            'id' => 'shareholding_type_id',
                            'class' => 'form-control']) 
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
                            'placeholder'=>'ตัวอย่าง: 10000', 
                            'autocomplete'=>'off'])
                        }}        
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('remark', 'หมายเหตุ', [
                        'class'=>'col-sm-2 control-label']) 
                    }}

                    <div class="col-sm-10">
                        {{ Form::textarea ('remark', null, [
                            'style'=>'height:100px; min-height:100px; max-height:100px;',
                            'class'=>'form-control'])
                        }}        
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    {{ Form::label('attachment', 'เอกสารแนบ', [
                        'class'=>'col-sm-2 control-label']) 
                    }}
                    {{ Form::file ('attachment', [
                        'class'=>'form-control-file',
                        'style'=>'padding-top: 7px'])
                    }} 
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
        $('#pay_date').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: false,
            focusOnShow: false,
            buddhism: true
        });
    });
    </script>
@endsection