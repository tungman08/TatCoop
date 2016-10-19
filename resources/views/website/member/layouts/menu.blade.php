<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
        <li class="header">เมนูหลัก</li>
        {!! MainMenu::member(Request::url()) !!}

        <!-- link -->
        <li class="header">บริการของ สอ.สรทท.</li>
        <li>
            <a href="{{ url('/') }}" target="_blank">
                <i class="fa fa-circle-o text-yellow"></i> 
                <span>เว็บไซต์ สอ.สรทท.</span>
            </a>
        </li>
    </ul>
</section>
<!-- /.sidebar -->