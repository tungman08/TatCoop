<ol class="breadcrumb">   
    @if(count($breadcrumb) > 0)
        <li><a href="/"><i class="fa fa-home"></i> หน้าหลัก</a></li>   
  
        @foreach ($breadcrumb as $header)
            @if (empty($header['link']))
                <li class="active">{{ $header['item'] }}</li>
            @else
                <li><a href="{{ url($header['link']) }}">{{ $header['item'] }}</a></li>
            @endif
        @endforeach
    @else
        <li class="active"><i class="fa fa-home"></i> หน้าหลัก</li>
    @endif
</ol>