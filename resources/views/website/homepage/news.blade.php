@forelse ($news as $item)
    <div class="col-sm-4 col-lg-4 col-md-4">
        <div class="thumbnail">
            <img src="{{ ($item->attachments()->count() == 0) ? asset('images/320x150.png') : url('/attachment/' . $item->attachments()->first()->file) }}" alt="" style="max-height: 122px;">
            <div class="caption">
                <h4 class="pull-right">&nbsp;</h4>
                <h4><a href="{{ url('/news/' . $item->id) }}" data-tooltip="true" title="{{ $item->title }}">{{ $item->title }}</a>
                </h4>
                <p>{!! html_entity_decode($item->content) !!}</p>
            </div>
            <div class="ratings">
                <p class="pull-right">อ่าน: {{ number_format($item->viewer, 0, '.', ',') }}</p>
                <p>{{ Diamond::parse($item->created_at)->thai_format('j M Y') }}</p>
            </div>   
        </div>
    </div>
@empty
    <div class="col-sm-12 col-lg-12 col-md-12" style="padding: 100px 0px 0px 0px;">
        <h4><i class="fa fa-newspaper-o fa-fw"></i> ไม่มีข่าวสารสำหรับสมาชิก</h>
    </div>
@endforelse

@if($news->count() > 0)
    <div class="col-sm-12 col-lg-12 col-md-12">
        <h4>
            <a href="{{ url('/news') }}">&gt;&gt; ดูข่าวสารทั้งหมด</a>
        </h4>
    </div>
@endif  

