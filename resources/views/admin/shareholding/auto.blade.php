@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการทุนเรือนหุ้นของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข ทุนเรือนหุ้นของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการทุนเรือนหุ้น', 'link' => 'service/shareholding/member'],
            ['item' => 'ชำระค่าหุ้นแบบอัตโนมัติ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ชำระค่าหุ้นแบบอัตโนมัติ</h4>
            <p>ให้ผู้ดูแลระบบสามารถชำระค่าหุ้นของสมาชิกที่หักค่าหุ้นผ่านบัญชีเงินเดือนได้แบบอัตโนมัติ</p>
        </div>

        @if(Session::has('flash_message'))
            <div class="callout {{ Session::get('callout_class') }}">
                <h4>แจ้งข้อความ!</h4>
                <p>
                    {{ Session::get('flash_message') }}

                    @if(Session::has('flash_link'))
                        <a href="{{ Session::get('flash_link') }}">Undo</a>
                    @endif
                </p>
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-money"></i> ชำระค่าหุ้นแบบอัตโนมัติ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <!-- form start -->   
                {{ Form::open(['url' => '/service/shareholding/autoshareholding', 'method' => 'post',
                    'onsubmit' => 'return confirm("คุณต้องการทำการชำระค่าหุ้นแบบอัตโนมัติใช่หรือไม่?");']) }}
                    <div class="form-group">
                        <label>
                            <i class="fa fa-calendar fa-fw"></i>
                            ปฏิทิน
                        </label>
                        <div>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="input-group" id="datepicker" style="width: 250px;">
                                                {{ Form::text('month', Diamond::today()->format('Y-m'), [
                                                    'id'=>'month', 
                                                    'class'=>'form-control'])
                                                }}     
                                                <span class="input-group-addon">
                                                    <span class="fa fa-calendar">
                                                    </span>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">                     
                                                {{ Form::button('<i class="fa fa-bolt"></i> ป้อนการชำระค่าหุ้นแบบอัตโนมัติ ประจำเดือน<span id="b_month">' . Diamond::today()->thai_format('F Y') . '</span>', [
                                                    'id' => 'automatic',
                                                    'type' => 'submit', 
                                                    'class'=>'btn btn-primary btn-flat margin-l-sm'])
                                                }}
                                            </div>  
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--/.table-->
                        </div>
                    </div>
                    <p class="help-block">กรุณาเลือกเดือนที่ต้องการชำระค่าหุ้นอัตโนมัติ</p>
                {{ Form::close() }}

                <br />
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 30%;">ชื่อสมาชิกที่สามารถชำระแบบอัตโนมัติ</th>
                                <th style="width: 20%;">ประเภทสมาชิก</th>
                                <th style="width: 20%;">หุ้นรายเดือน</th>
                                <th style="width: 20%;">ทุนเรือนหุ้นสะสม</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
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

    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent
    
    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <!-- Bootstrap DataTable JavaScript -->
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('[data-tooltip="true"]').tooltip();
        $(".ajax-loading").css("display", "none"); 

        $('#datepicker').datetimepicker({
            viewMode: 'months',
            format: 'YYYY-MM',
            minDate: moment('2015-12-31'),
            maxDate: moment(),
            locale: moment().lang('th'),
            useCurrent: false
        }).on("dp.change", function (e) {
            bindDataTable(e.date.format('YYYY-M-D'))
        }).on('dp.hide', function(e){
            setTimeout(function() {
                $('#datepicker').data('DateTimePicker').viewMode('months');
            }, 1);
        });

        bindDataTable($('#datepicker').data('DateTimePicker').date().format('YYYY-M-D'));
    });   

    function bindDataTable(date) {
        $('#dataTables-users').dataTable().fnDestroy();
        $('#dataTables-users').dataTable({
            "ajax": {
                "processing": true,
                "serverSide": true,
                "url": "/ajax/membershareholding",
                "type": "post",
                "data": {
                    "date": date
                },
                beforeSend: function () {
                    $(".ajax-loading").css("display", "block");
                    $('#automatic').prop('disabled', true);
                },
                complete: function() {
                    $(".ajax-loading").css("display", "none");

                    if ($('#dataTables-users').children('tbody').children('tr').children('td').length > 1) {
                        $('#automatic').prop('disabled', false);
                    }

                    moment.locale('th');
                    $('#b_month').html(moment(date).add(543, 'years').format('MMMM YYYY'));
                }  
            },
            "iDisplayLength": 25,
            "columns": [
                { "data": "code" },
                { "data": "fullname" },
                { "data": "typename" },
                { "data": "shareholding" },
                { "data": "amount" },
            ],
            "retrieve": true,
        });
    }
    </script>
@endsection