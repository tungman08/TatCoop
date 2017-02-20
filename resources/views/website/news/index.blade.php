@extends('website.news.layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">
                <ol class="breadcrumb">
                    <li><a href="{{ url('/') }}"><i class="fa fa-home fa-fw"></i></a></li>
                    <li class="active">ข่าวสารสำหรับสมาชิก</li>            
                </ol>
            </h3>
        </div>
    </div>
    <section id="about" class="about-section" style="padding: 0px;">
        <div class="container thumbnail-box">
            <div class="row">
                @forelse ($newses as $item)
                    <div class="col-sm-3 col-lg-3 col-md-3">
                        <div class="thumbnail">
                            <img src="{{ ($item->attachments()->count() == 0) ? asset('images/320x150.png') : url('/attachment/' . $item->attachments()->where('attach_type', 'photo')->first()->file) }}" alt="" style="max-height: 122px;">
                            <div class="caption">
                                <h4 class="pull-right">&nbsp;</h4>
                                <h4>
                                    <a href="{{ url('/news/' . $item->id) }}">{{ $item->title }}</a>
                                </h4>
                                <p>{!! html_entity_decode($item->content) !!}</p>
                            </div>
                            <div class="ratings">
                                <p class="pull-right">อ่าน: {{ number_format($item->viewer, 0, '.', ',') }}</p>
                                <p><i class="fa fa-clock-o"></i> {{ (Diamond::now()->diff(Diamond::parse($item->created_at))->days > 1) ? Diamond::parse($item->created_at)->thai_format('j M Y') : Diamond::parse($item->created_at)->thai_diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-sm-12 col-lg-12 col-md-12" style="padding: 150px 0px;">
                        <h4><i class="fa fa-newspaper-o fa-fw"></i> ไม่มีข่าวสารสำหรับสมาชิก</h>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="pull-right">
            {{ $newses->links() }}
        </div>
    </section>
@endsection