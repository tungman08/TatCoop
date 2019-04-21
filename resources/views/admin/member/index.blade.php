@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข้อมูลสมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">จำนวนสมาชิก ณ ปัจจุบัน:</th>
                        <td>{{ number_format($members, 0, '.', ',') }} คน</td>
                    </tr>
                    <tr>
                        <th>สมาชิกล่าสุด:</th>
                        <td>{{ $last_member->memberCode }} - {{ $last_member->profile->fullname }}</td>
                    </tr> 
                    <tr>
                        <th>เป็นสมาชิกเมื่อ:</th>
                        <td>{{ Diamond::parse($last_member->start_date)->thai_format('j M Y') }}</td>
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
                <h3 class="box-title"><i class="fa fa-users"></i> รายชื่อสมาชิกสหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat margin-b-md" type="button"
                    {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                    data-tooltip="true" title="เพิ่มสมาชิกสหกรณ์"
                    onclick="javascript:document.location.href='{{ action('Admin\MemberController@create') }}';">
                    <i class="fa fa-user-plus"></i> เพิ่มสมาชิกสหกรณ์
                </button>

                <button class="btn btn-default btn-flat margin-b-md pull-right" type="button" data-tooltip="true" title="สมาชิกสหกรณ์ที่ลาออก"
                    onclick="javascript:document.location.href='{{ action('Admin\MemberController@getInactive') }}';">
                    <i class="fa fa-trash"></i> แสดงสมาชิกที่ลาออก
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 20%;">ชื่อสมาชิก</th>
                                <th style="width: 20%;">ประเภทสมาชิก</th>
                                <th style="width: 25%;">วันที่สมัคร</th>
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
                "url": "/ajax/members",
                "type": "post",
                "data": {
                    "type": "active"
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
                { "data": "startdate" },
                { "data": "status" }
            ]
        });   

        $('#dataTables-users tbody').on('click', 'tr', function() {
            document.location.href  = '/service/member/' + parseInt($(this).children("td").first().html());            
        });         
    });   
    </script>
@endsection