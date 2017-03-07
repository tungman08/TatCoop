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
            <th>เงื่อนไข:</th>
            <td>
                <ul class="list-info">
                    @foreach($loantype->limits as $limit)
                        <li>
                            จำนวนเงินกู้ <strong>{{ number_format($limit->cash_begin, 2, '.', ',') }} - {{ number_format($limit->cash_end, 2, '.', ',') }}</strong> บาท 
                            @if ($limit->shareholding > 0) 
                                ต้องใช้หุ้น <strong>{{ $limit->shareholding }}%</strong> ของเงินที่ขอกู้
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
    </table>
</div>