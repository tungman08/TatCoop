@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสาระน่ารู้
        <small>การจัดการสาระน่ารู้ในหน้าเว็บไซต์ สอ.สรทท.</small>
    </h1>

    @include('admin.knowledge.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการสาระน่ารู้', 'link' => '/website/knowledge'],
        ['item' => 'แสดงรายละเอียด', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการสาระน่ารู้</h4>
            <p>แสดงรายละเอียดของสาระน่ารู้</p>
        </div>

        @if(Session::has('flash_message'))
            <div class="callout {{ Session::get('callout_class') }}">
                <h4>แจ้งข้อความ!</h4>
                <p>
                    {{ Session::get('flash_message') }}

                    @if(Session::has('flash_link'))
                        <a href="{{ Session::get('flash_link') }}">Undo</a>
                    @endif
                </p>
            </div>
        @endif

        <!-- Box content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-commenting"></i> สาระน่ารู้เกี่ยวกับสหกรณ์</h3>
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default btn-flat btn-xs"
                        onclick="javascript:window.location.href='{{ url('/website/knowledge/' . $knowledge->id . '/edit') }}';">
                        แก้ไข
                    </button>
                    <button type="button" class="btn btn-default btn-flat dropdown-toggle btn-xs" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a style="cursor: pointer;" onclick="javascript:window.location.href='{{ url('/website/knowledge/' . $knowledge->id . '/edit') }}';"><i class="fa fa-edit"></i> แก้ไข</a></li>
                        <li><a style="cursor: pointer;" onclick="javascript:result = confirm('คุณต้องการลบรายการนี้ใช่ไหม ?'); if (result) { $('#delete_item').click(); }"><i class="fa fa-trash"></i> ลบ</a></li>
                    </ul>
                </div>
            </div>
            <!-- /.box-header -->

            {{ Form::open(['url' => '/website/knowledge/' . $knowledge->id]) }}
                {{ Form::hidden('_method', 'delete') }}
                {{ Form::submit('Delete', ['id' => 'delete_item', 'style' => 'display: none;']) }}
            {{ Form::close() }}    

            <div class="box-body">
                <h3 class="text-center text-primary">{{ $knowledge->title }}</h3>
                <br />
                <div class="margin-l-sm margin-r-sm">
                    {!! $knowledge->content !!}             
                </div>
            </div>
            <!-- /.box-body -->          
        </div>
        <!-- /.box -->

        <!-- row -->
        <div class="row">
            <!-- col -->
            <div class="col col-sm-6 col-md-6 col-lg-6">
                <!-- Box content -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-photo fa-fw"></i> รูปประกอบ</h3>
                        <span id="result-photo" class="margin-l-md"></span>
                        <div class="btn-group pull-right">
                            <button type="button" id="button-photo" class="btn btn-default btn-flat btn-xs"
                                onclick="javascript:$('#uploadimage').click();">
                                <i class="fa fa-plus-circle fa-fw"></i> เพิ่มรูปประกอบ
                            </button>
                            <input type="file" id="uploadimage" name="image-uploadimage" class="file-upload" accept="image/jpeg"
                                onchange="javascript:uploadImage($(this), 'knowledge', {{ $knowledge->id }});" />
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div id="preview-photo" class="row" style="padding-bottom: 10px;">
                            @foreach ($knowledge->attachments()->where('attach_type', 'photo')->get() as $item)
                                <div id="photo-{{ $item->id }}" class="col-lg-4 col-md-6 padding-sm">
                                    <div class="thumbnail margin-b-sm text-center">
                                        <img src="{{ 'data:image/jpeg;base64,' . base64_encode(Storage::disk('attachments')->get($item->file)) }}" class="img-responsive" style="max-height: 130px;" alt="" />
                                        <hr class="margin-xs" />
                                        <button type="button" class="btn btn-danger btn-flat btn-xs"
                                            onclick="javascript:var result = confirm('คุณต้องการลบรูปนี้ใช่หรือไม่ ?'); if (result) { deleteImage('knowledge', {{ $item->id }}); }">
                                            <i class="fa fa-close"></i> ลบ
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.box-body -->          
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <!-- col -->
            <div class="col col-sm-6 col-md-6 col-lg-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-paperclip fa-fw"></i> เอกสารแนบ</h3>
                        <span id="result-document" class="margin-l-md"></span>
                        <div class="btn-group pull-right">
                            <button type="button" id="button-document" class="btn btn-default btn-flat btn-xs"
                                    onclick="javascript:$('#uploadfile').click();">
                                    <i class="fa fa-plus-circle fa-fw"></i> เพิ่มเอกสารแนบ
                            </button>
                            <input type="file" id="uploadfile" name="image-uploadfile" class="file-upload" accept="application/pdf"
                                onchange="javascript:uploadDocument($(this), 'knowledge', {{ $knowledge->id }});" />
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <ul id="preview-document" class="listsClass">
                            @foreach ($knowledge->attachments()->where('attach_type', 'document')->get() as $item)
                                <li id="document-{{ $item->id }}" class="item list-item" style="cursor: default;">
                                    <div class="input-group">
                                        <div id="caption-{{ $item->id }}" class="file-caption-name">
                                            <i class="fa fa-file-pdf-o fa-fw"></i> {{ $item->display }}
                                        </div>
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-danger btn-flat btn-xs"
                                                onclick="javascript:var result = confirm('คุณต้องการลบเอกสารนี้ใช่หรือไม่ ?'); if (result) { deleteDocument('knowledge', {{ $item->id }}); }">
                                            <i class="fa fa-close"></i> ลบ</button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- /.box-body -->          
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
        });   

        var attach_type = 'knowledge';
    </script>

    {{ Html::script(elixir('js/admin-attachment.js')) }}
@endsection