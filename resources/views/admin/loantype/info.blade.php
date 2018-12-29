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
            <td>{{ (Diamond::minValue()->diffInDays(Diamond::parse($loantype->start_date)) > 0) ? Diamond::parse($loantype->start_date)->thai_format('j M Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>วันที่สิ้นสุดการใช้:</th>
            <td>{{ (Diamond::maxValue()->diffInDays(Diamond::parse($loantype->expire_date)) > 0) ? Diamond::parse($loantype->expire_date)->thai_format('j M Y') : 'N/A' }} {!! (Diamond::today()->greaterThan(Diamond::parse($loantype->expire_date))) ? '<span class="text-red">(สิ้นสุดการใช้งานแล้ว)</span>' : '' !!}</td>
        </tr>
        <tr>
            <th>เงื่อนไข:</th>
            <td>
                <ul class="list-info">
                    <li><span class="text-blue">[เฉพาะพนักงาน/ลูกจ้าง ททท.]</span> กู้สูงสุดได้ไม่เกิน <strong>{{ $loantype->employee_ratesalary }}</strong> เท่าของเงินเดือน และไม่เกิน <strong>{{ number_format($loantype->limits->max('cash_end'), 2, '.', ',') }}</strong> บาท</li>
                    <li><span class="text-blue">[เฉพาะพนักงาน/ลูกจ้าง ททท.]</span> เงินเดือนสุทธิเมื่อหักค่างวดรายเดือนต้องไม่น้อยกว่า <strong>{{ number_format($loantype->employee_netsalary, 2, '.', ',') }}</strong> บาท</li>
                    <li><span class="text-blue">[เฉพาะบุคคลภายนอก]</span> กู้สูงสุดได้ไม่เกิน <strong>{{ number_format($loantype->outsider_rateshareholding * 100, 0, '.', ',') }}%</strong> ของทุนเรือนหุ้นสะสมทั้งหมด และไม่เกิน <strong>{{ number_format($loantype->limits->max('cash_end'), 2, '.', ',') }}</strong> บาท</li>

                    @if ($loantype->id != 2)
                        <li>จำนวนยอดกู้สามัญ รวมกับยอดกู้เฉพาะกิจอื่นๆ (ไม่รวมกู้ฉุกเฉิน) ต้องไม่เกิน <strong>{{ number_format($loantype->max_loansummary, 2, '.', ',') }}</strong> บาท</li>
                    @endif

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
    </table>
</div>