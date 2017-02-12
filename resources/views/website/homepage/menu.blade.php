<img class="logo" src="{{ asset('images/logo-coop.png') }}" alt="tatcoop logo" />
<p class="lead">ข้อมูลข่าวสารสำหรับสมาชิก</p>

<div class="list-group">
    <ul class="nav" id="side-menu">
        <li>
            <a href="{{ url('/documents/rules') }}" class="list-group-item">
                <i class="fa fa-files-o fa-fw"></i> ระเบียบ/คำสั่ง/ข้อบังคับ
                <span class="fa arrow"></span>
            </a>
        </li>
        <li>
            <a href="{{ url('/documents/forms') }}" class="list-group-item">
                <i class="fa fa-files-o fa-fw"></i> ใบสมัคร/แบบฟอร์มต่างๆ
                <span class="fa arrow"></span>
            </a>
        </li>
        <li>
            <a href="{{ url('/documents/status') }}" class="list-group-item">
                <i class="fa fa-file-text-o fa-fw"></i> สถานะทางการเงิน
            </a>
        </li>
        <li>
            <a href="{{ url('/documents/deposit') }}" class="list-group-item">
                <i class="fa fa-file-text-o fa-fw"></i> สรุปยอดเงินฝาก/การซื้อสลาก
            </a>
        </li>
        <li>
            <a href="{{ url('/documents/loan_rate') }}" class="list-group-item" data-tooltip="true" title="ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด">
                <i class="fa fa-file-text-o fa-fw"></i> {{ str_limit('ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด', 30) }}
            </a>
        </li>
        <li>
            <a href="{{ url('/documents/rate') }}" class="list-group-item">
                <i class="fa fa-file-text-o fa-fw"></i> ประกาศอัตราดอกเบี้ยใหม่
            </a>
        </li>
        <li>
            <a href="{{ url('/documents/boards') }}" class="list-group-item">
                <i class="fa fa-users fa-fw"></i> คณะกรรมการดำเนินการสหกรณ์ฯ
            </a>
        </li>
        <li>
            <a href="{{ url('/documents/officers') }}" class="list-group-item">
                <i class="fa fa-users fa-fw"></i> เจ้าหน้าที่ประจำสหกรณ์ฯ
            </a>
        </li>
    </ul>
</div>
