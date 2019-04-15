<!-- The timeline -->
<ul class="timeline timeline-inverse">
    @foreach($histories as $history)
        <!-- timeline time label -->
        <li class="time-label">
            <span class="bg-red">{{ Diamond::parse($history->date)->thai_format('j M Y') }}</span>
        </li>
        <!-- /.timeline-label -->

        @foreach($history->items as $item)
            <!-- timeline item -->
            <li>
                <i class="fa {{ $item->history_type->icon }} {{ $item->history_type->color }}"></i>
                @if (is_null($item->description))
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> {{ (Diamond::today()->diff(Diamond::parse($item->created_at))->days > 1) ? Diamond::parse($item->created_at)->thai_format('j M Y') : Diamond::parse($item->created_at)->thai_diffForHumans() }}</span>

                        <h3 class="timeline-header no-border">{{ $item->history_type->name }}</h3>
                    </div>
                @else
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> {{ (Diamond::today()->diff(Diamond::parse($item->created_at))->days > 1) ? Diamond::parse($item->created_at)->thai_format('j M Y') : Diamond::parse($item->created_at)->thai_diffForHumans() }}</span>

                        <h3 class="timeline-header">{{ $item->history_type->name }}</h3>

                        <div class="timeline-body">
                            {{ $item->description }}
                        </div>
                    </div>
                @endif
            </li>
            <!-- END timeline item -->
        @endforeach   
    @endforeach

    <!-- END timeline item -->
    <li id="end">
        <i class="fa fa-clock-o bg-gray"></i>
        @if($index < $count - 1)
            <div class="timeline-item" style="border: none; background: none;">
                <button id="more" onclick="javascript:loadmore({{ $index + 1 }});" data-tooltip="true" title="Load more...">...</button>
            </div>
        @endif
    </li>
</ul>