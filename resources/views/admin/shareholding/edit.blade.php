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
            ['item' => Diamond::parse($shareholding->pay_date)->thai_format('M Y'), 'link' => action('Admin\ShareholdingController@getMonth', ['member_id'=>$member->id, 'pay_date'=>Diamond::parse($shareholding->pay_date)->format('Y-n-1')])],
            ['item' => 'รายละเอียด', 'link' => action('Admin\ShareholdingController@show', ['member_id'=>$member->id, 'id'=>$shareholding->id])],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>
            <p>แก้ไขข้อมูลชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullname }}</p>
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
                {{ Form::open(['action' => ['Admin\ShareholdingController@destroy', $member->id, $shareholding->id], 'method' => 'delete']) }}
                    <h3 class="box-title"><i class="fa fa-edit"></i> แก้ไขการชำระค่าหุ้น</h3>

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
            {{ Form::model($shareholding, ['action' => ['Admin\ShareholdingController@update', $member->id, $shareholding->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                @include('admin.shareholding.form', ['edit' => true])
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
        $('[data-tooltip="true"]').tooltip();
        
        $('#pay_date').datetimepicker({
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