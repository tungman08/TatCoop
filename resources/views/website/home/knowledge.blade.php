<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="section-heading">สาระน่ารู้เกี่ยวกับสหกรณ์</h2>
            <h3 class="section-subheading text-muted">สาระน่ารู้หรือข่าวสารที่เกี่ยวกับสหกรณ์ออมทรัพย์</h3>
        </div>
        
        @forelse ($knowledges as $item)
            <div class="col-sm-3 col-lg-3 col-md-3">
                <div class="thumbnail">
                    @php($files = $item->attachments()->where('attach_type', 'photo'))
                    <img src="{{ ($files->count() == 0) ? asset('images/320x150.png') : url('/storage/file/attachments/' . $files->first()->file) }}" alt="" style="max-height: 122px;">
                    <div class="caption">
                        <h4 class="pull-right">&nbsp;</h4>
                        <h4>
                            <a href="{{ action('Website\KnowledgeController@show', ['id'=>$item->id]) }}" data-tooltip="true" title="{{ $item->title }}">{{ $item->title }}</a>
                        </h4>
                        {{--<p>{!! html_entity_decode($item->content) !!}</p>--}}
                    </div>
                    <div class="ratings text-left">
                        <span><i class="fa fa-clock-o"></i> {{ (Diamond::now()->diff(Diamond::parse($item->created_at))->days > 2) ? Diamond::parse($item->created_at)->thai_format('j M Y') : Diamond::parse($item->created_at)->thai_diffForHumans() }}</span>
                        <span class="pull-right">อ่าน: {{ number_format($item->viewer, 0, '.', ',') }}</span>
                    </div>      
                </div>
            </div>
        @empty
            <div class="col-sm-12 col-lg-12 col-md-12" style="padding: 50px 0px;">
                <h4><i class="fa fa-commenting fa-fw"></i> ไม่มีสาระน่ารู้น่ารู้เกี่ยวกับสหกรณ์</h>
            </div>
        @endforelse

        @if($knowledges->count() > 0)
            <div class="col-sm-12 col-lg-12 col-md-12">
                <h4>
                    <a href="{{ action('Website\KnowledgeController@index') }}">&gt;&gt; ดูสาระน่ารู้ทั้งหมด</a>
                </h4>
            </div>      
        @endif       
    </div>
</div>
