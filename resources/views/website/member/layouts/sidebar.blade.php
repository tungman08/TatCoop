<!-- Create the tabs -->
<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
    <li class="active">
        <a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gear"></i></a>
    </li>
    <li>
        <a><i class="fa"></i></a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    {!! Form::open(['url' => '/', 'role' => 'form']) !!}
        <!-- Setting tab content -->
        <div class="tab-pane active" id="control-sidebar-settings-tab">
            <h3 class="control-sidebar-heading">รูปแบบเว็บไซต์</h3>
            <ul class="list-unstyled clearfix">
                @foreach ($skins as $skin)
                    <li class="skin-menu-item">
                        <a class="clearfix full-opacity-hover" data-skin="{{ $skin->code }}" href="javascript:void(0);" onclick="change_skin($(this).attr('data-skin'));">
                            <div>
                                <span class="bg-{{ str_replace('-light', '', str_replace('skin-', '', $skin->code)) }}-active skin-menu-active"></span>
                                <span class="bg-{{ str_replace('-light', '', str_replace('skin-', '', $skin->code)) }} skin-menu-normal"></span>
                            </div>
                            <div>
                                <span class="skin-menu-side{{ str_contains($skin->code, '-light') ? '-light' : '' }}"></span>
                                <span class="skin-menu-body"></span>
                            </div>
                        </a>
                        <p class="text-center no-margin">{{ $skin->name }}</p>
                    </li>
                @endforeach
            </ul>
           <!-- /.setting-sidebar-menu -->

        </div>
    {!! Form::close() !!}

    <!-- Controls tab content -->
    <div class="tab-pane active" id="control-sidebar-controls-tab"></div>
</div>