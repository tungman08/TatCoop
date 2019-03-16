<div class="col-lg-6">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>
                <i class="fa fa-fw fa-users"></i> บริการสมาชิกสหกรณ์
            </h4>
        </div>
        <div class="panel-body">
            @if (Auth::guard('users')->check())
                @php($member_id = App\User::find(Auth::guard('users')->id())->member_id)

                <a href="{{ url('/member/' . $member_id ) }}" class="btn btn-primary center-block" target="_blank">เข้าใช้งานบริการอิเล็กทรอนิกส์</a>
            @else
                <a href="{{ url('/member') }}" class="btn btn-primary center-block" target="_blank">เข้าใช้งานบริการอิเล็กทรอนิกส์</a>
            @endif

            <br />
            <a href="{{ url('/loan') }}" class="btn btn-success center-block">คำนวณสินเชื่อเงินกู้เบื้องต้น</a>
        </div>
    </div>
</div>
<!-- /.col -->

<div class="col-lg-6">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h4>
                <i class="fa fa-fw fa-calculator"></i> อัตราดอกเบี้ยเงินกู้
            </h4>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ประเภท</td>
                        <th class="text-right">อัตรา</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loan_types as $loan_type)
                        <tr>
                            <td>{{ $loan_type->name }}</td>
                            <td class="text-right">{{ $loan_type->rate }}%</td>
                        </tr>  
                    @endforeach
                <tbody>
            </table>
        </div>
    </div>
</div>
<!-- /.col -->
