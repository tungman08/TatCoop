<div class="form-group">
    <strong class="col-md-1" style="padding-left: 0px; margin-top: 8px;">เงินปันผลปี: </strong>
    <div class="col-md-1" style="padding-left: 0px; margin-bottom: 15px;">
        <select class="form-control" id="selectyear">
            @if ($dividend_years->count() > 0)
                @foreach($dividend_years as $year)
                    <option value="{{ $year->pay_year }}"{{ ($year->pay_year == Diamond::today()->format('Y')) ? 'selected' : '' }}>{{ $year->pay_year }}</option>
                @endforeach
            @else
                <option value="{{ Diamond::today()->format('Y') }}">{{ Diamond::today()->format('Y') }}</option>
            @endif
        </select>
    </div>
</div>

@eval($rate = App\Dividend::where('rate_year', Diamond::today()->year)->first())

<table id="dividend" class="table table-hover dataTable">
    <thead>
        <tr>
            <th style="width: 20%;">#</th>
            <th style="width: 20%;">จำนวนหุ้น</th>
            <th style="width: 20%;">จำนวนเงิน</th>
            <th style="width: 20%;">เงินปันผล <span id="dividend_rate">({{ !is_null($rate) ? $rate->rate : '0' }}%)</span></th>
            <th style="width: 20%;">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        @eval($index = 0)
        @eval($total_amount = 0)
        @eval($total_shareholding = 0)
        @eval($total_dividend = 0)
        @foreach ($dividends as $dividend)
            <tr>
                <td class="text-primary">{{ ($index == 0) ? $dividend->name : Diamond::parse($dividend->name)->thai_format('F Y') }}</td>
                <td>{{ number_format($dividend->shareholding, 0, '.', ',') }} หุ้น</td>
                <td>{{ number_format($dividend->amount, 2, '.', ',') }} บาท</td>
                <td>{{ number_format($dividend->dividend, 2, '.', ',') }} บาท</td>
                <td{!! (is_null($rate)) ? ' class="text-danger"' : '' !!}>{{ $dividend->remark }}</td>
            </tr>
            @eval($index++)
            @eval($total_amount += $dividend->amount)
            @eval($total_shareholding += $dividend->shareholding)
            @eval($total_dividend += $dividend->dividend)  
        @endforeach
        <tr>
            <td class="text-primary"><strong>รวม</strong></td>
            <td><strong>{{ number_format($total_shareholding, 0, '.', ',') }} หุ้น</strong></td>
            <td><strong>{{ number_format($total_amount, 2, '.', ',') }} บาท</strong></td>
            <td class="text-success"><strong>{{ number_format($total_dividend, 2, '.', ',') }} บาท</strong></td>
            <td{!! (!is_null($rate)) ? (Diamond::today()->mount == 12) ? '' : ' class="text-warning' : ' class="text-danger"' !!}>
                {{ (!is_null($rate)) ? 
                    (Diamond::today()->mount == 12) ? 
                        '' : 
                        'อัตราเงินปันผล ' . Diamond::today()->mount . ' เดือน' : 
                        'ยังไม้ได้กำหนดอัตราเงินปันผล' }}
            </td>
        </tr> 
    </tbody>
</table>
