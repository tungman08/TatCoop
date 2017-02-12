<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-line-chart fa-fw"></i> 
        สถิติการเข้าใช้งานรายเดือน
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="flot-chart">
            <div class="flot-chart-content" id="visitor-{{ $chart }}-flot-line-chart"></div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.panel -->

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> 
                ระบบปฏิบัติการที่ใช้
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="platform-{{ $chart }}-flot-bar-chart"></div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col -->

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> 
                เว็บเบราเซอร์ที่ใช้
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="browser-{{ $chart }}-flot-bar-chart"></div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-table fa-fw"></i>
        รายละเอียดการเข้าใช้งาน
        <div class="pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-gear fa-fw"></i>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li>
                        <a id="login-excel" href="#">
                            <i class="fa fa-file-excel-o fa-fw"></i>
                            Export to excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="table-responsive" style="padding-left: 15px; padding-right: 15px;">
            <table id="dataTables-{{ $chart }}" class="table table-hover dataTable" width="100%">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 23%;">{{ ($chart == 'website') ? 'เซสชั่น' : 'บัญชี' }}</th>
                        <th style="width: 23%;">เข้าชมเมื่อ</th>
                        <th style="width: 21%;">หมายเลขไอที</th>
                        <th style="width: 14%;">ระบบปฏิบัติการ</th>
                        <th style="width: 14%;">เว็บเบราเซอร์</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.panel -->