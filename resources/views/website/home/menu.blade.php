<img class="logo" src="{{ asset('images/logo-coop.png') }}" alt="tatcoop logo" />
<p class="lead">สอ.สรทท.</p>

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

@if (Request::is('/'))
    <div>
        บราวเซอร์ที่รองรับ
        <ul class="list-inline margin-t-sm">
            <li class="margin-r-xs" style="cursor: pointer;" data-tooltip="true" title="IE เวอร์ชั่น 9 ขึ้นไป"><i class="fa fa-internet-explorer"></i></li>
            <li class="margin-l-xs margin-r-xs" style="cursor: pointer;" data-tooltip="true" title="Chrome เวอร์ชั่น 8 ขึ้นไป"><i class="fa fa-chrome"></i></li>
            <li class="margin-l-xs margin-r-xs" style="cursor: pointer;" data-tooltip="true" title="Firefox เวอร์ชั่น 19 ขึ้นไป"><i class="fa fa-firefox"></i></li>
            <li class="margin-l-xs margin-r-xs" style="cursor: pointer;" data-tooltip="true" title="Edge ทุกเวอร์ชั่น"><i class="fa fa-edge"></i></li>
            <li class="margin-l-xs margin-r-xs" style="cursor: pointer;" data-tooltip="true" title="Opera เวอร์ชั่น 12 ขึ้นไป"><i class="fa fa-opera"></i></li>
            <li class="margin-l-xs" style="cursor: pointer;" data-tooltip="true" title="Safari เวอร์ชั่น 5 ขึ้นไป"><i class="fa fa-safari"></i></li>
        </ul>
    </div>
@endif