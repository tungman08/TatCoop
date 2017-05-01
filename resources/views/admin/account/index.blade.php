@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            บัญชีผู้ใช้งานระบบฯ
            <small>รายชื่อบัญชีของสมาชิกที่ใช้งานระบบบริการอิเล็กทรอนิกส์</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'บัญชีผู้ใช้งานระบบ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>บัญชีผู้ใช้งานระบบบริการอิเล็กทรอนิกส์</h4>
            <p>แสดงรายชื่อบัญชีของสมาชิกที่ได้ลงทะเบียนเข้าใช้งานระบบบริการอิเล็กทรอนิกส์</p>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-user-circle-o"></i> รายชื่อบัญชีผู้ใช้งานระบบบริการอิเล็กทรอนิกส์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 25%;">ชื่อสมาชิก</th>
                                <th style="width: 25%;">อีเมล</th>
                                <th style="width: 15%;">ลงทะเบียนเมื่อ</th>
                                <th style="width: 25%;">สถานะ</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
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
    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DataTable JavaScript -->
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();
        $(".ajax-loading").css("display", "none");

        $('#dataTables-users').dataTable().fnDestroy();
        $('#dataTables-users').dataTable({
            "ajax": {
                "processing": true,
                "serverSide": true,
                "url": "/ajax/accounts",
                "type": "get",
                beforeSend: function () {
                    $(".ajax-loading").css("display", "block");
                },
                complete: function(){
                    $(".ajax-loading").css("display", "none");
                }       
            },
            "iDisplayLength": 25,
            "createdRow": function(row, data, index) {
                $(this).css('cursor', 'pointer');
            },
            "columns": [
                { "data": "code" },
                { "data": "fullname" },
                { "data": "email" },
                { "data": "register_at" },
                { "data": "status" },
            ]
        });   

        $('#dataTables-users tbody').on('click', 'tr', function() {
            document.location = '/service/member/' + parseInt($(this).children("td").first().html());            
        });         
    });   
    </script>
@endsection