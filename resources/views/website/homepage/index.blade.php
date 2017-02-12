@extends('website.homepage.layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i> สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด</li>
                </ol>
            </h3>
        </div>
    </div>

    <div class="row carousel-holder">
        @include('website.homepage.carousel')
    </div>

    <div class="row">
        @include('website.homepage.news')
    </div>
@endsection
