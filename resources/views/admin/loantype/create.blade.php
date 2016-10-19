@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการประเภทเงินกู้พิเศษ
        <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
    </h1>

    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'ข้อมูลประเภทเงินกู้พิเศษ', 'link' => '/admin/loantype'],
        ['item' => 'เพิ่ม', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการประเภทเงินกู้พิเศษของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ประเภทเงินกู้พิเศษของสหกรณ์</p>
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
                <h3 class="box-title">เพิ่มประเภทเงินกู้พิเศษ</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/admin/loantype', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.loantype.form', ['edit' => false])
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
    $('#start_date').datetimepicker({
        locale: 'th',
        viewMode: 'days',
        format: 'YYYY-MM-DD'
    });

    $('#expire_date').datetimepicker({
        locale: 'th',
        viewMode: 'days',
        format: 'YYYY-MM-DD'
    });
    </script>
@endsection