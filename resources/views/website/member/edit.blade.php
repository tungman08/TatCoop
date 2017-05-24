@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลสมาชิก
        <small>รายละเอียดของสมาชิก สอ.สรทท.</small>
    </h1>
    @include('website.member.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'ข้อมูลสมาชิก', 'link' => '/member'],
        ['item' => 'แก้ไขข้อมูล', 'link' => ''],
    ]])   
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลสมาชิกสหกรณ์</h4>
            <p>ข้อมูลของ{{ $member->profile->fullName }} รหัสสมาชิก: {{ $member->memberCode }}</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">แก้ไขรายละเอียดข้อมูลสมาชิก</h3>
            </div>
            <!-- /.box-header -->

            {{ Form::model($member, ['url' => '/member/' . $member->id, 'method' => 'put', 'class' => 'form-horizontal']) }}
                @include('website.member.form', ['edit' => true, 'id' => $member->id])
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

    <!-- Custom JavaScript -->
    {!! Html::script(elixir('js/member-form.js')) !!}

    <script>
    $('#birth_date').datetimepicker({
        locale: 'th',
        viewMode: 'days',
        format: 'YYYY-MM-DD'
    });
    </script>
@endsection