@if (is_null($member->leave_date))
    <button class="btn btn-primary btn-flat"
        title="แก้ไขข้อมูล"
        onclick="javascript:window.location = '/service/member/{{ $member->id }}/edit';">
        <i class="fa fa-edit"></i> แก้ไขข้อมูล
    </button>

    <button class="btn btn-danger btn-flat pull-right"
        title="ลาออกจากสมาชิก"
        onclick="javascript:window.location = '/service/member/{{ $member->id }}/leave';">
        <i class="fa fa-user-times"></i> ลาออกจากสมาชิก
    </button>
@endif

<div class="table-responsive margin-t-xl">
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
            <td>{{ $member->profile->CitizenCode }}</td>
        </tr>
        <tr>
            <th>รหัสพนักงาน:</th>
            <td>{{ $member->profile->employee->code }}</td>
        </tr>
        <tr>
            <th>ประเภทสมาชิก:</th>
            <td>{!! (is_null($member->leave_date)) ? '<span class="label label-primary">' . $member->profile->employee->employee_type->name . '</span>' : '<span class="label label-danger">ลาออก</span>' !!}</td>
        </tr>
        <tr>
            <th>วันเกิด:</th>
            <td>{{ (!empty($member->profile->birth_date)) ? Diamond::parse($member->profile->birth_date)->thai_format('j M Y') : '-' }}</td>
        </tr>
        <tr>
            <th>ที่อยู่:</th>
            <td>{{ ($member->profile->address == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullAddress }}</td>
        </tr>
        <tr>
            <th>เป็นสมาชิกเมื่อ:</th>
            <td>{{ Diamond::parse($member->start_date)->thai_format('j M Y') }}</td>
        </tr>
        @if (!is_null($member->leave_date))
            <tr>
                <th>ออกจากสมาชิกเมื่อ:</th>
                <td>{{ Diamond::parse($member->leave_date)->thai_format('j M Y') }}</td>
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
                                <td><a href="/service/member/{{ $history->id }}">{{ $history->memberCode }}</a></td>
                                <td>{{ Diamond::parse($history->start_date)->thai_format('j M Y') }}</td>
                                <td>{{ is_null($history->leave_date) ? '-' : Diamond::parse($history->leave_date)->thai_format('j M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <th>บัญชีบริการอิเล็กทรอนิกส์:</th>
            <td>{!! $account !!}</td>
        </tr>
    </table>
</div>