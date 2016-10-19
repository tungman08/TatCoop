@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสมาชิกสหกรณ์
        <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
    </h1>

    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข้อมูลสมาชิกสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ตั้งรหัสผ่านใหม่ และกำหนดสิทธิ์การเข้าถึงข้อมูล ของผู้สมาชิกสหกรณ์</p>
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
                <h3 class="box-title">รายชื่อสมาชิกสหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat" type="button" data-tooltip="true" title="เพิ่มสมาชิกสหกรณ์"
                    style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.location.href='{{ url('/admin/member/create') }}';">
                    <i class="fa fa-user-plus"></i> เพิ่มสมาชิกสหกรณ์
                </button>
                <button class="btn btn-primary btn-flat" type="button" data-tooltip="true" title="ชำระค่าหุ้นอัตโนมัติ"
                    style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:var result = confirm('คุณต้องการทำรายการชำระเงินค่าหุ้นประจำเดือน {{ Diamond::today()->thai_format('M Y') }} ?'); if (result) { window.location.href='{{ url('/admin/member/shareholding') }}'; }">
                    <i class="fa fa-money"></i> ชำระค่าหุ้นอัตโนมัติ
                </button>
                <button class="btn btn-default btn-flat pull-right" type="button" data-tooltip="true" title="สมาชิกสหกรณ์ที่ลาออก"
                    style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.location.href='{{ url('/admin/member/inactive') }}';">
                    <i class="fa fa-trash"></i> แสดงสมาชิกที่ลาออก
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 21%;">ชื่อสมาชิก</th>
                                <th style="width: 15%;">ประเภทสมาชิก</th>
                                <th style="width: 15%;">จำนวนหุ้นปัจจุบัน</th>
                                <th style="width: 15%;">ทุนเรือนหุ้นสะสม</th>
                                <th style="width: 24%;">วันที่สมัคร</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                            <tr onclick="javascript: document.location = '{{ url('/admin/member/' . $member->id) }}';"
                                style="cursor: pointer;">
                                <td>{{ $member->memberCode }}</td>
                                <td class="text-primary"><i class="fa fa-user fa-fw"></i> {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullName }} </td>
                                <td>{{ $member->profile->employee->employee_type->name }}</td>
                                <td>{{ number_format($member->shareholding, 0,'.', ',') }} หุ้น</td>
                                <td>{{ number_format($member->shareholdings()->sum('amount'), 2,'.', ',') }} บาท</td>
                                <td>{{ Diamond::parse($member->start_date)->thai_format('j M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
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
    });

    $('#dataTables-users').dataTable({
        "iDisplayLength": 25
    });
    </script>
@endsection