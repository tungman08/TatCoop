@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการทุนเรือนหุ้นของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข ทุนเรือนหุ้นของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการทุนเรือนหุ้น', 'link' => '/service/shareholding/member'],
            ['item' => 'ทุนเรือนหุ้น', 'link' => '/service/' . $member->id . '/shareholding'],
            ['item' => 'ชำระค่าหุ้น', 'link' => '']
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>
            <p>เพิ่มการชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullName }}</p>
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
                <h3 class="box-title"><i class="fa fa-asterisk"></i> กรอกการชำระค่าหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/service/' . $member->id . '/shareholding', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.shareholding.form', ['edit' => false])
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
    $('#datepicker').datetimepicker({
        locale: 'th',
        viewMode: 'days',
        format: 'YYYY-MM-DD',
        locale: moment().lang('th'),
        useCurrent: false
    });
    </script>
@endsection