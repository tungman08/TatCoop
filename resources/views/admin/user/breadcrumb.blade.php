<ol class="breadcrumb">
    <li><a href="/"><i class="fa fa-home"></i> หน้าหลัก</a></li>
    <li class="active">ผู้ใช้งาน</li>
    @if (isset($breadcrumb))
        <li class="active">{{ $breadcrumb }}</li>
    @endif
</ol>