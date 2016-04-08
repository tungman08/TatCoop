@extends('homepage.layout')

@section('content')
    <img class="logo" src="{{ asset('images/logo-coop.png') }}" alt="tatcoop logo" />
    <p class="lead">ข้อมูลข่าวสารสำหรับสมาชิก</p>
    <div class="list-group">
        <a href="{{ url('/pr/docs') }}" class="list-group-item">ระเบียบ/คำสั่ง/ข้อบังคับ</a>
        <a href="#" class="list-group-item">ใบสมัคร/แบบฟอร์มต่าง ๆ</a>
        <a href="#" class="list-group-item">สรุปยอดเงินฝาก/การซื้อสลาก</a>
        <a href="#" class="list-group-item">สถานะทางการเงิน</a>
        <a href="#" class="list-group-item">ตารางอัตราการหักคืนเงินกู้พร้อมดอกเบี้ยรายงวด</a>
        <a href="#" class="list-group-item">ประกาศอัตราดอกเบี้ยใหม่</a>
        <a href="#" class="list-group-item">คณะกรรมการดำเนินการสหกรณ์ฯ</a>
        <a href="#" class="list-group-item">เจ้าหน้าที่ประจำสหกรณ์ฯ</a>
    </div>
@endsection
