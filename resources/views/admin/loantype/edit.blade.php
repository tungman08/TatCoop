@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการประเภทเงินกู้
            <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลประเภทเงินกู้', 'link' => '/admin/loantype'],
            ['item' => 'ประเภทเงินกู้', 'link' => '/admin/loantype/' . $loantype->id],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการประเภทเงินกู้ของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของสหกรณ์</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <!-- Horizontal Form -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">เพิ่มประเภทเงินกู้</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($loantype, ['route' => ['admin.loantype.update', $loantype->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                @include('admin.loantype.form', ['edit' => true])
            {{ Form::close() }}
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
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

    <!-- Custom JavaScript -->
    {!! Html::script(elixir('js/member-form.js')) !!}

    <script>
        $(document).ready(function() {
            $('#start_date').datetimepicker({
                locale: 'th',
                viewMode: 'days',
                format: 'YYYY-MM-DD',
                locale: moment().lang('th'),
                useCurrent: false
            });

            $('#expire_date').datetimepicker({
                locale: 'th',
                viewMode: 'days',
                format: 'YYYY-MM-DD',
                locale: moment().lang('th'),
                useCurrent: false
            });
        });
  
        function check_limits(editmode) {
            if (!editmode) {
                var empty = 0;

                $('.limits').each(function () {
                    if (this.value == "") {
                        empty++;
                    } 
                });

                $('#add_limit').prop("disabled", (empty != 0));
            }
        }
    </script>
@endsection