@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            การค้ำประกันของสมาชิกสหกรณ์ฯ
            <small>แสดงรายละเอียดข้อมูลการค้ำประกันของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลการค้ำประกัน', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลการค้ำประกันของสมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">สมาชิกที่ค้ำเต็มแล้ว:</th>
                        <td>{{ number_format($full, 0, '.', ',') }} คน</td>
                    </tr>
                    <tr>
                        <th>สมาชิกที่ยังสามารถค้ำได้ 1 สัญญา:</th>
                        <td>{{ number_format($available1, 0, '.', ',') }} คน</td>
                    </tr> 
                    <tr>
                        <th>สมาชิกที่ยังสามารถค้ำได้ 2 สัญญา:</th>
                        <td>{{ number_format($available2, 0, '.', ',') }} คน</td>
                    </tr> 
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

        @if(Session::has('flash_message'))
            <div class="callout {{ Session::get('callout_class') }}">
                <h4>แจ้งข้อความ!</h4>
                <p>
                    {{ Session::get('flash_message') }}

                    @if(Session::has('flash_link'))
                        <a href="{{ Session::get('flash_link') }}">Undo</a>
                    @endif
                </p>
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-share-alt"></i> รายละเอียดการค้ำประกันของสมาชิกสหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 20%;">ชื่อสมาชิก</th>
                                <th style="width: 15%;">ประเภทสมาชิก</th>
                                <th style="width: 20%;">ค้ำประกันตนเอง</th>
                                <th style="width: 20%;">ค้ำประกันผู้อื่น</th>
								<th style="width: 15%;">ความสามารถในการค้ำฯ ผู้อื่น</th>
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

        $('[data-tooltip="true"]').tooltip();
        $(".ajax-loading").css("display", "none");

        $('#dataTables-users').dataTable().fnDestroy();
        $('#dataTables-users').dataTable({
            //"processing": true,
            //"serverSide": true,
            "ajax": {
                "url": "/ajax/guaruntee",
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
            "columnDefs": [
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 }
            ],
            "columns": [
                { "data": "code" },
                { "data": "fullname" },
                { "data": "typename" },
                { "data": "yourself" },
                { "data": "other" },
				{ "data": "status" }
            ]
        });   

        $('#dataTables-users tbody').on('click', 'tr', function() {
            document.location.href  = '/service/' + parseInt($(this).children("td").first().html()).toString() + '/guaruntee';            
        });         
    });   
    
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