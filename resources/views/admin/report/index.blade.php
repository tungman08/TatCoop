@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานต่างๆ
            <small>แสดงรายงานต่างๆ ในระบบ</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'รายงานต่างๆ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายงานต่างๆ</h4>
            <p>ให้ผู้ดูแลระบบสามารถดูแสดงรายงานต่างๆ ในระบบได้</p>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-file-excel-o"></i> รายงานข้อมูล ณ ปัจจุบัน</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        @include('admin.report.today')
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-file-excel-o"></i> รายงานประจำเดือน</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="form-group">
                            <label for="monthly">กรุณาเลือกเดือน</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" id="monthly" name="monthly" class="form-control" value="{{ Diamond::today()->format('Y-m') }}" />
                            </div>
                        </div>
                        <!-- /.form-group -->

                        @include('admin.report.monthly')
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-file-excel-o"></i> รายงานประจำปี</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="form-group">
                            <label for="annual">กรุณาเลือกปี</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" id="annual" name="annual" class="form-control" value="{{ Diamond::today()->format('Y') }}" />
                            </div>
                        </div>
                        <!-- /.form-group -->

                        @include('admin.report.annual')
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->

    {{ Form::open(['action' => 'Admin\ReportController@postExport', 'method' => 'post', 'id' => 'export']) }}
        {{ Form::hidden('report', '', [ 'id' => 'report' ]) }}
        {{ Form::hidden('reporttype', '', [ 'id' => 'reporttype' ]) }}
        {{ Form::hidden('date', '', [ 'id' => 'date' ]) }}
    {{ Form::close() }}
@endsection

@section('styles')
    @parent

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#annual').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'years',
            format: 'YYYY',
            useCurrent: false,
            focusOnShow: false,
            buddhismEra: true
        });

        $('#monthly').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'months',
            format: 'YYYY-MM',
            useCurrent: false,
            focusOnShow: false,
            buddhismEra: true
        });
    });

    function reportexport(report, reporttype) {
        var mydate = (reporttype == 'annual') ? 
            $('#annual').data('DateTimePicker').date() :
            $('#monthly').data('DateTimePicker').date();

        $('#report').val(report);
        $('#reporttype').val(reporttype);
        $('#date').val(mydate.format("YYYY-M-D"));
        $('#export').submit();
    }
    </script>
@endsection