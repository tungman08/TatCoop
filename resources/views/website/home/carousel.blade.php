<div class="col-md-12">
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            @php ($index = 0)
            @forelse ($carousels as $carousel)
                <li data-target="#carousel-example-generic" data-slide-to="{{ $index }}"{{ ($index == 0) ? ' class="active"' : '' }}></li>

                @php ($index++)
            @empty
                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
            @endforelse
        </ol>
        <div class="carousel-inner">
            @php ($index = 0)

            @forelse ($carousels as $carousel)
                <div class="item{{ ($index == 0) ? ' active' : '' }}">
                    <a href="{{ url('/documents/' . str_plural(strtolower($carousel->document->document_type->name)) . '/' . $carousel->document->display) }}">
                        <img class="slide-image" src="{{ url('/storage/file/carousels/' . $carousel->image) }}" alt="">
                    </a>
                </div>

                @php ($index++)
            @empty
                <div class="item active">
                    <img class="slide-image" src="{{ asset('images/carousel.jpg') }}" alt="">
                </div>
            @endforelse
        </div>
        <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>
</div>
