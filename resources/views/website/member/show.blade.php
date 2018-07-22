@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลสมาชิก
        <small>รายละเอียดของสมาชิก สอ.สรทท.</small>
    </h1>
    @include('website.member.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'ข้อมูลสมาชิก', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลสมาชิกสหกรณ์</h4>
            <p>ข้อมูลของ {{ $member->profile->fullName }} รหัสสมาชิก: {{ $member->memberCode }}</p>
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
                <h3 class="box-title">รายละเอียดข้อมูลสมาชิก</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat"
                    style="margin-bottom: 5px;"
                    title="แก้ไขข้อมูล"
                    onclick="javascript:window.location = '/member/{{ $user->member_id }}/edit';">
                    <i class="fa fa-edit"></i> แก้ไขข้อมูล
                </button>

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:20%; border-top: none;">หมายเลขสมาชิก:</th>
                            <td style="border-top: none;">{{ $member->member_code }}</td>
                        </tr>
                        <tr>
                            <th>ชื่อ:</th>
                            <td>
                                {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullName }} 
                                {!! !is_null($member->leave_date) ? ' <span class="text-danger">(ออกจากสมาชิกแล้ว)<span>' : '' !!}
                            </td>
                        </tr>
                        <tr>
                            <th>หมายเลขบัตรประชาชน:</th>
                            <td>{{ $member->profile->citizen_code }}</td>
                        </tr>
                        <tr>
                            <th>รหัสพนักงาน:</th>
                            <td>{{ $member->profile->employee->code }}</td>
                        </tr>
                        <tr>
                            <th>ประเภทสมาชิก:</th>
                            <td>{{ $member->profile->employee->employee_type->name }}</td>
                        </tr>
                        <tr>
                            <th>จำนวนหุ้นต่อเดือน:</th>
                            <td>{{ number_format($member->shareholding, 0,'.', ',') }} หุ้น ({{ number_format($member->shareholding * 10, 2,'.', ',') }} บาท)</td>
                        </tr>
                        <tr>
                            <th>ทุนเรือนหุ้นสะสม:</th>
                            <td>{{ number_format($member->shareholdings()->sum('amount'), 2,'.', ',') }} บาท</td>
                        </tr>
                        <tr>
                            <th>วันเกิด:</th>
                            <td>{{ (!empty($member->profile->birth_date)) ? Diamond::parse($member->profile->birth_date)->thai_format('Y-m-d') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>ที่อยู่:</th>
                            <td>{{ ($member->profile->address == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullAddress }}</td>
                        </tr>
                        <tr>
                            <th>เป็นสมาชิกเมื่อ:</th>
                            <td>{{ Diamond::parse($member->start_date)->thai_format('Y-m-d') }}</td>
                        </tr>
                        @if (!is_null($member->leave_date))
                            <tr>
                                <th>ออกจากสมาชิกเมื่อ:</th>
                                <td>{{ Diamond::parse($member->leave_date)->thai_format('Y-m-d') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>ประวัติการเป็นสมาชิก:</th>
                            <td style="padding: 0px;">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>หมายเลขสมาชิก</th>
                                            <th>สมัครเป็นสมาชิกเมื่อ</th>
                                            <th>ออกจากสมาชิกเมื่อ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($histories as $history)
                                            <tr>
                                                <td>{{ $history->memberCode }}</td>
                                                <td>{{ Diamond::parse($history->start_date)->thai_format('Y-m-d') }}</td>
                                                <td>{{ is_null($history->leave_date) ? '-' : Diamond::parse($history->leave_date)->thai_format('Y-m-d') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
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
    @parent
@endsection

@section('scripts')
    @parent
@endsection