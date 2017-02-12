<ol class="breadcrumb">
    <li><a href="/"><i class="fa fa-home"></i> หน้าหลัก</a></li>
    @foreach ($breadcrumb as $header)
        @if (empty($header['link']))
            <li class="active">{{ $header['item'] }}</li>
        @else
            <li><a href="{{ url($header['link']) }}">{{ $header['item'] }}</a></li>
        @endif
    @endforeach
</ol>