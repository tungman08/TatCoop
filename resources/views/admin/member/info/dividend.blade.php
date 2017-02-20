<div class="form-group">
    <div class="col-md-2" style="padding-left: 0px; margin-bottom: 15px;">
        <select class="form-control" id="selectyear" autocomplete="off">
            @forelse($dividend_years as $year)
                <option value="{{ $year->pay_year }}"{{ (intval($year->pay_year) == Diamond::today()->year) ? ' selected' : '' }}>เงินปันผลปี {{ $year->pay_year + 543 }}</option>
            @empty
                <option value="{{ Diamond::today()->year }}">เงินปันผลปี {{ Diamond::today()->year + 543 }}</option>
            @endforelse
        </select>
    </div>
    @php($rate = App\Dividend::where('rate_year', Diamond::today()->year)->first())
    <strong id="dividend_rate" class="col-md-10" style="margin-top: 8px;">{{ !is_null($rate) ? 'อัตราเงินปันผล ' . $rate->rate . '%' : 'ยังไม่ได้กำหนดอัตราเงินปันผล' }}</strong>
</div>

<table id="dividend" class="table table-hover dataTable">
    <thead>
        <tr>
            <th style="width: 20%;">#</th>
            <th style="width: 20%;">จำนวนหุ้น</th>
            <th style="width: 20%;">จำนวนเงิน</th>
            <th style="width: 20%;">เงินปันผล</th>
            <th style="width: 20%;">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        @php
            $index = 0;
            $total_amount = 0;
            $total_shareholding = 0;
            $total_dividend = 0;
        @endphp

        @foreach ($dividends as $dividend)
            <tr>
                <td class="text-primary">{{ ($index == 0) ? $dividend->name : Diamond::parse($dividend->name)->thai_format('F Y') }}</td>
                <td>{{ number_format($dividend->shareholding, 0, '.', ',') }} หุ้น</td>
                <td>{{ number_format($dividend->amount, 2, '.', ',') }} บาท</td>
                <td>{{ ((!is_null($member->leave_date)) ? 
                        (Diamond::parse($member->leave_date)->year <= Diamond::today()->year) ? 
                            '0.00' : 
                            number_format($dividend->dividend, 2, '.', ',') : 
                            number_format($dividend->dividend, 2, '.', ',')) 
                    }} 
                </td>
                <td{!! ((is_null($member->leave_date)) ? (!is_null($rate)) ? ' ' : ' class="text-danger"' : ' class="text-danger"') !!}>
                    {{ ((is_null($member->leave_date)) ? $dividend->remark : 'ลาออกแล้ว') }}
                </td>
            </tr>

            @php
                $index++;
                $total_amount += $dividend->amount;
                $total_shareholding += $dividend->shareholding;
                $total_dividend += $dividend->dividend;
            @endphp

        @endforeach
        <tr>
            <td class="text-primary"><strong>รวม</strong></td>
            <td><strong>{{ number_format($total_shareholding, 0, '.', ',') }} หุ้น</strong></td>
            <td><strong>{{ number_format($total_amount, 2, '.', ',') }} บาท</strong></td>
            <td class="text-success"><strong>{{ 
                ((!is_null($member->leave_date)) ? 
                    (Diamond::parse($member->leave_date)->year <= Diamond::today()->year) ? 
                        '0.00' :
                        number_format($total_dividend, 2, '.', ',') : 
                        number_format($total_dividend, 2, '.', ','))
                }} บาท</strong></td>
            <td{!! (is_null($member->leave_date)) ? (!is_null($rate)) ? (Diamond::today()->mount == 12) ? '' : ' class="text-warning' : ' class="text-danger"' : ' class="text-danger"' !!}>
                {{ (is_null($member->leave_date)) ? 
                    (!is_null($rate)) ? 
                        (Diamond::today()->mount == 12) ? 
                            '' : 
                            'อัตราเงินปันผล ' . Diamond::today()->mount . ' เดือน' : 
                            'ยังไม้ได้กำหนดอัตราเงินปันผล' :
                            'ลาออกแล้ว' }}
            </td>
        </tr> 
    </tbody>
</table>
