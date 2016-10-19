@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        สถิติการเข้าใช้งาน
        <small>รายละเอียดการเข้าใช้งานระบบของสมาชิก สอ.สรทท.</small>
    </h1>

    @include('admin.statistic.breadcrumb')

    </section>

    <!-- Main content -->
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#website_result" data-toggle="tab"><i class="fa fa-laptop fa-fw"></i> เว็บไซต์</a></li>
                <li><a href="#webapp_result" data-toggle="tab"><i class="fa fa-desktop fa-fw"></i> เว็บแอดมิน</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="website_result">
                    @include('admin.statistic.result', ['web' => 'website', 'statistics' => $website])
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="webapp_result">
                    @include('admin.statistic.result', ['web' => 'webapp', 'statistics' => $webapp])
                </div>
                <!-- /.tab-pane -->

            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">สถิติการเข้าใช้งานรายเดือน <span class="display-month display-number">{{ $date->thai_format('F Y') }}</span></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <label>
                        <i class="fa fa-calendar fa-fw"></i>
                        ปฏิทิน
                    </label>
                    <div class="input-group" id="datepicker" style="width: 250px;">
                        <input type="text" id="month" class="form-control" value="{{ $date->format('Y-m') }}" />
                        <span class="input-group-addon">
                            <span class="fa fa-calendar">
                            </span>
                        </span>
                    </div>
                </div>
                <p class="help-block">กรุณาเลือกเดือนที่ต้องการแสดงข้อมูล</p>
            </div>
        </div>
        <!-- /.box -->

        <div class="nav-tabs-custom">
            <ul id="chart" class="nav nav-tabs">
                <li class="active"><a href="#website" data-toggle="tab"><i class="fa fa-laptop fa-fw"></i> เว็บไซต์</a></li>
                <li><a href="#webapp" data-toggle="tab"><i class="fa fa-desktop fa-fw"></i> เว็บแอดมิน</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="website">
                    @include('admin.statistic.chart', ['chart' => 'website'])
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="webapp">
                    @include('admin.statistic.chart', ['chart' => 'webapp'])
                </div>
                <!-- /.tab-pane -->

            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->

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

    <!-- jQuery Flot Chart JavaScript -->
    {!! Html::script(elixir('js/jquery.flot.js')) !!}
    {!! Html::script(elixir('js/jquery.flot.tooltip.js')) !!}

    <!-- Custom JavaScript -->
    {!! Html::script(elixir('js/admin-statistics.js')) !!}
@endsection