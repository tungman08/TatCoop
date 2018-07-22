@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ข้อมูลเงินปันผลของสมาชิกสหกรณ์ฯ
            <small>แสดงรายละเอียดข้อมูลเงินปันผลของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลเงินปันผล', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลเงินปันผลของสมาชิกสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถดูรายละเอียดข้อมูลเงินปันผลของสมาชิกสหกรณ์</p>
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
                <h3 class="box-title"><i class="fa fa-dollar"></i> รายละเอียดข้อมูลเงินปันผลของสมาชิกสหกรณ์ ปี <span id="year">{{ $year + 543 }}</span></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                {{ Form::open(['url' => '/service/dividend/member/export/' . $year, 'method' => 'post']) }}
                    {{ Form::button('<i class="fa fa-file-excel-o fa-fw"></i> สรุปการปันผลเป็นเอกสาร Excel', [
                        'type'=>'submit',
                        'class'=>'btn btn-primary btn-flat margin-b-md'])
                    }}
                {{ Form::close() }}

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 25%;">ชื่อสมาชิก</th>
                                <th style="width: 20%;">ประเภทสมาชิก</th>
                                <th style="width: 15%;">จำนวนเงินปันผล</th>
                                <th style="width: 15%;">จำนวนเงินเฉลี่ยคืน</th>
                                <th style="width: 15%;">รวมเงินทั้งหมด</th>
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

        $('[data-tooltip="true"]').tooltip();
        $(".ajax-loading").css("display", "none");

        $('#dataTables-users').dataTable().fnDestroy();
        $('#dataTables-users').dataTable({
            //"processing": true,
            //"serverSide": true,
            "ajax": {
                "url": "/ajax/dividendlist",
                "type": "post",
                "data": {
                        "year": {{ $year }}
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
            "columns": [
                { "data": "code" },
                { "data": "fullname" },
                { "data": "typename" },
                { "data": "shareholding" },
                { "data": "interest" },
                { "data": "total" }
            ]
        });   

        $('#dataTables-users tbody').on('click', 'tr', function() {
            document.location = '/service/' + parseInt($(this).children("td").first().html()).toString() + '/dividend';            
        }); 
    });   
    </script>
@endsection