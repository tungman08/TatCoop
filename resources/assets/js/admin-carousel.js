$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $("#carousel").sortable({ 
        placeholder: 'ui-state-highlight',
        items: "li:not(.unsortable)",
        update: function(event, ui) {
            var id = ui.item.attr('id').replace('item-', '');
            var index = ui.item.index();

            reorder(id, index);
        }
    });  

    $('#add').click(function() {
        createForm();
    });
});

function createForm() {
    $.ajax({
        dataType: 'json',
        url: '/ajax/documentlists',
        type: 'post',
        cache: false,
        error: function(xhr, ajaxOption, thrownError) {
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        success: function(obj) {
            var item = '<li class="carousel-item unsortable" style="cursor: default;">';
                item += '<div class="form-group" style="position: relative; border: 1px solid lightgray; padding: 10px;">';
                item += '<div class="row">';
                item += '<div class="col-md-2 col-lg-2">';
                item += '<div id="carousel-new" class="carousel-link" onclick="$(\'#image-new\').click();">';
                item += '<div class="carousel-hover">';
                item += '<div class="carousel-hover-content">';
                item += '<i class="fa fa-image fa-2x"></i>';
                item += '</div>';
                item += '</div>';
                item += '<img id="preview-new" src="/images/carousel.png" class="img-responsive" alt="" />';
                item += '</div>';
                item += '</div>';
                item += '<div class="col-md-10">';
                item += '<label>';
                item += '<i class="fa fa-link fa-fw"></i>';
                item += 'เอกสาร/แบบฟอร์ม';
                item += '</label>';
                item += '<select id="document-type-new" name="document-type-new" class="form-control" onchange="javascript:typeChange($(this).val(), $(\'#document-new\'));">';

                $.each(obj.document_types, function(i, document_type) {
                    item += '<option value="' + document_type.id + '">' + document_type.display + '</option>';
                });

                item += '</select>';
                item += '<select id="document-new" name="document-new" class="form-control margin-t-xs margin-b-xs">';

                $.each(obj.documents, function(i, document) {
                    item += '<option value="' + document.id + '">' + document.display + '</option>';
                });

                item += '</select>';
                item += '<br />';
                item += '<button id="save" type="button" class="btn btn-primary btn-flat" onclick="javascript:uploadImage(document.getElementById(\'image-new\'), $(\'#document-new\').val(), this);">';
                item += '<i class="fa fa-save fa-fw"></i> บันทึก';
                item += '</button>';
                item += '<button id="cancel" type="button" class="btn btn-danger btn-flat margin-r-xs margin-l-sm" onclick="javascript:cancel(this);">';
                item += '<i class="fa fa-ban fa-fw"></i> ยกเลิก';
                item += '</button>';
                item += '<span id="result-new"></span>';
                item += '<input type="file" id="image-new" name="image-new" class="file-upload" accept="image/jpeg" ';
                item += 'onchange="javascript:selectImage(this, $(\'#preview-new\'))">';                
                item += '</div>';
                item += '</div>';
                item += '</div>';
                item += '</li>';

            $('#carousel').append(item);
            $('#add').hide();       
        }
    });
}

function cancel(element) {
    element.closest('ul li:last-child').remove();
    $('#add').show();
}

function typeChange(id, target) {
    $.ajax({
        dataType: 'json',
        url: '/ajax/documentsbytype',
        type: 'post',
        cache: false,
        data: {
            'id': id
        },
        error: function(xhr, ajaxOption, thrownError) {
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        success: function(opts) {
            target.empty();
            
            $.each(opts.documents, function(i, d) {
                // You will need to alter the below to get the right values from your json object.  Guessing that d.id / d.modelName are columns in your carModels data
                target.append('<option value="' + d.id + '">' + d.display + '</option>');
            });
        }
    });
}

function selectImage(file, target) {
    if (file.files && file.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var img = new Image();

            img.onload = function () {
                var width = img.width;
                var height = img.height;
                var MAX_WIDTH = 256;
                var MAX_HEIGHT = 144;

                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }

                var canvas = document.createElement("canvas");
                canvas.width = width;
                canvas.height = height;
                canvas.getContext("2d").drawImage(this, 0, 0, width, height);
                this.src = canvas.toDataURL();

                //remove this if you don't want to show it
                //document.body.appendChild(this);
            }

            img.src = e.target.result;
            target.attr('src', img.src);
        }

        reader.readAsDataURL(file.files[0]);
    }
}

function uploadImage(file, document_id, element) {
    if (file.files && file.files[0]) {
        var reader = new FileReader();
        var image = file.files[0];

        reader.onload = function (e) {
            var img = new Image();

            img.onload = function () {
                var width = img.width;
                var height = img.height;

                if (width == 768 && height == 432) {
                    if(image.size < 20971520) {
                        var formData = new FormData();
                            formData.append('image', image);
                            formData.append('document_id', document_id);

                        $.ajax({
                            dataType: 'json',
                            url: '/ajax/uploadcarousel',
                            type: 'post',
                            cache: false,
                            data: formData,
                            processData: false,
                            contentType: false,
                            error: function(xhr, ajaxOption, thrownError) {
                                $('#result-new').html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> เกิดข้อผิดพลาดในการอัฟโหลด</span>');
                                $('#result-new').show().delay(3000).fadeOut();
                                $('#cancel').show(); 
                                console.log(xhr.responseText);
                                console.log(thrownError);
                            },
                            beforeSend: function() {
                                $('#result-new').html('<i class="fa fa-spinner fa-pulse"></i> Uploading... (<span id="progress-new">0</span>%)');
                                $('#result-new').show();
                                $('#document-type-new').hide();
                                $('#document-new').hide();
                                $('#carousel-new').attr('onclick', '');
                                $('#carousel-new').removeClass('carousel-link');
                                $('.carousel-hover').html('');
                                $('#save').hide();
                                $('#cancel').hide();                            
                            },
                            xhr: function(){
                                // get the native XmlHttpRequest object
                                var xhr = $.ajaxSettings.xhr() ;
                                // set the onprogress event handler
                                xhr.upload.onprogress = function(evt){ $('#progress-new').html(Math.ceil((evt.loaded / evt.total) * 100)); };
                                // set the onload event handler
                                // xhr.upload.onload = function(){ $('#result-new').html('<i class="fa fa-clock-o fa-fw"></i> Please wait...'); };
                                // return the customized object
                                return xhr;
                            },
                            success: function(obj) {
                                $('#add').show();
                                element.closest('ul li:last-child').remove();

                                // write item.
                                writeItem(obj);
                            }
                        });
                    }
                    else {
                        $('#result-new').html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> รูปที่ใช้ต้องมีขนาดไม่เกิน 20M</span>');
                        $('#result-new').show().delay(3000).fadeOut();
                    }
                }
                else {
                    $('#result-new').html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> รูปที่ใช้ต้องมีขนาด 768x432 พิกเซลเท่านั้น</span>');
                    $('#result-new').show().delay(3000).fadeOut();
                }
            }

            img.src = e.target.result;
        }

        reader.readAsDataURL(file.files[0]);
    }  
    else {
        $('#result-new').html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> กรุณาเลือกรูปที่ต้องการขนาด 768x432 พิกเซล</span>');
        $('#result-new').show().delay(3000).fadeOut();
    }          
}

function writeItem(obj) {
    var item = '<li id="item-' + obj.id + '" class="carousel-item">';
        item += '<div class="form-group" style="position: relative; border: 1px solid lightgray; padding: 10px;">';
        item += '<div class="row">';
        item += '<div class="col-md-2 col-lg-2">';
        item += '<div class="carousel-link" onclick="$(\'#image-' + obj.id + '\').click();">';
        item += '<div class="carousel-hover">';
        item += '<div class="carousel-hover-content">';
        item += '<i class="fa fa-image fa-2x"></i>';
        item += '</div>';
        item += '</div>';
        item += '<img id="preview-' + obj.id + '" src="' + obj.thumbnail + '" class="img-responsive" alt="" />';
        item += '</div>';
        item += '</div>';
        item += '<div class="col-md-10">';
        item += '<label>';
        item += '<i class="fa fa-link fa-fw"></i>';
        item += 'เอกสาร/แบบฟอร์ม';
        item += '</label>';
        item += '<select id="document-type-' + obj.id + '" name="document-type-' + obj.id + '" ';
        item += 'class="form-control" ';
        item += 'onchange="javascript:typeChange($(this).val(), $(\'#document-' + obj.id + '\'));">';

        $.each(obj.document_types, function(i, document_type) {
            item += (obj.document_type_id == document_type.id) ? '<option value="' + document_type.id + '" selected>' + document_type.display + '</option>' : '<option value="' + document_type.id + '">' + document_type.display + '</option>';
        });

        item += '</select>';
        item += '<select id="document-' + obj.id + '" name="document-' + obj.id + '" ';
        item += 'class="form-control margin-t-xs margin-b-xs" ';
        item += 'onchange="javascript:updateDocument(' + obj.id + ', $(this).val());">';

        $.each(obj.documents, function(i, document) {
            item += (obj.document_id == document.id) ? '<option value="' + document.id + '" selected>' + document.display + '</option>' : '<option value="' + document.id + '">' + document.display + '</option>';
        });

        item += '</select>';
        item += '<span id="result-' + obj.id + '"></span>';
        item += '<button id="delete" type="button" class="btn btn-danger btn-flat pull-right" ';
        item += 'onclick="javascript:var result = confirm(\'Do you want to delete this carousel?\'); if (result) { deleteCarousel(' + obj.id + '); }">';
        item += '<i class="fa fa-trash fa-fw"></i> ลบ';
        item += '</button>';
        item += '<input type="file" id="image-' + obj.id + '" name="image-' + obj.id + '" ';
        item += 'class="file-upload" accept="image/jpeg" onchange="javascript:updateImage(' + obj.id + ', this);" />';
        item += '</div>';
        item += '</div>';
        item += '</div>';
        item += '</li>';

    $('#carousel').append(item);
}

function updateDocument(id, document_id) {
    var formData = new FormData();
        formData.append('id', id);
        formData.append('document_id', document_id);

    $.ajax({
        dataType: 'json',
        url: '/ajax/updatecarouseldocument',
        type: 'post',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        error: function(xhr, ajaxOption, thrownError) {
            $('#result-' + id).html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> เกิดข้อผิดพลาด</span>');
            $('#result-' + id).show().delay(3000).fadeOut();
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        success: function(obj) {
            $('#result-' + obj.id).html('<span class="text-success"><i class="fa fa-check-circle fa-fw"></i> บันทึกข้อมูลเรียบร้อย</span>');
            $('#result-' + id).show().delay(3000).fadeOut();
        }
    });            
}

function updateImage(id, file) {
    if (file.files && file.files[0]) {
        var reader = new FileReader();
        var image = file.files[0];

        reader.onload = function (e) {
            var img = new Image();

            img.onload = function () {
                var width = img.width;
                var height = img.height;

                if (width == 768 && height == 432) {
                    if(image.size < 20971520) {
                        var formData = new FormData();
                            formData.append('id', id);
                            formData.append('image', image);

                        $.ajax({
                            dataType: 'json',
                            url: '/ajax/updatecarouselimage',
                            type: 'post',
                            cache: false,
                            data: formData,
                            processData: false,
                            contentType: false,
                            error: function(xhr, ajaxOption, thrownError) {
                                $('#result-' + id).html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> เกิดข้อผิดพลาดในการอัฟโหลด</span>');
                                $('#result-' + id).show().delay(3000).fadeOut();
                                console.log(xhr.responseText);
                                console.log(thrownError);
                            },
                            beforeSend: function() {
                                $('#result-' + id).html('<i class="fa fa-spinner fa-pulse"></i> Uploading... (<span id="progress-new">0</span>%)');
                                $('#result-' + id).show();
                            },
                            xhr: function(){
                                // get the native XmlHttpRequest object
                                var xhr = $.ajaxSettings.xhr() ;
                                // set the onprogress event handler
                                xhr.upload.onprogress = function(evt){ $('#progress-' + id).html(Math.ceil((evt.loaded / evt.total) * 100)); };
                                // set the onload event handler
                                // xhr.upload.onload = function(){ $('#result-new').html('<i class="fa fa-clock-o fa-fw"></i> Please wait...'); };
                                // return the customized object
                                return xhr;
                            },
                            success: function(obj) {
                                $('#preview-' + obj.id).attr('src', obj.thumbnail);
                                $('#result-' + obj.id).html('<span class="text-success"><i class="fa fa-check-circle fa-fw"></i> บันทึกข้อมูลเรียบร้อย</span>');
                                $('#result-' + id).show().delay(3000).fadeOut();
                            }
                        });
                    }
                    else {
                        $('#result-' + id).html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> รูปที่ใช้ต้องมีขนาดไม่เกิน 20M</span>');
                        $('#result-' + id).show().delay(3000).fadeOut();
                    }
                }
                else {
                    $('#result-' + id).html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> รูปที่ใช้ต้องมีขนาด 768x432 พิกเซลเท่านั้น</span>');
                    $('#result-' + id).show().delay(3000).fadeOut();
                }
            }

            img.src = e.target.result;
        }

        reader.readAsDataURL(file.files[0]);
    }  
    else {
        $('#result-' + id).html('<span class="text-danger"><i class="fa fa-times-circle fa-fw"></i> กรุณาเลือกรูปที่ต้องการขนาด 768x432 พิกเซล</span>');
    }          
}

function deleteCarousel(id) {
    $.ajax({
        dataType: 'json',
        url: '/ajax/deletecarousel',
        type: 'post',
        cache: false,
        data: {
            'id': id
        },
        error: function(xhr, ajaxOption, thrownError) {
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        success: function(id) {
            $('#item-' + id).remove();
        }
    });            
}

function reorder(id, index) {
    $.ajax({
        dataType: 'json',
        url: '/ajax/reordercarousel',
        type: 'post',
        cache: false,
        data: {
            'id': id,
            'index': index
        },
        error: function(xhr, ajaxOption, thrownError) {
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        success: function(msg) {
            console.log(msg);
        }
    });
}