<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            @include('admin.layouts.message')
            @include('admin.layouts.notice')
            @include('admin.layouts.alert')
            @include('admin.layouts.user')
        </ul>
    </div>
</nav>