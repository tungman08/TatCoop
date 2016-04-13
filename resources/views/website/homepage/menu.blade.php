<img class="logo" src="{{ asset('images/logo-coop.png') }}" alt="tatcoop logo" />
<p class="lead">ข้อมูลข่าวสารสำหรับสมาชิก</p>

<div class="list-group">
    <ul class="nav" id="side-menu">
    <li>
        <a href="#" class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> ระเบียบ/คำสั่ง/ข้อบังคับ
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level">
            <li>
                <a href="{{ url('/announce/loanreg')}}">{{ str_limit('ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 3) พ.ศ. 2554 (เงินกู้สามัญ)', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 5) พ.ศ. 2558 (เงินกู้เพื่อเหตุฉุกเฉิน)', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ระเบียบฯ ว่าด้วยการใช้ทุนสาธารณประโยชน์ พ.ศ. 2549', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ระเบียบฯ ว่าด้วยการใช้ทุนให้สวัสดิการแก่สามชิกและครอบครัว พ.ศ. 2549', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ระเบียบฯ ว่าด้วยพนักงานและลูกจ้าง พ.ศ. 2547', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ระเบียบฯ ว่าด้วยอัตราเงินเดือนพนักงานสหกรณ์ พ.ศ. 2547', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ระเบียบฯ ว่าด้วยการถือหุ้น พ.ศ. 2558', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ข้อบังคับ', 30) }}</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="#" class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> ใบสมัคร/แบบฟอร์มต่าง ๆ
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level">
            <li>
                <a href="#">{{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการท่องเที่ยว', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อดำรงชีพ', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการศึกษา', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจ', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('แบบแจ้งผู้ประสบอุทกภัย', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('หนังสือตั้งผู้รับโอนประโยชน์', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('หนังสือแสดงความยินยอมให้หักเงินเดือนคู่สมรส (กรณีติดตาม)', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('หนังสือให้คำยินยอมหักเงินเดือนหรือค่าจ้างหรือบำเหน็จ', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ใบรับรองข้อมูลสมาชิก', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ใบสมัครสมาชิกสหกรณ์ฯ', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ใบขอเพิ่ม-ลดหุ้น', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ใบลาออก', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('หนังสือแจ้งการเปลี่ยนแปลงผู้ค้ำประกันเงินกู้สามัญ', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ตัวอย่างการกรอกคำขอกู้เงินสามัญและเอกสารประกอบต่าง ๆ', 30) }}</a>
            </li>
            <li>
                <a href="#">{{ str_limit('ตัวอย่างการกรอกคำขอกู้เงินและหนังสือกู้เงินเพื่อเหตุฉุกเฉิน', 30) }}</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="{{ url('/announce/status') }}"class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> สถานะทางการเงิน
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/reports') }}" class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> สรุปยอดเงินฝาก/การซื้อสลาก
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/loanrate') }}" class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> {{ str_limit('ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด', 30) }}
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/newrate') }}" class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> ประกาศอัตราดอกเบี้ยใหม่
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/board') }}" class="list-group-item">
            <i class="fa fa-users fa-fw"></i> คณะกรรมการดำเนินการสหกรณ์ฯ
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/employee') }}" class="list-group-item">
            <i class="fa fa-users fa-fw"></i> เจ้าหน้าที่ประจำสหกรณ์ฯ
        </a>
    </li>
</ul>
</div>
