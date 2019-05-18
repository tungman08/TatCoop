<!-- User Account: style can be found in dropdown.less -->
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <img src="{{ asset('images/user.png') }}" class="user-image" alt="User Image">
        <span class="hidden-xs">{{ $admin->fullname }}</span>
    </a>

    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <img src="{{  asset('images/user.png') }}" class="img-circle" alt="User Image">
            <p>
                {{ $admin->email }}<br />
                {{ $admin->fullname }}
                <small>ลงทะเบียนเมื่อ: {{ Diamond::parse($admin->created_at)->thai_format('j M Y') }}</small>
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
                    'onclick' => 'javascript:document.location.href = "' . action('Admin\UserController@getProfile') . '"']) }}
            </div>
            <div class="pull-right">
                {{ Form::button('<i class="fa fa-sign-out"></i> ออกจากระบบ', [
                    'class' => 'btn btn-default btn-flat',
                    'onclick' => 'javascript:var result = confirm(\'คุณต้องการออกจากระบบใช่ไหม ?\'); if (result) { document.location.href = "' . action('Admin\AuthController@getLogout') . ';" }']) }}
            </div>
        </li>
    </ul>
</li>