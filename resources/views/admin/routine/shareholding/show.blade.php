@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ชำระค่าหุ้นปกติ
            <small>ชำระค่าหุ้นปกติ สำหรับสมาชิกประเภทพนักงาน/ลูกจ้าง ททท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ชำระค่าหุ้นปกติ', 'link' => action('Admin\RoutineShareholdingController@index')],
            ['item' => Diamond::parse($routine->calculated_date)->thai_format('M Y'), 'link' => ''],
        ]])
    </section>
    
    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ชำระค่าหุ้นปกติ</h4>    
            <p>
                ให้ผู้ดูแลระบบตรวจสอบความถูกต้องของข้อมูลที่ระบบคำนวณค่ายอดเงินที่ได้ถูกต้องหรือไม่<br />
                หากไม่ทำการตรวจสอบ การใช้การบันทึกอัตโนมัติจะไม่ทำงาน
            </p>  
            
            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">จำนวนสมาชิก:</th>
                        <td>{{ number_format($routine->details->count(), 0, '.', ',') }} คน</td>
                    </tr>
                    <tr>
                        <th>ค่าหุ้นปกติทั้งหมด:</th>
                        <td>{{ number_format($routine->details->sum('amount'), 0, '.', ',') }} บาท</td>
                    </tr>  
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 

            <div class="form-group" style="margin-bottom: 0px;">
                <input type="hidden" id="routine_status" value="{{ $routine->status }}" />
                <input type="hidden" id="month_id" value="{{ $routine->id }}" />
                <div id="approve-toggle" class="toggle-btn">
                    <label class="toggle-label">ตรวจสอบความถูกต้อง</label>
                    <input type="checkbox" id="approve" class="cb-value"  />
                    <span class="round-btn"></span>
                </div>
            </div>
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

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-eur"></i> รายละเอียดยอดการชำระค่าหุ้นปกติ ของสมาชิกประเภทพนักงาน/ลูกจ้าง ททท. ประจำเดือน{{ Diamond::parse($routine->calculated_date)->thai_format('F Y') }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        {{ Form::open(['action' => ['Admin\RoutineShareholdingController@save', $routine->id], 'method' => 'post', 'class' => 'form', 'onsubmit' => "return confirm('คุณต้องการบันทึกข้อมูลทั้งหมดใช่ไหม?');"]) }}
                            {{ Form::button('<i class="fa fa-floppy-o"></i> บันทึกทั้งหมด', [
                                'id'=>'save_all',
                                'type'=>'submit', 
                                'class'=>'btn btn-primary btn-flat margin-b-md',
                                'disabled'=>true])
                            }}  
                        {{ Form::close() }}
                    </div>
                    <!--/.col-->

                    <div class="col-md-6">
                        {{ Form::open(['action' => ['Admin\RoutineShareholdingController@report', $routine->id], 'method' => 'post', 'class' => 'form']) }}
                            {{ Form::button('<i class="fa fa-file-excel-o"></i> บันทึกเป็น Excel', [
                                'id'=>'report',
                                'type' => 'submit', 
                                'class'=>'btn btn-default btn-flat margin-b-md pull-right'])
                            }}  
                        {{ Form::close() }}
                    </div>
                    <!--/.col-->
                </div>
                <!--/.row-->

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 20%;">ชื่อสมาชิก</th>
                                <th style="width: 15%;">วันที่ชำระ</th>
                                <th style="width: 15%;">จำนวนหุ้น</th>
                                <th style="width: 15%;">ค่าหุ้น</th>
                                <th style="width: 15%;">ทุนเรือนหุ้นสะสม</th>
                                <th style="width: 10%;"><i class="fa fa-gear"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail) 
                                <tr>
                                    <td>{{ $detail->membercode }}</td>
                                    <td class="text-primary">{{ $detail->fullname }}</td>
                                    <td><span class="label label-primary">{{ $detail->paydate }}</span></td>
                                    <td>{{ $detail->shareholding }}</td>
                                    <td>{{ $detail->amount }}</td>
                                    <td>{{ $detail->total }}</td>
                                    <td>    
                                        @if (!$detail->status || (is_null($routine->approved_at) && !$routine->status && Diamond::today()->greaterThan(Diamond::parse($routine->saved_at))))   
                                            <div class="btn-group">
                                                {{--<button type="button" class="btn btn-default btn-flat btn-xs"
                                                    onclick="javascript: save_detail({{ $detail->id }});">
                                                    <i class="fa fa-floppy-o"></i>
                                                </button>--}}
                                                <button type="button" class="btn btn-default btn-flat btn-xs"
                                                    onclick="javascript: document.location.href='{{ action('Admin\RoutineShareholdingController@editDetail', ['id' => $detail->id]) }}';">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-default btn-flat btn-xs"
                                                    onclick="javascript: delete_detail({{ $detail->id }});">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        @else 
                                            <span class="label label-primary">บันทึกข้อมูลแล้ว</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ Form::open(['action' => ['Admin\RoutineShareholdingController@saveDetail'], 'id' => 'save_detail_form', 'method' => 'post', 'role' => 'form', 'onsubmit' => "return confirm('คุณต้องการบันทึกข้อมูลนี้ใช่ไหม?');"]) }}
                        {{ Form::hidden('detail_id', null, [ 'id' => 'detail_id' ]) }}
                    {{ Form::close() }}

                    {{ Form::open(['action' => ['Admin\RoutineShareholdingController@deleteDetail', 0], 'id' => 'delete_delete_form', 'method' => 'delete', 'role' => 'form', 'onsubmit' => "return confirm('คุณต้องการลบข้อมูลนี้ใช่ไหม?');"]) }}
                    {{ Form::close() }}
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

    <!-- Toggle Switch CSS -->
    {!! Html::style(elixir('css/toggle-switch.css')) !!}

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

            $('#approve-toggle').click(() => {
                let routine = $('#routine_status').val();
                let id = $('#month_id').val();
                let status = $('#approve').is(':checked');

                if (routine !== "1") {
                    toggle(id, status);
                }           
            });

            init($('#month_id').val());

            $('#dataTables').dataTable({
                "iDisplayLength": 25,
                "columnDefs": [
                    { type: 'formatted-num', targets: 3 },
                    { type: 'formatted-num', targets: 4 },
                    { type: 'formatted-num', targets: 5 }
                ]
            });
        });

        function init(id) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/autoshareholdingapprove',
                type: 'post',
                data: { 
                    id: id
                },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function(result) {
                    set_toggle_switch(result);

                    $(".ajax-loading").css("display", "none");
                }
            });
        }

        function toggle(id, status) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/autoshareholdingsetapprove',
                type: 'post',
                data: { 
                    id: id,
                    status: status 
                },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function(result) {
                    set_toggle_switch(result);

                    $(".ajax-loading").css("display", "none");
                }
            });
        }

        function save_detail(id) {
            $('#detail_id').val(id);

            $('#save_detail_form').submit();
        }

        function delete_detail(id) {
            let url = $("#delete_delete_form").attr("action");
            let action = url.substr(0, url.lastIndexOf("/") + 1) + id;

            $("#delete_delete_form").attr("action", action);
            $('#delete_delete_form').submit();
        }

        function set_toggle_switch(status) {
            let routine = $('#routine_status').val();

            $('#approve').prop("checked", status);

            if (routine !== "1") {
                $('#save_all').prop("disabled", !status);
            }    

            if (status) {
                $('#approve-toggle').addClass('active');
            }
            else {
                $('#approve-toggle').removeClass('active');
            }
        }
    </script>
@endsection