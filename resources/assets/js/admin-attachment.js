function uploadImage(input, type, id) {
    var photo = input.get(0).files[0];
    var button_p = $('#button-photo');
    var button_d = $('#button-document');
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
                button_d.addClass('disabled');

                var item = '<div id="photo-new" class="col-lg-4 col-md-6 padding-sm">';
                    item += '<div class="thumbnail margin-b-sm text-center" style="height: 150px; padding-top: 65px;">';
                    item += '<span id="progress">0%</span>';
                    item += '</div>';
                    item += '</div>';

                preview.append(item);
            },
            xhr: function(){
                // get the native XmlHttpRequest object
                var xhr = $.ajaxSettings.xhr() ;
                // set the onprogress event handler
                xhr.upload.onprogress = function(evt){ $('#progress').html(Math.ceil((evt.loaded / evt.total) * 100) + '%'); };
                // set the onload event handler
                // xhr.upload.onload = function(){ };
                // return the customized object
                return xhr;
            },
            success: function(obj) {
                result.html('<span class="text-success"><i class="fa fa-check-circle fa-fw"></i> เพิ่มรูปประกอบเรียบร้อย</span>');
                result.show().delay(3000).fadeOut();

                $('#photo-new').remove();
                button_p.removeClass('disabled');
                button_d.removeClass('disabled');

                var item = '<div id="photo-' + obj.id + '" class="col-lg-4 col-md-6 padding-sm">';
                    item += '<div class="thumbnail margin-b-sm text-center">';
                    item += '<img src="' + obj.file + '" class="img-responsive" style="max-height: 130px;" alt="" />';
                    item += '<hr class="margin-xs" />';
                    item += '<button type="button" class="btn btn-danger btn-flat btn-xs"';
                    item += 'onclick="javascript:var result = confirm(\'คุณต้องการลบรูปนี้ใช่หรือไม่ ?\'); if (result) { deleteImage(\'' + attach_type + '\', ' + obj.id + '); }">';
                    item += '<i class="fa fa-close"></i> ลบ';
                    item += '</button>';
                    item += '</div>';
                    item += '</div>';

                preview.append(item);
            }
        })
    }
    else {
        result.html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> รูปภาพต้องมีขนาดไม่เกิน 20 MB</span>');
        result.show().delay(3000).fadeOut();
    }
}

function uploadDocument(input, type, id) {
    var document = input.get(0).files[0];
    var button_p = $('#button-photo');
    var button_d = $('#button-document');
    var result = $('#result-document');
    var preview = $('#preview-document');

    var formData = new FormData();
        formData.append('document', document);
        formData.append('id', id)
        formData.append('type', type);

    if (document.size < 20971520) {
        $.ajax({
            dataType: 'json',
            url: '/ajax/uploaddocument',
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
                button_d.addClass('disabled');

                var item = '<li id="document-new" class="item list-item" style="cursor: default;">';
                    item += '<div class="input-group">';
                    item += '<div id="caption-new" class="file-caption-name">';
                    item += '<i class="fa fa-hourglass fa-fw"></i> 0%';
                    item += '</div>';
                    item += '</div>';
                    item += '</li>';

                preview.append(item);
            },
            xhr: function(){
                // get the native XmlHttpRequest object
                var xhr = $.ajaxSettings.xhr() ;
                // set the onprogress event handler
                xhr.upload.onprogress = function(evt){ $('#caption-new').html('<i class="fa fa-hourglass fa-fw"></i> ' + Math.ceil((evt.loaded / evt.total) * 100) + '%'); };
                // set the onload event handler
                // xhr.upload.onload = function(){ };
                // return the customized object
                return xhr;
            },
            success: function(obj) {
                result.html('<span class="text-success"><i class="fa fa-check-circle fa-fw"></i> เพิ่มรูปประกอบเรียบร้อย</span>');
                result.show().delay(3000).fadeOut();

                $('#document-new').remove();
                button_p.removeClass('disabled');
                button_d.removeClass('disabled');

                var item = '<li id="document-' + obj.id + '" class="item list-item" style="cursor: default;">';
                    item += '<div class="input-group">';
                    item += '<div id="caption-' + obj.id + '" class="file-caption-name">';
                    item += '<i class="fa fa-file-pdf-o fa-fw"></i> ' + obj.display;
                    item += '</div>';
                    item += '<div class="input-group-btn">';
                    item += '<button type="button" class="btn btn-danger btn-flat btn-xs" ';
                    item += 'onclick="javascript:var result = confirm(\'คุณต้องการลบเอกสารนี้ใช่หรือไม่ ?\'); if (result) { deleteDocument(\'' + attach_type + '\', ' + obj.id + '); }">';
                    item += '<i class="fa fa-close"></i> ลบ</button>';
                    item += '</div>';
                    item += '</div>';
                    item += '</li>';

                preview.append(item);
            }
        })
    }
    else {
        result.html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> เอกสารต้องมีขนาดไม่เกิน 20 MB</span>');
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
        success: function(id) {
            $('#photo-' + id).remove();
        }
    }); 
}

function deleteDocument(type, id) {
    $.ajax({
        dataType: 'json',
        url: '/ajax/deletedocument',
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
        success: function(id) {
            $('#document-' + id).remove();
        }
    }); 
}