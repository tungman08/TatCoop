@if (is_null($member->leave_date))
    <div class="btn-group">
        <button class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
            onclick="javascript:window.location.href = '{{ url('/admin/member/' . $member->id . '/loan/createnormal') }}';">
            <i class="fa fa-plus-circle fa-fw"></i> ทำสัญญากู้สามัญ
        </button>
        <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu" style="margin-top: -15px; border-radius: 0px;">
            <li>
                <a style="cursor: pointer;" onclick="javascript:window.location.href ='{{ url('/admin/member/' . $member->id . '/loan/createemerging') }}';">
                    <i class="fa fa-plus-circle"></i> ทำสัญญากู้ฉุกเฉิน
                </a>
            </li>
            <li>
                <a href="#specialModal" style="cursor: pointer;" data-toggle="modal" data-tooltip="true" data-placement="top">
                    <i class="fa fa-plus-circle"></i> ทำสัญญากู้เฉพาะกิจ
                </a>
            </li>
        </ul>
    </div>
@endif

<!-- Special Load Modal -->
<div id="specialModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ประเภทสินเชื่อเฉพาะกิจ</h4>
            </div>
            <div class="modal-body text-center">
                <select id="special_loan" class="form-control">
                    @foreach($special_loans as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-flat margin-t-lg margin-b-lg"
                    onclick="javascript:window.location.href='{{ url('/admin/member/' . $member->id . '/loan/createspecial') }}/' + $('#special_loan').val();">
                    <i class="fa fa-file-o"></i> ทำสัญญา
                </button>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="dataTables-loan" class="table table-hover dataTable" width="100%">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">เลขที่สัญญา</th>
                <th style="width: 15%;">ประเภทเงินกู้</th>
                <th style="width: 10%;">วันที่กู้</th>
                <th style="width: 10%;">วงเงินที่กู้</th>
                <th style="width: 15%;">จำนวนงวดที่ผ่อนชำระ</th>
                <th style="width: 15%;">จำนวนเงินที่ผ่อนชำระแล้ว</th>
                <th style="width: 10%;">ดอกเบี้ยสะสม</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <!-- /.table-responsive -->
</div>