@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสาระน่ารู้
        <small>การจัดการสาระน่ารู้ในหน้าเว็บไซต์ สอ.สรทท.</small>
    </h1>

    @include('admin.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการสาระน่ารู้', 'link' => action('Admin\KnowledgeController@index')],
		['item' => 'สาระน่ารู้ที่ถูกลบ', 'link' => action('Admin\KnowledgeController@getInactive')],
        ['item' => 'แสดงรายละเอียด', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการสาระน่ารู้</h4>
            <p>ให้ผู้ดูแลระบบ ลบถาวรหรือคืนสภาพให้สาระน่ารู้ที่ถูกลบ</p>
        </div>

        <!-- Box content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-commenting"></i> สาระน่ารู้เกี่ยวกับสหกรณ์ที่ถูกลบ</h3>
				<div class="btn-group pull-right">
					<button class="btn btn-default btn-flat btn-xs" onclick="javascript:window.history.go(-1);">
						<i class="fa fa-reply"></i> ถอยกลับ
					</button>
				</div> 
            </div>
            <!-- /.box-header -->

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
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div id="preview-photo" class="row" style="padding-bottom: 10px;">
                            @foreach ($knowledge->attachments()->where('attach_type', 'photo')->get() as $item)
                                <div id="photo-{{ $item->id }}" class="col-lg-4 col-md-6 padding-sm">
                                    <div class="thumbnail margin-b-sm text-center">
                                        <img src="{{ FileManager::get('attachments', $item->file) }}" class="img-responsive" style="max-height: 130px;" alt="" />
                                        <hr class="margin-xs" />
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
@endsection