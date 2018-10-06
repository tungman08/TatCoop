<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">จำนวนสมาชิกปัจจุบัน</span>
            <span class="info-box-number">{{ number_format($info->members, 0, '.', ',') }} คน</span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-baht"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">ยอดหุ้นรวม ณ เดือน{{ Diamond::parse(App\ShareHolding::max('pay_date'))->thai_format('F Y') }}</span>
            <span class="info-box-number">{{ number_format($info->shareholdings, 2, '.', ',') }} บาท</span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->

<!-- fix for small devices only -->
<div class="clearfix visible-sm-block"></div>

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <span class="info-box-icon bg-yellow"><i class="fa fa-baht"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">เงินกู้ที่ยังไม่ได้ชำระรวม ณ เดือน{{ Diamond::parse(App\ShareHolding::max('pay_date'))->thai_format('F Y') }}</span>
            <span class="info-box-number">{{ number_format($info->loans, 2, '.', ',') }} บาท</span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <span class="info-box-icon bg-red"><i class="fa fa-baht"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">ผลประกอบการปี {{ Diamond::today()->thai_format('Y') }} ณ เดือน{{ Diamond::parse(App\ShareHolding::max('pay_date'))->thai_format('F Y') }}</span>
            <span class="info-box-number">#,### บาท</span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->