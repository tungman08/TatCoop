@if (is_null($member->leave_date))
    <button class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
        onclick="javascript:window.location = '/admin/member/{{ $member->id }}/shareholding/create';">
        <i class="fa fa-plus"></i> ชำระเงินค่าหุ้น
    </button>
@endif

<div class="table-responsive">
    <table id="dataTables-shareholding" class="table table-hover dataTable" width="100%">
        <thead>
            <tr>
                <th style="width: 10%;">#</th>
                <th style="width: 15%;">วันที่ชำระ</th>
                <th style="width: 15%;">ประเภท</th>
                <th style="width: 15%;">จำนวนเงิน</th>
                <th style="width: 45%;">หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            @eval($count = 0)
            @foreach($member->shareholdings->sortByDesc('pay_date') as $share) 
            <tr onclick="javascript: document.location = '{{ url('admin/member/' . $member->id . '/shareholding/' . $share->id . '/edit') }}';"
                style="cursor: pointer;">
                <td>{{ ++$count }}</td>
                <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ Diamond::parse($share->pay_date)->thai_format('j M Y') }}</td>
                <td>{{ $share->shareholding_type->name }}</td>
                <td>{{ number_format($share->amount, 2, '.', ',') }} บาท</td>
                <td>{{ $share->remark }}</td>
            </tr>
            @endforeach
        </tbody>
        <!-- /.table-responsive -->
    </table>
</div>