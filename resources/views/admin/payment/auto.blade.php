@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => '/service/loan/member'],
            ['item' => 'ชำระเงินกู้แบบอัตโนมัติ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ชำระเงินกู้แบบอัตโนมัติ</h4>
            <p>ให้ผู้ดูแลระบบสามารถชำระเงินกู้ของสมาชิกที่หักค่าหุ้นผ่านบัญชีเงินเดือนได้แบบอัตโนมัติ</p>
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
                <h3 class="box-title"><i class="fa fa-credit-card"></i> ชำระเงินกู้แบบอัตโนมัติ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <!-- form start -->   
                {{ Form::open(['url' => '/service/loan/autopayment', 'method' => 'post',
                    'onsubmit' => 'return confirm("คุณต้องการทำการชำระเงินกู้แบบอัตโนมัติใช่หรือไม่?");']) }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <i class="fa fa-calendar fa-fw"></i>
                                    ปฏิทิน
                                </label>
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
                            </div>
                            <p class="help-block">กรุณาเลือกเดือนที่ต้องการชำระเงินกู้อัตโนมัติ</p>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group pull-right">                     
                                    {{ Form::button('<i class="fa fa-bolt"></i> ป้อนการชำระเงินกู้แบบอัตโนมัติ ประจำเดือน<span id="b_month">' . Diamond::today()->thai_format('F Y') . '</span>', [
                                        'id' => 'automatic',
                                        'type' => 'submit', 
                                        'class'=>'btn btn-primary btn-flat'])
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}

                <br />
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 30%;">ชื่อสมาชิกที่สามารถชำระแบบอัตโนมัติ</th>
                                <th style="width: 12%;">ประเภทสมาชิก</th>
                                <th style="width: 12%;">จำนวนสัญญา</th>
                                <th style="width: 12%;">จำนวนเงินต้น</th>
                                <th style="width: 12%;">จำนวนดอกเบี้ย</th>
                                <th style="width: 12%;">รวมเป็นเงิน</th>
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
                "url": "/ajax/memberpayment",
                "type": "post",
                "data": {
                    "date": date
                },
                beforeSend: function () {
                    $(".ajax-loading").css("display", "block");
                    $('#automatic').addClass('disabled');
                },
                complete: function() {
                    $(".ajax-loading").css("display", "none");

                    if ($('#dataTables-users').children('tbody').children('tr').children('td').length > 1) {
                        $('#automatic').removeClass('disabled');
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
                { "data": "loanCount" },
                { "data": "principle" },
                { "data": "interest" },
                { "data": "total" }
            ],
            "retrieve": true,
        });
    }
    </script>
@endsection