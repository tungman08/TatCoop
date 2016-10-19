@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสมาชิกสหกรณ์
        <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
    </h1>

    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/admin/member'],
        ['item' => 'สมาชิกสหกรณ์ที่ลาออก', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>สมาชิกสหกรณ์ที่ลาออก</h4>
            <p>แสดงรายละเอียดของสมาชิก สอ.สรทท. ที่ลาออกไปแล้ว</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายชื่อสมาชิกสหกรณ์ที่ลาออกไปแล้ว</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-default btn-flat" style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.history.go(-1);">
                    <i class="fa fa-reply"></i> ถอยกลับ
                </button>

                <div class="table-responsive">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 21%;">ชื่อสมาชิก</th>
                                <th style="width: 15%;">ประเภทสมาชิก</th>
                                <th style="width: 15%;">จำนวนหุ้นปัจจุบัน</th>
                                <th style="width: 15%;">ทุนเรือนหุ้นสะสม</th>
                                <th style="width: 12%;">วันที่สมัคร</th>
                                <th style="width: 12%;">วันที่ลาออก</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                            <tr onclick="javascript: document.location = '{{ url('/admin/member/' . $member->id) }}';"
                                style="cursor: pointer;">
                                <td>{{ $member->memberCode }}</td>
                                <td class="text-primary">{!! ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : '<i class="fa fa-user fa-fw"></i> ' . $member->profile->fullName !!} </td>
                                <td>{{ $member->profile->employee->employee_type->name }}</td>
                                <td>{{ number_format($member->shareholding, 0,'.', ',') }} หุ้น</td>
                                <td>{{ number_format($member->shareholdings()->sum('amount'), 2,'.', ',') }} บาท</td>
                                <td>{{ Diamond::parse($member->start_date)->thai_format('j M Y') }}</td>
                                <td>{{ Diamond::parse($member->leave_date)->thai_format('j M Y') }}</td>
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