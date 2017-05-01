@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผล
            <small>เพิ่ม ลบ แก้ไข อัตราเงินปันผลประจำปี สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงินปันผล', 'link' => '/admin/dividend'],
            ['item' => 'ข้อมูลเงินปันผล', 'link' => ''],
            ['item' => 'แก้ไข', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการอัตราเงินปันผลประจำปีของสหกรณ์</h4>
            <p>แก้ไขอัตราเงินปันผลประจำปีของสหกรณ์</p>
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
                <h3 class="box-title">แก้อัตราเงินปันผลประจำปี</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($dividend, ['route' => ['admin.dividend.update', $dividend->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                @include('admin.dividend.form', ['edit' => true])
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
        $('#rate_year').datetimepicker({
            locale: 'th',
            format: 'YYYY',
            viewMode: 'years',
            locale: moment().lang('th'),
            useCurrent: false
        }).on('dp.hide', function(e){
            setTimeout(function() {
                $('#rate_year').data('DateTimePicker').viewMode('months');
            }, 1);
        });
    </script>
@endsection