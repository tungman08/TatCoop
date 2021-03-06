@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        สถิติการเข้าใช้งาน
        <small>รายละเอียดการเข้าใช้งานระบบของสมาชิก สอ.สรทท.</small>
    </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'สถิติการเข้าใช้งาน', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">สรุปสถิติการเข้าใช้งาน เว็บไซต์</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        @include('admin.statistic.result', ['statistics' => $website])
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col-sm-6 -->

            <div class="col-sm-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">สรุปสถิติการเข้าใช้งาน เว็บสมาชิก</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        @include('admin.statistic.result', ['statistics' => $webuser])
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col-sm-6 -->

            <div class="col-sm-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">สรุปสถิติการเข้าใช้งาน เว็บแอดมิน</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        @include('admin.statistic.result', ['statistics' => $webapp])
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col-sm-6 -->
        </div>
        <!-- /.row -->

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
                    <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="text" id="datepicker" class="form-control" value="{{ $date->format('Y-m') }}" />
                    </div>
                </div>
                <p class="help-block">กรุณาเลือกเดือนที่ต้องการแสดงข้อมูล</p>
            </div>
        </div>
        <!-- /.box -->

        <div class="nav-tabs-custom">
            <ul id="chart" class="nav nav-tabs">
                <li class="active"><a href="#website" data-toggle="tab"><i class="fa fa-laptop fa-fw"></i> เว็บไซต์</a></li>
                <li><a href="#webuser" data-toggle="tab"><i class="fa fa-desktop fa-fw"></i> เว็บสมาชิก</a></li>
                <li><a href="#webapp" data-toggle="tab"><i class="fa fa-desktop fa-fw"></i> เว็บแอดมิน</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="website">
                    @include('admin.statistic.chart', ['chart' => 'website'])
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="webuser">
                    @include('admin.statistic.chart', ['chart' => 'webuser'])
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