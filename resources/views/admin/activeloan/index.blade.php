@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ตรวจสอบสัญญาเงินกู้ที่กำลังผ่อนชำระ
            <small>แสดงรายการสัญญาเงินกู้ทุกประเภทที่กำลังผ่อนชำระอยู่</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ตรวจสอบสัญญาเงินกู้ฯ', 'link' => ''],
        ]])
    </section>

        <!-- Main content -->
        <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ตรวจสอบสัญญาเงินกู้ที่กำลังผ่อนชำระ</h4>
            <p>ให้ผู้ดูแลระบบสามารถดูรายการสัญญาเงินกู้ทุกประเภทที่กำลังผ่อนชำระอยู่</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-money"></i> สัญญาเงินกู้ที่กำลังผ่อนชำระ</h3>
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default btn-flat btn-xs"
                        onclick="javascript:window.location.href='{{ url('/admin/inactiveloan/') }}';">
                        <i class="fa fa-check-circle-o"></i> สัญญาเงินกู้ที่ชำระหมดแล้ว
                    </button>
                </div> 
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 15%;">ประเภทเงินกู้</th>
                                <th style="width: 10%;">เลขที่สัญญา</th>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 20%;">ชื่อผู้กู้</th>
                                <th style="width: 10%;">วันที่กู้</th>
                                <th style="width: 10%;">วงเงินที่กู้</th>
                                <th style="width: 10%;">ผ่อนชำระไปแล้ว</th>
                                <th style="width: 10%;">เงินต้นคงเหลือ</th>
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
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(".ajax-loading").css("display", "none");

        $('#dataTables').dataTable().fnDestroy();
        $('#dataTables').dataTable({
            "ajax": {
                "url": "/ajax/activeloan",
                "type": "post",
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
            "rowCallback": function (row, data) {
                $(row).attr('data-memberid', data.memberid);
                $(row).attr('data-loanid', data.loanid);
            },
            "columns": [
                { "data": "index" },
                { "data": "loantype" },
                { "data": "loancode" },
                { "data": "membercode" },
                { "data": "membername" },
                { "data": "loandate" },
                { "data": "outstanding" },
                { "data": "period" },
                { "data": "priciple" }
            ]
        });  
        
        $('#dataTables tbody').on('click', 'tr', function() {
            document.location = '/service/' + parseInt($(this).data('memberid')) + '/loan/' + parseInt($(this).data('loanid'));            
        });            
    });   
    </script>
@endsection