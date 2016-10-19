<button class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
    onclick="javascript:window.location = '/admin/member/{{ $member->id }}/loan/create';">
    <i class="fa fa-plus"></i> เพิ่มสัญญาการกู้ยืม
</button>

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