<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">เงินกู้ ปี {{ Diamond::today()->thai_format('Y') }}</h3>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="row">
            <div class="col-md-8">
                <p class="text-center">
                    <strong>การกู้ยืมในแต่ละเดือน</strong>
                </p>
                <div class="chart flot-chart">
                    <div class="flot-chart-content" id="loan-flot-line-chart"></div>
                </div>
                <!-- /.chart-responsive -->
            </div>
            <!-- /.col -->

            <div class="col-md-4">
                <p class="text-center">
                    <strong>สมาชิกที่มียอดกู้เงินสูงสุด</strong>
                </p>

                <ul id="toploans" class="products-list product-list-in-box">
                </ul>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->