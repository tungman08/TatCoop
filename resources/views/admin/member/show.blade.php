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
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/service/member'],
                ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => ''],
            ]])
        @else
            @include('admin.layouts.breadcrumb', ['breadcrumb' => [
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/service/member'],
                ['item' => 'สมาชิกสหกรณ์ที่ลาออก', 'link' => '/service/member/inactive'],
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
                <button type="button" class="btn btn-block btn-success btn-lg" onclick="javascript:document.location.href='{{ url('/service/' . $member->id . '/shareholding') }}';">
                    <i class="fa fa-money fa-fw"></i> ทุนเรือนหุ้น
                </button>
            </div>            
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-danger btn-lg" onclick="javascript:document.location.href='{{ url('/service/' . $member->id . '/loan') }}';">
                    <i class="fa fa-credit-card fa-fw"></i> การกู้ยืม
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-warning btn-lg" onclick="javascript:document.location.href='{{ url('/service/' . $member->id . '/guaruntee') }}';">
                    <i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-purple btn-lg" onclick="javascript:document.location.href='{{ url('/service/' . $member->id . '/dividend') }}';">
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
            buddhism: true
        });

        $('#leave_btn').click(function() {
            if($('#leave_date').val() != '') {
                let leave_date = $('#leave_date').val();

                document.location.href = "/service/member/" + $('#member_id').val() + "/leave/" + leave_date;
            }
        });
    });
    </script>
@endsection