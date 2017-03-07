<div class="table-responsive">
    <table class="table table-info">
        <tr>
            <th style="width:20%;">หมายเลขสมาชิก:</th>
            <td>{{ $member->member_code }}</td>
        </tr>
        <tr>
            <th>ชื่อ:</th>
            <td>{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullName }} </td>
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
            <th>ยอดหนี้คงเหลือ (เฉพาะเงินต้น):</th>
            <td>- บาท</td>
        </tr>                
    </table>
</div>