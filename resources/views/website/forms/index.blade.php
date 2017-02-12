@extends('website.forms.layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}"><i class="fa fa-home fa-fw"></i></a></li>
                @if (is_null($file))
                    <li class="active">ใบสมัคร/แบบฟอร์มต่าง ๆ</li>
                @else
                    <li><a href="{{ url('/forms') }}">ใบสมัคร/แบบฟอร์มต่าง ๆ</a></li>
                    <li class="active">เอกสาร</li>
                @endif
            </ol>
        </h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <object data="" type="application/pdf" width="100%" height="1000"></object>
    </div>
</div>
@endsection