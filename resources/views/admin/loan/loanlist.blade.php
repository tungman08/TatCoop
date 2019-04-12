@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ตรวจสอบสัญญาเงินกู้
            <small>แสดงรายการสัญญาเงินกู้ทุกประเภท</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'สัญญาเงินกู้', 'link' => ''],
        ]])
    </section>

        <!-- Main content -->
        <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ตรวจสอบสัญญาเงินกู้</h4>
            <p>ให้ผู้ดูแลระบบสามารถดูรายการสัญญาเงินกู้ทุกประเภท</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-money"></i> สัญญาเงินกู้</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <select class="form-control" id="selecttype" autocomplete="off">
                                <option value="1">สัญญาที่กำลังผ่อนชำระ</option>
                                <option value="2">สัญญาที่ชำระหมดแล้ว</option>
                            </select>
                        </div>
                        <!-- /.col -->               
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.form-group -->

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 10%;">เลขที่สัญญา</th>
                                <th style="width: 15%;">ประเภทเงินกู้</th>
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
    {!! Html::script(elixir('js/formatted-numbers.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(".ajax-loading").css("display", "none");

        loanlist($('#selecttype').val());
        $('#selecttype').change(function() {
            loanlist($(this).val());
        });
    
        $('#dataTables tbody').on('click', 'tr', function() {
            document.location.href  = '/service/' + parseInt($(this).data('memberid')) + '/loan/' + parseInt($(this).data('loanid'));            
        }); 
    });   
    
    function loanlist(selected_type) {
        $('#dataTables').dataTable().fnDestroy();
        $('#dataTables').dataTable({
            "ajax": {
                "url": "/ajax/displayloan",
                "type": "post",
                "data": {
                    "type": selected_type
                },
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
            "columnDefs": [
                { type: 'formatted-num', targets: 6 },
                { type: 'formatted-num', targets: 8 }
            ],
            "columns": [
                { "data": "index" },
                { "data": "loancode" },
                { "data": "loantype" },
                { "data": "membercode" },
                { "data": "membername" },
                { "data": "loandate" },
                { "data": "outstanding" },
                { "data": "period" },
                { "data": "priciple" }
            ]
        });             
    }

    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "formatted-num-pre": function ( a ) {
            a = (a === "-" || a === "") ? 0 : a.replace(/[^\d\-\.]/g, "");
            return parseFloat( a );
        },

        "formatted-num-asc": function ( a, b ) {
            return a - b;
        },

        "formatted-num-desc": function ( a, b ) {
            return b - a;
        }
    });
    </script>
@endsection