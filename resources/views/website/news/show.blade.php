@extends('website.news.layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">
                <ol class="breadcrumb">
                    <li><a href="{{ url('/') }}"><i class="fa fa-home fa-fw"></i></a></li>
                    <li><a href="{{ url('/news') }}">ข่าวสารสำหรับสมาชิก</a></li>
                    <li class="active">เนื้อหาข่าวสาร</li>
                </ol>
            </h3>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-newspaper-o fa-fw"></i> ข่าวสารสำหรับสมาชิก
            <div class="pull-right">
                วันที่ {{ Diamond::parse($news->created_at)->thai_format('j F Y') }}
            </div>

        </div>
        <!-- /.panel-heading -->

        <div class="panel-body">
            <h3 class="text-center text-primary">{{ $news->title }}</h3>
            <br />
            <div class="margin-l-sm margin-r-sm">
                {!! html_entity_decode($news->content) !!}
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->

    @if($news->attachments()->where('attach_type', 'photo')->get()->count() > 0) 
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-photo fa-fw"></i> รูปประกอบ
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="container parent-container thumbnail-box">
                    <div class="row" style="margin-right: 25px;">
                        @foreach ($news->attachments()->where('attach_type', 'photo')->get() as $item)
                            <div class="col-sm-3 col-md-3 col-ld-3">
                                <div class="thumbnail" style="border-radius: 0px;">
                                    <a href="{{ url('/attachment/' . $item->file) }}">
                                        <img src="{{ url('/attachment/' . $item->file) }}" style="max-height: 200px; max-width: 100%;" alt="" />
                                    </a>
                                </div>    
                            </div>
                        @endforeach
                    </div>    
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    @endif

    @if($news->attachments()->where('attach_type', 'document')->get()->count() > 0)        
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-paperclip fa-fw"></i> เอกสารแนบ
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <ul class="list-unstyled">
                    @foreach ($news->attachments()->where('attach_type', 'document')->get() as $item)
                        <li class="padding-xs"><i class="fa fa-file-pdf-o fa-fw"></i> <a href="{{ url('/attachment/' . $item->file) }}">{{ $item->display }}</a></li>
                    @endforeach
                </ul>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    @endif
@endsection