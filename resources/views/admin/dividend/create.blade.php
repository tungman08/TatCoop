@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผล/เฉลี่ยคืน
            <small>เพิ่ม ลบ แก้ไข อัตราเงินปันผล/เฉลี่ยคืนประจำปี สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงินปันผล/เฉลี่ยคืน', 'link' => '/database/dividend'],
            ['item' => 'เพิ่ม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการอัตราเงินปันผล/เฉลี่ยคืนประจำปีของสหกรณ์</h4>
            <p>เพิ่มอัตราเงินปันผล/เฉลี่ยคืนประจำปีของสหกรณ์</p>
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
                <h3 class="box-title"><i class="fa fa-plus-circle"></i> เพิ่มอัตราเงินปันผล/เฉลี่ยคืนประจำปี</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/database/dividend', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.dividend.form', ['edit' => false])
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
            $('#rate_year').datetimepicker({
                locale: moment.locale('th'),
                format: 'YYYY',
                viewMode: 'years',
                minDate: moment('2015-12-31'),
                maxDate: moment(),
                useCurrent: false,
                focusOnShow: false,
                buddhismEra: true
            }).on('dp.hide', function(e){
                setTimeout(function() {
                    $('#rate_year').data('DateTimePicker').viewMode('years');
                }, 1);
            });

            $('#release_date').datetimepicker({
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