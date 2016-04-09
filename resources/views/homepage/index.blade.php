@extends('homepage.layout')

@section('content')
    <div class="row carousel-holder">
        @include('homepage.carousel')
    </div>

    <div class="row">
        @include('homepage.news')
    </div>
@endsection
