<!-- User Account: style can be found in dropdown.less -->
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <img src="{{ asset('images/user.png') }}" class="user-image" alt="User Image">
        <span class="hidden-xs">{{ $admin->name }}</span>
    </a>

    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <img src="{{  asset('images/user.png') }}" class="img-circle" alt="User Image">
            <p>
                {{ $admin->email }}<br />
                {{ $admin->name }}
                <small>ลงทะเบียนเมื่อ: {{ Diamond::parse($admin->create_at)->thai_format('Y-m-d') }}</small>
            </p>
        </li>

        <!-- Menu Body -->
        <!-- <li class="user-body">
            <div class="row">
                <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                </div>
                <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                </div>
                <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                </div>
            </div> -->
            <!-- /.row -->
        <!-- </li> -->

        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                {{ Form::button('<i class="fa fa-user"></i> ข้อมูลผู้ใช้งาน', [
                    'class' => 'btn btn-default btn-flat',
                    'onclick' => 'javascript:window.location = "' . url('/user/profile') . '"']) }}
            </div>
            <div class="pull-right">
                {{ Form::button('<i class="fa fa-sign-out"></i> ออกจากระบบ', [
                    'class' => 'btn btn-default btn-flat',
                    'onclick' => 'javascript:var result = confirm(\'คุณต้องการออกจากระบบใช่ไหม ?\'); if (result) { window.location.href = "' . url('/auth/logout') . '" }']) }}
            </div>
        </li>
    </ul>
</li>