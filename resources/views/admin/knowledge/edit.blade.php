@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสาระน่ารู้
        <small>การจัดการสาระน่ารู้ในหน้าเว็บไซต์ สอ.สรทท.</small>
    </h1>

    @include('admin.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการสาระน่ารู้', 'link' => '/website/knowledge'],
        ['item' => 'แสดงรายละเอียด', 'link' => '/website/knowledge/' . $knowledge->id],
        ['item' => 'แก้ไข', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>แก้ไขสาระน่ารู้</h4>
            <p>ให้ผู้ดูแลระบบ แก้ไข สาระน่ารู้</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Box content -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-commenting"></i> สาระน่ารู้</h3>
                    </div>
                    <!-- /.box-header -->

                    <!-- form start -->
                    {{ Form::model($knowledge, ['route' => ['website.knowledge.update', $knowledge->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                        @include('admin.knowledge.form', ['edit' => true])
                    {{ Form::close() }}          
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4">
                <!-- Box content -->
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-photo"></i> รูปประกอบ</h3>
                        <span id="result-photo" class="margin-l-md"></span>
                    </div>
                    <!-- /.box-header -->

                    @include('admin.knowledge.image', ['images' => $knowledge->attachments()->where('attach_type', 'photo')->get() ])
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
        $(document).ready(function() {
            //bootstrap WYSIHTML5 - text editor
            $(".textarea").wysihtml5({
                toolbar: {
                    "font-styles": true, // Font styling, e.g. h1, h2, etc.
                    "emphasis": true, // Italics, bold, etc.
                    "lists": true, // (Un)ordered lists, e.g. Bullets, Numbers.
                    "html": true, // Button which allows you to edit the generated HTML.
                    "link": true, // Button to insert a link.
                    "image": true, // Button to insert an image.
                    "color": true, // Button to change color of font
                    "blockquote": false, // Blockquote
                    "size": "default" // options are xs, sm, lg
                }
            });
        });

        var attach_type = 'knowledge';

        function uploadImage(input, type, id) {
            var photo = input.get(0).files[0];
            var button_p = $('#button-photo');
            var result = $('#result-photo');
            var preview = $('#preview-photo');

            var formData = new FormData();
                formData.append('photo', photo);
                formData.append('id', id);
                formData.append('type', type);

            if (photo.size < 20971520) {
                $.ajax({
                    dataType: 'json',
                    url: '/ajax/uploadphoto',
                    type: 'post',
                    cache: false,
                    data: formData,
                    processData: false,
                    contentType: false,
                    error: function(xhr, ajaxOption, thrownError) {
                        result.html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> เกิดข้อผิดพลาดในการอัฟโหลด</span>');
                        result.show().delay(3000).fadeOut();
                        button_p.removeClass('disabled');
                        button_d.removeClass('disabled');

                        console.log(xhr.responseText);
                        console.log(thrownError);
                    },
                    beforeSend: function() {
                        button_p.addClass('disabled');

                        var item = '<li id="photo-new" style="border-bottom: 1px solid #f4f4f4; padding: 5px 0;">';
                            item += '<div class="thumbnail margin-b-sm text-center" style="height: 70px; padding-top: 28px;">';
                            item += '<span id="progress">Uploading...</span>';
                            item += '</div>';
                            item += '</li>';

                        preview.append(item);
                    },
                    success: function(obj) {
                        result.html('<span class="text-success"><i class="fa fa-check-circle fa-fw"></i> เพิ่มรูปประกอบเรียบร้อย</span>');
                        result.show().delay(3000).fadeOut();

                        $('#photo-new').remove();
                        button_p.removeClass('disabled');

                        var item = '<li id="photo-' + obj.id + '" style="border-bottom: 1px solid #f4f4f4; padding: 5px 0;">';
                            item += '<div class="row">';
                            item += '<div class="col-xs-4">';
                            item += '<img src="' + obj.file + '" class="img-responsive" style="max-height: 70px;" alt="" />';
                            item += '</div>';
                            item += '<div class="col-xs-8">';
                            item += '<input id="url-' + obj.id + '" type="text" class="form-control" value="' + obj.file + '" readonly />';
                            item += '<div class="btn-group" style="margin-top: 2px;">';
                            item += '<button class="btn btn-default btn-flat" onclick="copy($(\'#url-' + obj.id + '\'));"><i class="fa fa-copy"></i></button>';
                            item += '<button class="btn btn-default btn-flat" ';
                            item += 'onclick="javascript:var result = confirm(\'คุณต้องการลบรูปนี้ใช่หรือไม่ ?\'); if (result) { deleteImage(\'' + attach_type + '\', ' + obj.id + '); }">';
                            item += '<i class="fa fa-trash"></i></button>';
                            item += '</div>';
                            item += '</div>';
                            item += '</div>';
                            item += '</li>';

                        preview.append(item);
                    }
                })
            }
            else {
                result.html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> รูปภาพต้องมีขนาดไม่เกิน 20 MB</span>');
                result.show().delay(3000).fadeOut();
            }
        }

        function deleteImage(type, id) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/deletephoto',
                type: 'post',
                cache: false,
                data: {
                    'id': id,
                    'type': type
                },
                error: function(xhr, ajaxOption, thrownError) {
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                beforeSend: function() {
                    var item = '<div class="thumbnail margin-b-sm text-center" style="height: 70px; padding-top: 28px;">';
                        item += '<span id="progress">Deleting...</span>';
                        item += '</div>';

                    $('#photo-' + id).html(item);
                },
                success: function(id) {
                    $('#photo-' + id).remove();
                }
            }); 
        }

        function copy(textbox) {
            /* Select the text field */
            textbox.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");

            /* Alert the copied text */
            alert("คัดลอก URL รูปภาพเรียบร้อย");
        }
    </script>  
@endsection