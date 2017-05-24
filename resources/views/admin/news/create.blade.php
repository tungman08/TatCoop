@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการข่าวสารสำหรับสมาชิก
            <small>การจัดการข่าวสารสำหรับสมาชิกของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการข่าวสารสำหรับสมาชิก', 'link' => '/website/news'],
            ['item' => 'เพิ่ม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>เพิ่มข่าวสารสำหรับสมาชิก</h4>
            <p>ให้ผู้ดูแลระบบ เพิ่มข่าวสารสำหรับสมาชิก</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <!-- Box content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-newspaper-o"></i> ข่าวสารสำหรับสมาชิก</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/website/news', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.news.form', ['edit' => false])
            {{ Form::close() }}    
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
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function() {
            //bootstrap WYSIHTML5 - text editor
            $(".textarea").wysihtml5({
                toolbar: {
                    "font-styles": true, // Font styling, e.g. h1, h2, etc.
                    "emphasis": true, // Italics, bold, etc.
                    "lists": true, // (Un)ordered lists, e.g. Bullets, Numbers.
                    "html": true, // Button which allows you to edit the generated HTML.
                    "link": true, // Button to insert a link.
                    "image": false, // Button to insert an image.
                    "color": true, // Button to change color of font
                    "blockquote": false, // Blockquote
                    "size": "default" // options are xs, sm, lg
                }
            });
        });
    </script>  
@endsection