@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => action('Admin\LoanController@getMember')],
            ['item' => 'การกู้ยืม', 'link' => action('Admin\LoanController@index', ['member_id'=>$member->id])],
            ['item' => 'สัญญากู้ยืม', 'link' => action('Admin\LoanController@show', ['member_id'=>$member->id, 'id'=>$loan->id])],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การทำสัญญาการกู้ยืมของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullname }}</h4>
            <p>ให้ผู้ดูแลระบบ แก้ไขสัญญาเงินกู้ได้</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <!-- Box content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                @if ($is_super)
                    {{ Form::open(['action' => ['Admin\LoanController@destroy', $member->id, $loan->id], 'method' => 'delete']) }}        
                        <h3 class="box-title"><i class="fa fa-credit-card"></i> แก้ไขสัญญาเงินกู้</h3>

                        {{ Form::button('<i class="fa fa-times"></i>', [
                            'type'=>'submit',
                            'data-tooltip'=>"true",
                            'title'=>"ลบ",
                            'class'=>'btn btn-danger btn-xs btn-flat pull-right', 
                            'onclick'=>'javascript:return confirm(\'คุณต้องการลบรายการนี้ใช่ไหม ?\');'])
                        }}
                    {{ Form::close() }}
                @else
                    <h3 class="box-title"><i class="fa fa-credit-card"></i> แก้ไขสัญญาเงินกู้</h3>
                @endif
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($loan, ['action' => ['Admin\LoanController@update', $member->id, $loan->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('code', 'เลขที่สัญญา', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            {{ Form::text('code', $loan->code, [
                                'class'=>'form-control',
                                'placeholder'=>'ป้อนเลขที่สัญญา',
                                'autocomplete'=>'off'])
                            }}        
                        </div>
                    </div>
                    
                    <div class="form-group">
                        {{ Form::label('loaned_at', 'วันที่ทำสัญญา', [
                            'class'=>'col-sm-2 control-label']) 
                        }}

                        <div class="col-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                {{ Form::text('loaned_at', $loan->loaned_at, [
                                    'id' => 'loaned_at',
                                    'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                                    'autocomplete'=>'off',
                                    'class'=>'form-control'])
                                }}  
                            </div>           
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
        $('#loaned_at').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: false,
            focusOnShow: false,
            buddhismEra: true
        });
    });
    </script>
@endsection