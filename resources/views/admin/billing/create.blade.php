@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการใบรับเงินค่าหุ้น
            <small>รายละเอียดข้อมูลใบเสร็จรับเงินค่าหุ้นของสมาชิก</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการใบรับเงินค่าหุ้น', 'link' => 'admin/billing'],
            ['item' => 'เพิ่ม', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>จัดการใบรับเงินค่าหุ้น</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม แก้ไข ชื่อผู้จัดการและเหรัญญิกได้</p>
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
                <h3 class="box-title">แก้ไขชื่อผู้จัดการและเหรัญญิก</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/admin/billing', 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group padding-l-md padding-r-md">
                        {{ Form::label('manager', 'ผู้จัดการ', [
                            'class'=>'control-label']) 
                        }}
                        {{ Form::text('manager', null, [
                            'id' => 'manager',
                            'placeholder' => 'ตัวอย่าง: ร.ต.อ.วศิน มีปรีชา',
                            'autocomplete'=>'off',
                            'class'=>'form-control'])
                        }}
                    </div>

                    <div class="form-group padding-l-md padding-r-md">
                        {{ Form::label('treasurer', 'เหรัญญิก', [
                            'class'=>'control-label']) 
                        }}
                        {{ Form::text('treasurer', null, [
                            'id' => 'treasurer',
                            'placeholder' => 'ตัวอย่าง: น.ส.พัชราภา ไชยเชื้อ',
                            'autocomplete'=>'off',
                            'class'=>'form-control'])
                        }}
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
                        'type' => 'submit', 
                        'class'=>'btn btn-primary btn-flat'])
                    }}
                    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
                        'class'=>'btn btn-default btn-flat', 
                        'onclick'=>'javascript:history.go(-1);'])
                    }}
                </div>
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
    @parent
@endsection

@section('scripts')
    @parent
@endsection