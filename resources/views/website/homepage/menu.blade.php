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
                <a href="{{ url('/announce/loan_reg2009-2') }}" data-tooltip="true" title="ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 3) พ.ศ. 2554 (เงินกู้สามัญ)">
                    {{ str_limit('ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 3) พ.ศ. 2554 (เงินกู้สามัญ)', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/loan_reg200706') }}" data-tooltip="true" title="ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 5) พ.ศ. 2558 (เงินกู้เพื่อเหตุฉุกเฉิน)">
                    {{ str_limit('ระเบียบฯ ว่าด้วยการให้เงินกู้แก่สมาชิกและดอกเบี้ยเงินกู้ (ฉบับที่ 5) พ.ศ. 2558 (เงินกู้เพื่อเหตุฉุกเฉิน)', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/docs03') }}" data-tooltip="true" title="ระเบียบฯ ว่าด้วยการใช้ทุนสาธารณประโยชน์ พ.ศ. 2549">
                    {{ str_limit('ระเบียบฯ ว่าด้วยการใช้ทุนสาธารณประโยชน์ พ.ศ. 2549', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/docs04') }}" data-tooltip="true" title="ระเบียบฯ ว่าด้วยการใช้ทุนให้สวัสดิการแก่สามชิกและครอบครัว พ.ศ. 2549">
                    {{ str_limit('ระเบียบฯ ว่าด้วยการใช้ทุนให้สวัสดิการแก่สามชิกและครอบครัว พ.ศ. 2549', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/docs05') }}" data-tooltip="true" title="ระเบียบฯ ว่าด้วยพนักงานและลูกจ้าง พ.ศ. 2547">
                    {{ str_limit('ระเบียบฯ ว่าด้วยพนักงานและลูกจ้าง พ.ศ. 2547', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/docs06') }}" data-tooltip="true" title="ระเบียบฯ ว่าด้วยอัตราเงินเดือนพนักงานสหกรณ์ พ.ศ. 2547">
                    {{ str_limit('ระเบียบฯ ว่าด้วยอัตราเงินเดือนพนักงานสหกรณ์ พ.ศ. 2547', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/docs07') }}" data-tooltip="true" title="ระเบียบฯ ว่าด้วยการถือหุ้น พ.ศ. 2558">
                    {{ str_limit('ระเบียบฯ ว่าด้วยการถือหุ้น พ.ศ. 2558', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/rule') }}">ข้อบังคับ</a>
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
                <a href="{{ url('/announce/loan_tour1') }}" data-tooltip="true" title="แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการท่องเที่ยว">
                    {{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการท่องเที่ยว', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/living_loan_form') }}" data-tooltip="true" title="แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อดำรงชีพ">
                    {{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อดำรงชีพ', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/edu_loan_form') }}" data-tooltip="true" title="แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการศึกษา">
                    {{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจเพื่อการศึกษา', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/special_loan_form') }}" data-tooltip="true" title="แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจ">
                    {{ str_limit('แบบคำขอและหนังสือกู้เงินสามัญเฉพาะกิจ', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/floodhelp') }}">แบบแจ้งผู้ประสบอุทกภัย</a>
            </li>
            <li>
                <a href="{{ url('/announce/benef_form_new') }}">หนังสือตั้งผู้รับโอนประโยชน์</a>
            </li>
            <li>
                <a href="{{ url('/announce/agree2_form') }}" data-tooltip="true" title="หนังสือแสดงความยินยอมให้หักเงินเดือนคู่สมรส (กรณีติดตาม)">
                    {{ str_limit('หนังสือแสดงความยินยอมให้หักเงินเดือนคู่สมรส (กรณีติดตาม)', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/agree_form') }}" data-tooltip="true" title="หนังสือให้คำยินยอมหักเงินเดือนหรือค่าจ้างหรือบำเหน็จ">
                    {{ str_limit('หนังสือให้คำยินยอมหักเงินเดือนหรือค่าจ้างหรือบำเหน็จ', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/member_info') }}">ใบรับรองข้อมูลสมาชิก</a>
            </li>
            <li>
                <a href="{{ url('/announce/AppForm58') }}">ใบสมัครสมาชิกสหกรณ์ฯ</a>
            </li>
            <li>
                <a href="{{ url('/announce/inc_oct') }}">ใบขอเพิ่ม-ลดหุ้น</a>
            </li>
            <li>
                <a href="{{ url('/announce/resign') }}">ใบลาออก</a>
            </li>
            <li>
                <a href="{{ url('/announce/change1_form') }}" data-tooltip="true" title="หนังสือแจ้งการเปลี่ยนแปลงผู้ค้ำประกันเงินกู้สามัญ">
                    {{ str_limit('หนังสือแจ้งการเปลี่ยนแปลงผู้ค้ำประกันเงินกู้สามัญ', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/sample_common_form_new') }}" data-tooltip="true" title="ตัวอย่างการกรอกคำขอกู้เงินสามัญและเอกสารประกอบต่าง ๆ">
                    {{ str_limit('ตัวอย่างการกรอกคำขอกู้เงินสามัญและเอกสารประกอบต่าง ๆ', 30) }}
                </a>
            </li>
            <li>
                <a href="{{ url('/announce/ex_emer_reqst_form') }}" data-tooltip="true" title="ตัวอย่างการกรอกคำขอกู้เงินและหนังสือกู้เงินเพื่อเหตุฉุกเฉิน">
                    {{ str_limit('ตัวอย่างการกรอกคำขอกู้เงินและหนังสือกู้เงินเพื่อเหตุฉุกเฉิน', 30) }}
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="{{ url('/announce/status25580331') }}" class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> สถานะทางการเงิน
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/dep5601') }}" class="list-group-item">
            <i class="fa fa-file-text-o fa-fw"></i> สรุปยอดเงินฝาก/การซื้อสลาก
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/loan_rate1254') }}" class="list-group-item" data-tooltip="true" title="ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด">
            <i class="fa fa-file-text-o fa-fw"></i> {{ str_limit('ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด', 30) }}
        </a>
    </li>
    <li>
        <a href="{{ url('/announce/int200707') }}" class="list-group-item">
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
