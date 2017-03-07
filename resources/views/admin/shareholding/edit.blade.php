@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสมาชิกสหกรณ์
        <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
    </h1>

    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/admin/member'],
        ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => "/admin/member/{$member->id}"],
        ['item' => 'การชำระค่าหุ้น', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well" style="padding-bottom: 0px;">
            <h4>ทุนเรือนหุ้น</h4>

            @include('admin.member.info.detail', ['member' => $member])
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
                <h3 class="box-title"><i class="fa fa-edit"></i> แก้ไขการชำระค่าหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($shareholding, ['url' => ['/admin/member/' . $member->id . '/shareholding', $shareholding->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
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
    $('#datepicker').datetimepicker({
        locale: 'th',
        viewMode: 'days',
        format: 'YYYY-MM-DD'
    });
    </script>
@endsection