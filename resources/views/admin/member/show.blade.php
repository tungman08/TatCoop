@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
        </h1>

        @if (is_null($member->leave_date))
            @include('admin.layouts.breadcrumb', ['breadcrumb' => [
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => action('Admin\MemberController@index')],
                ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => ''],
            ]])
        @else
            @include('admin.layouts.breadcrumb', ['breadcrumb' => [
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => action('Admin\MemberController@index')],
                ['item' => 'สมาชิกสหกรณ์ที่ลาออก', 'link' => action('Admin\MemberController@getInactive')],
                ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => ''],
            ]])
        @endif
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลสมาชิกสหกรณ์</h4>
            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullname }}</td>
                    </tr>
                    <tr>
                        <th>รหัสสมาชิก:</th>
                        <td>{{ $member->memberCode }}</td>
                    </tr>    
                    <tr>
                        <th>ประเภทสมาชิก:</th>
                        <td>
                            @if (is_null($member->leave_date))
                                <span class="label label-primary">{{ $member->profile->employee->employee_type->name }}</span>
                            @else
                                <span class="label label-danger">ลาออก</span>
                            @endif
                        </td>
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

        <div class="row margin-b-md">
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-primary btn-lg disabled">
                    <i class="fa fa-user fa-fw"></i> ข้อมูลสมาชิก
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-success btn-lg" onclick="javascript:document.location.href='{{ action('Admin\ShareholdingController@index', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-money fa-fw"></i> ทุนเรือนหุ้น
                </button>
            </div>            
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-danger btn-lg" onclick="javascript:document.location.href='{{ action('Admin\LoanController@index', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-credit-card fa-fw"></i> การกู้ยืม
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-warning btn-lg" onclick="javascript:document.location.href='{{ action('Admin\GuarunteeController@index', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-purple btn-lg" onclick="javascript:document.location.href='{{ action('Admin\DividendController@getMemberDividend', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-dollar fa-fw"></i> เงินปันผล
                </button>
            </div>
        </div>
        <!-- /.row -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-user"></i> ข้อมูลของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullname }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                @include('admin.member.profile', ['member' => $member])
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="row">
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user"></i> ข้อมูลผู้รับผลประโยชน์</h3>
                    </div>
                    <!-- /.box-header -->
        
                    <div class="box-body">
                        <input type="file" id="beneficiary" name="beneficiary" class="file-upload" accept="image/jpeg,application/pdf"
                            onchange="javascript:uploadFile(this, {{ $member->id }});" />
                        <button class="btn btn-primary btn-flat margin-b-md"
                            {{ (($is_super || $is_admin) ? '' : 'disabled') }} title="เพิ่มเอกสาร"
                            onclick="$('#beneficiary').click();"><i class="fa fa-plus"></i> เพิ่มเอกสาร
                        </button>
        
                        <div class="table-responsive" style=" margin-top: 10px;">
                            <table id="beneficiaries" class="table table-striped">
                                <tbody>
                                    @if ($member->beneficiaries->count() > 0)
                                        @foreach ($member->beneficiaries->sortByDesc('created_at') as $beneficiary)
                                            <tr id="beneficiary-{{ $beneficiary->id }}">
                                                <td>
                                                    <a href="{{ url(env('APP_URL') . '/storage/file/beneficiaries/' . $beneficiary->file) }}" target="_blank">
                                                        <i class="fa fa-paperclip"></i> {{ Diamond::parse($beneficiary->created_at)->thai_format('j M Y') }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($is_super || $is_admin)
                                                        <span class="text-danger" style="cursor: pointer;" onclick="javascript:var result = confirm('คุณต้องการลบเอกสารนี้ใช่ไหม?'); if (result) { deleteFile('{{ $beneficiary->id }}'); }"><i class="fa fa-times"></i></span>
                                                    @else
                                                        <span>&nbsp;</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="empty">
                                            <td colspan="2" class="text-center" style="color: #ff0000;">ไม่มีข้อมูล</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
        
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!--/.col-->

            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user"></i> ข้อมูลยอดลูกหนี้ เงินรับฝากและทุนเรือนหุ้น</h3>
                    </div>
                    <!-- /.box-header -->
        
                    <div class="box-body">
                        <div class="table-responsive" style=" margin-top: 10px;">
                            <table id="dataTables-cashflow" class="table table-hover dataTable" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">#</th>
                                        <th style="width: 90%;">ปี</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($index = 0)
                                    @for ($year = Diamond::today()->year - 1; $year >= $startYear; $year--)
                                        <tr onclick="javascript: document.location.href  = '{{ action('Admin\MemberController@getCashflow', ['id'=>$member->id, 'year'=>$year]) }}';"
                                            style="cursor: pointer;">
                                            <td>{{ ++$index }}.</td>
                                            <td class="text-primary"><i class="fa fa-file-o fa-fw"></i> ข้อมูลปี {{ $year + 543 }}</td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                            <!-- /.table -->
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!--/.col-->
        </div>
        <!--/.row-->
    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>

    <!-- Leave Modal -->
    <div id="leaveModal" class="modal fade" role="dialog">
        <input type="hidden" id="member_id" value="{{ $member->id }}" />
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ลาออกจากสมาชิก</h4>
                </div>
                <div class="modal-body">
                    <label for="leave_date">วันที่ลาออก</label>
                    <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="text" id="leave_date" 
                            placeholder="กรุณาเลือกจากปฏิทิน..." 
                            autocomplete="off"
                            class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="leave_btn" class="btn btn-danger btn-flat">
                        <i class="fa fa-user-times"></i> ลาออก
                    </button>
                </div>
            </div>
        </div>
    </div> 
@endsection

@section('styles')
    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DataTable JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('[data-tooltip="true"]').tooltip();

        $('#leave_date').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: false,
            focusOnShow: false,
            buddhismEra: true
        });

        $('#leave_btn').click(function() {
            if($('#leave_date').val() != '') {
                let leave_date = $('#leave_date').val();

                document.location.href = "/service/member/" + $('#member_id').val() + "/leave/" + leave_date;
            }
        });

        $('#dataTables-cashflow').dataTable({
            "iDisplayLength": 10
        });     
    });

    function uploadFile(doc, id) {
        var file = $(doc).get(0).files[0];
        var formData = new FormData();
        formData.append('ID', id);
        formData.append('File', file);

        if(file.size < 20971520) {
            $("tr[id=empty]").remove();
            $("tr[id=uploading]").remove();
            $('#beneficiaries > tbody').prepend('<tr id="uploading"><td id="caption" colspan="2" class="text-center"><i class="fa fa-spinner fa-pulse"></i> Uploading... (<span id="progress">0</span>%)</td></tr>');

            $.ajax({
                dataType: 'json',
                url: '/ajax/uploadbeneficiary',
                type: 'post',
                cache: false,
                data: formData,
                processData: false,
                contentType: false,
                error: function(xhr, ajaxOption, thrownError) {
                    $('#caption').addClass('text-danger');
                    $('#caption').html('<i class="fa fa-times-circle fa-fw"></i> Upload failed.');
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                beforeSend: function() {
                    $('#caption').html('<i class="fa fa-spinner fa-pulse"></i> Uploading... (<span id="progress">0</span>%)');
                },
                xhr: function(){
                    // get the native XmlHttpRequest object
                    var xhr = $.ajaxSettings.xhr() ;
                    // set the onprogress event handler
                    xhr.upload.onprogress = function (evt) { $('#progress').html(Math.ceil((evt.loaded / evt.total) * 100)); };
                    // set the onload event handler
                    xhr.upload.onload = function (){ $('#caption').html('<i class="fa fa-clock-o fa-fw"></i> Please wait...'); };
                    // return the customized object
                    return xhr;
                },
                success: function (obj) {
                    $("tr[id=uploading]").remove();
                    $('#beneficiaries > tbody').prepend('<tr id="beneficiary-' + obj.id + 
                        '"><td class="text-primary"><a href="' + obj.link + 
                        '" target="_blank"><i class="fa fa-paperclip"></i> ' + obj.display +
                        '</a></td><td><span class="text-danger" style="cursor: pointer;" onclick="javascript:var result = confirm(\'คุณต้องการลบเอกสารนี้ใช่ไหม?\'); if (result) { deleteFile(\'' + obj.id +
                        '\'); }"><i class="fa fa-times"></i></span></td></tr>');
                }
            });
        }
        else {
            alert('เอกสารที่ใช้ต้องมีขนาดไม่เกิน 20M');
        }
    }

    function deleteFile(id) {
        $.ajax({
            dataType: 'json',
            url: '/ajax/deletebeneficiary',
            type: 'post',
            cache: false,
            data: {
                'id': id
            },
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function(id) {
                $('#beneficiary-' + id).remove();

                if ($('#beneficiaries > tbody > tr').length == 0) {
                    $('#beneficiaries > tbody').prepend('<tr id="empty"><td colspan="2" class="text-center">ไม่มีข้อมูล</td></tr>');
                }
            }
        });
    }
    </script>
@endsection