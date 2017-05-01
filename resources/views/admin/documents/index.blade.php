@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เอกสาร/แบบฟอร์ม
            <small>การจัดการเอกสารและแบบฟอร์มต่างๆ ที่แสดงในเว็บไซต์ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'เอกสาร/แบบฟอร์ม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>เอกสาร/แบบฟอร์ม</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข การจัดการเอกสารและแบบฟอร์มต่างๆ</p>
        </div>

        <!-- Tab Panel -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#rules" data-toggle="tab"><strong><i class="fa fa-files-o fa-fw"></i> ระเบียบ/คำสั่ง/ข้อบังคับ</a></strong></li>
                <li><a href="#forms" data-toggle="tab"><strong><i class="fa fa-files-o fa-fw"></i> ใบสมัคร/แบบฟอร์มต่างๆ</a></strong></li>
                <li><a href="#others" data-toggle="tab"><strong><i class="fa fa-files-o fa-fw"></i> เอกสารอื่นๆ</a></strong></li>                   
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="rules">
                    @include('admin.documents.rules')
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="forms">
                    @include('admin.documents.forms')
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="others">
                    @include('admin.documents.others')
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
    @parent
@endsection

@section('scripts')
    @parent

    {{ Html::script(elixir('js/admin-document.js')) }}  
@endsection