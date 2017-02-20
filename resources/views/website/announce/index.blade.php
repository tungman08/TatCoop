@extends('website.announce.layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}"><i class="fa fa-home fa-fw"></i></a></li>
                <li class="active">เอกสาร</li>
            </ol>
        </h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <object data="{{ url('/docs/'. $key . '.pdf') }}" type="application/pdf" width="100%" height="1000"></object>
    </div>
</div>
@endsection
