@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการข่าวประชาสัมพันธ์
            <small>การจัดการข่าวประชาสัมพันธ์ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการข่าวประกาศ', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข่าวประชาสัมพันธ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ข่าวประชาสัมพันธ์ (Slide Picture)</p>
        </div>

        <!-- Box content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-rss"></i> ข่าวประชาสัมพันธ์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <ul class="listsClass" id="carousel">
                    @foreach($carousels as $carousel)
                        <li id="item-{{ $carousel->id }}" class="carousel-item">
                            <div class="form-group" style="position: relative; border: 1px solid lightgray; padding: 10px;">
                                <div class="row">
                                    <div class="col-md-2 col-lg-2">
                                        <div class="carousel-link" onclick="$('#image-{{ $carousel->id }}').click();">
                                            <div class="carousel-hover">
                                                <div class="carousel-hover-content">
                                                    <i class="fa fa-image fa-2x"></i>
                                                </div>
                                            </div>
                                            <img id="preview-{{ $carousel->id }}" 
                                                src="{{ url('/carousel/' . 'thumbnail_' . $carousel->image) }}" 
                                                class="img-responsive" alt="" />
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <label>
                                            <i class="fa fa-link fa-fw"></i>
                                            เอกสาร/แบบฟอร์ม
                                        </label>
                                        @php($document = App\Document::find($carousel->document_id))
                                        <select id="document-type-{{ $carousel->id }}" name="document-type-{{ $carousel->id }}" class="form-control"
                                            onchange="javascript:typeChange($(this).val(), $('#document-{{ $carousel->id }}'));">
                                            @foreach ($document_types as $type)
                                                <option value="{{ $type->id }}"{{ ($document->document_type_id == $type->id) ? ' selected' : '' }}>{{ $type->display }}</option>
                                            @endforeach
                                        </select>
                                        <select id="document-{{ $carousel->id }}" name="document-{{ $carousel->id }}" class="form-control margin-t-xs margin-b-xs"
                                            onchange="javascript:updateDocument({{ $carousel->id }}, $(this).val());">
                                            @foreach($document_types->find($document->document_type_id)->documents->sortBy('position') as $item)
                                                <option value="{{ $item->id }}"{{ ($carousel->document_id == $item->id) ? ' selected' : '' }}>{{ $item->display }}</option>
                                            @endforeach
                                        </select>
                                        <span id="result-{{ $carousel->id }}"></span>
                                        <button id="delete" type="button" class="btn btn-danger btn-flat pull-right"
                                            onclick="javascript:var result = confirm('Do you want to delete this carousel?'); if (result) { deleteCarousel({{ $carousel->id }}); }">
                                            <i class="fa fa-trash fa-fw"></i> ลบ
                                        </button>
                                        <input type="file" id="image-{{ $carousel->id }}" name="image-{{ $carousel->id }}" class="file-upload" accept="image/jpeg"
                                            onchange="javascript:updateImage({{ $carousel->id }}, this);" />
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="btn-group">
                    <button id="add" type="button" class="btn btn-primary btn-flat">
                        <i class="fa fa-plus-circle fa-fw"></i> เพิ่มข่าวประชาสัมพันธ์
                    </button>
                </div>            
            </div>
            <!-- /.box-body -->          
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent

    {{ Html::style(elixir('css/admin-carousel.css')) }}

    <style>
        .ui-state-highlight {
            height: 157px;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('scripts')
    @parent

    {{ Html::script(elixir('js/admin-carousel.js')) }}  
@endsection