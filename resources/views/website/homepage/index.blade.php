@extends('website.homepage.layout')

@section('content')
    <div class="row carousel-holder">
        @include('website.homepage.carousel')
    </div>

    <div class="row">
        @include('website.homepage.news')
    </div>
@endsection
