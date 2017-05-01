<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <h2 class="section-heading">บริการอิเล็กทรอนิกส์</h2>
            <h3 class="section-subheading text-muted">บริการอิเล็กทรอนิกส์ต่างๆ ของ สอ.สรทท.</h3>
        </div>
        <div class="col-md-4 col-md-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4>
                        <i class="fa fa-fw fa-users"></i>สมาชิกสหกรณ์ออมทรัพย์
                    </h4>
                </div>
                <div class="panel-body">
                    <p>สามารถแก้ไขข้อมูลส่วนตัว และเรียกดูข้อมูลทุนเรือนหุ้น เงินปันผลประจำปี การกู้ยืม รวมถึงสถานะการค้ำประกัน</p>
                    <button class="btn btn-primary" onclick="javascript:window.location.href='{{ url('/member') }}'">เข้าใช้งาน</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4>
                        <i class="fa fa-fw fa-calculator"></i>คำนวณสินเชื่อเงินกู้เบื้องต้น
                    </h4>
                </div>
                <div class="panel-body">
                    <p>สำหรับใช้คำนวณการผ่อนชำระค่างวดเงินกู้เบื้องต้น สำหรับสมาชิกก่อนทำการกู้ยืม</p>
                    <button class="btn btn-success" onclick="javascript:window.location.href='{{ url('/loan') }}'">เข้าใช้งาน</button>
                </div>
            </div>
        </div>
        <!--<div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h4>
                        <i class="fa fa-fw fa-comments"></i>กระดานสนทนา
                    </h4>
                </div>
                <div class="panel-body">
                    <p>สำหรับแจ้งข่าวสารจากสหกรณ์ และเป็นช่องทางสื่อสารระหว่างสมาชิก</p>
                    <button class="btn btn-danger disabled">อยู่ในระหว่างพัฒนา</button>
                </div>
            </div>
        </div>-->
    </div>
</div>
