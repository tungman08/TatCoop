<div class="table-responsive">
    @php
        $loansCount = $member->loans->filter(function ($value, $key) { return is_null($value->completed_at); })->count();
        $outstanding = $member->loans->filter(function ($value, $key) { return is_null($value->completed_at); })->sum('outstanding');
        $principle = $member->loans->filter(function ($value, $key) { return is_null($value->completed_at); })->sum('payments.principle');
    @endphp

    <table class="table table-info">
        <tr>
            <th style="width:20%;">หมายเลขสมาชิก:</th>
            <td>{{ $member->member_code }}</td>
        </tr>
        <tr>
            <th>จำนวนทุนเรือนหุ้นสะสม:</th>
            <td>{{ number_format($member->shareholdings()->sum('amount'), 2,'.', ',') }} บาท</td>
        </tr>
        <tr>
            <th>เงินต้นคงเหลือทั้งหมด:</th>
            <td>{{ ($outstanding - $principle> 0) ?number_format($outstanding - $principle, 2, '.', ',') . ' บาท' : '-' }} {{ ($loansCount > 0) ? '(สัญญาเงินกู้ ' . number_format($loansCount, 0, '.', ',') . ' สัญญา)' : '' }}</td>
        </tr>                
    </table>
</div>