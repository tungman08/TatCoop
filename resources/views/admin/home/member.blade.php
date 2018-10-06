<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">สมาชิก ปี {{ Diamond::today()->thai_format('Y') }}</h3>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="row">
            <div class="col-md-8">
                <p class="text-center">
                    <strong>ความเคลื่อนไหวของจำนวนสมาชิกในแต่ละเดือน</strong>
                </p>
                <div class="chart flot-chart">
                    <div class="flot-chart-content" id="member-flot-line-chart"></div>
                </div>
                <!-- /.chart-responsive -->
            </div>
            <!-- /.col -->

            <div class="col-md-4">
                <p class="text-center">
                    <strong>สมาชิกล่าสุด</strong>
                </p>

                <ul id="newmembers" class="products-list product-list-in-box">
                </ul>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->