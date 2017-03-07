<div class="table-responsive">
    <table class="table table-info">
        <tr>
            <th style="width:20%;">ประเภท:</th>
            <td>{{ $loantype->name }}</td>
        </tr>
        <tr>
            <th>อัตราดอกเบี้ย:</th>
            <td>{{ $loantype->rate }}%</td>
        </tr>
        <tr>
            <th>วันที่เริ่มใช้:</th>
            <td>{{ (Diamond::minValue()->diffInDays(Diamond::parse($loantype->start_date)) > 0) ? Diamond::parse($loantype->start_date)->thai_format('j F Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>วันที่สิ้นสุดการใช้:</th>
            <td>{{ (Diamond::maxValue()->diffInDays(Diamond::parse($loantype->expire_date)) > 0) ? Diamond::parse($loantype->expire_date)->thai_format('j F Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>เงื่อนไข:</th>
            <td>
                <ul class="list-info">
                    @foreach($loantype->limits as $limit)
                        <li>
                            จำนวนเงินกู้ <strong>{{ number_format($limit->cash_begin, 2, '.', ',') }} - {{ number_format($limit->cash_end, 2, '.', ',') }}</strong> บาท 
                            @if ($limit->shareholding > 0) 
                                ใช้หุ้น <strong>{{ $limit->shareholding }}%</strong> ของเงินที่ขอกู้
                            @endif
                            @if ($limit->shareholding > 0) 
                                ใช้ผู้ค้ำจำนวน <strong>{{ $limit->surety }}</strong> คน
                            @endif
                            สามารถผ่อนชำระได้ไม่เกิน <strong>{{ number_format($limit->period, 0, '.', ',') }}</strong> งวด
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
        <tr>
            <th>จำนวนสัญญาเงินกู้:</th>
            <td>0</td>
        </tr>
    </table>
</div>