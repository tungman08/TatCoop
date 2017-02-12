$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    display();

    $("#Rule").click(function() {
        addFile($(this), $("#ruleTree"));
    });

    $("#Form").click(function() {
        addFile($(this), $("#formTree"));
    });

    $("#ruleTree").sortable({ 
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            var id = ui.item.attr('id').replace('item-', '');
            var index = ui.item.index();

            reorder(id, index);
        }
    });  

    $("#formTree").sortable({ 
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            var id = ui.item.attr('id').replace('item-', '');
            var index = ui.item.index();

            reorder(id, index);
        }
    });            
});

function display() {
    $.ajax({
        dataType: 'json',
        url: '/ajax/documents',
        type: 'get',
        cache: false,
        error: function(xhr, ajaxOption, thrownError) {
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        beforeSend: function() {
            $("#ruleTree").html('<li class="item"><i class="fa fa-spinner fa-pulse"></i></li>');
            $("#formTree").html('<li class="item"><i class="fa fa-spinner fa-pulse"></i></li>');
        },
        success: function(data) {
            $("#ruleTree").html('');
            $("#formTree").html('');

            show(data.rules, $("#ruleTree"));
            show(data.forms, $("#formTree"));
        }
    });
}

function show(data, target) {
    $.each(data, function(i, obj) {
        var item = '<li id="item-' + obj.id.toString() + '" class="item">';
            item += '<div class="input-group">';
            item += writeFile(obj.id, obj.display);
            item += '<div id="button-' + obj.id.toString() + '" class="input-group-btn">';
            item += writeFileButton(obj.id);
            item += '</div>';
            item += '</div>';
            item += '</li>';

        target.append(item);
    });
}  

function reorder(id, index) {
    $.ajax({
        dataType: 'json',
        url: '/ajax/reorder',
        type: 'get',
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

function writeFile(id, display) {
    var item = '<div class="form-control file-caption kv-fileinput-caption">';
        item += '<input type="file" id="input-' + id.toString() + '" name="input-' + id.toString() + '" class="file-upload" accept="application/pdf" ';
        item += 'onchange="javascript:selectFile(null, $(\'#input-' + id + '\'), \'' + id + '\')">';
        item += '<div id="caption-' + id.toString() + '" class="file-caption-name">';
        item += '<i class="fa fa-file-pdf-o fa-fw"></i> ' + display;
        item += '</div>';
        item += '</div>';

    return item;
}

function writeFileButton(id) {
    var item = '<button id="more-' + id.toString() + '" class="btn btn-default btn-background dropdown-toggle" title="More..." type="button" data-toggle="dropdown">';
        item += '<i class="fa fa-gear"></i> ';
        item += '<i class="fa fa-caret-down"></i>';
        item += '</button>';
        item += '<ul class="dropdown-menu pull-right">';
        item += '<li>';
        item += '<a href="javascript:void(0);" onclick="javascript:editFileMode(' + id.toString() + ');"><i class="fa fa-edit fa-fw"></i> Edit</a>';
        item += '</li>';
        item += '<li>';
        item += '<a href="javascript:void(0);" ';
        item += 'onclick="javascript:var result = confirm(\'Do you want to delete this file?\'); if (result) { deleteFile(' + id.toString() + '); }"><i class="fa fa-trash-o fa-fw"></i> Delete</a>';
        item += '</li>';
        item += '</ul>';

    return item;
}

function addFile(button, target) {
    var id = button.attr('id') + '-temp-' + target.children('li').length.toString();
    var item = '<li id="item-' + id + '" class="item">';
            item += '<div class="input-group">';
            item += writeUploadFile(button, id);
            item += '<div id="button-' + id + '" class="input-group-btn">';
            item += '<button type="button" class="btn btn-default btn-background" title="Cancel" onclick="javascript:cancelFile($(\'#' + button.attr('id') + '\'), this);">';
            item += '<i class="fa fa-ban"></i>';
            item += '</button>';
            item += '<button type="button" class="btn btn-primary" title="Browse" onclick="$(\'#input-' + id + '\').click();">';
            item += '<i class="fa fa-folder-open-o"></i>';
            item += '</button>';
            item += '</div>';
            item += '</div>';
            item += '</li>';

    target.append(item);
    button.addClass('disabled');
}

function writeUploadFile(button, id) {
    var item = '<div class="form-control file-caption kv-fileinput-caption" tabindex="500">';
        item += '<input type="file" id="input-' + id + '" name="input-' + id + '" class="file-upload" accept="application/pdf" ';
        item += 'onchange="javascript:selectFile($(\'#' + button.attr('id') + '\'), $(\'#input-' + id + '\'), \'' + id + '\')">';
        item += '<div id="caption-' + id + '" class="file-caption-name">';
        item += '</div>';
        item += '</div>';

    return item;
}

function selectFile(button, file, id) {
    if (button != null) {
        if ($('#button-' + id).children('button').length > 2) { 
            $('#button-' + id).find('button:first').remove();
        }

        var item = '<button type="button" class="btn btn-default btn-background" title="Upload" ';
            item += 'onclick="javascript:uploadFile($(\'#' + button.attr('id') + '\'), $(\'#' + file.attr('id') + '\'), \'' + id + '\');">';
            item += '<i class="fa fa-arrow-circle-o-up"></i>';
            item += '</button>';

        $('#button-' + id).prepend(item); 
        $('#caption-' + id).html('<i class="fa fa-file fa-fw"></i> ' + file.val());
    }
    else {
        if ($('#button-' + id).children('button').length > 2) { 
            $('#button-' + id).find('button:first').remove();
        }

        var item = '<button type="button" class="btn btn-default btn-background" title="Upload" ';
            item += 'onclick="javascript:updateFile($(\'#' + file.attr('id') + '\'), \'' + id + '\');">';
            item += '<i class="fa fa-arrow-circle-o-up"></i>';
            item += '</button>';

        $('#button-' + id).prepend(item); 
        $('#caption-' + id).html('<i class="fa fa-file fa-fw"></i> ' + file.val());
    }
}

function cancelFile(button, element) {
    element.closest('ul li:last-child').remove();
    button.removeClass('disabled');
}

function uploadFile(button, document, id) {
    var file = document.get(0).files[0];
    var formData = new FormData();
    formData.append('File', file);
    formData.append('DocType', fileTypeID(button.attr('id')));

    if(file.size < 20971520) {
        $.ajax({
            dataType: 'json',
            url: '/ajax/uploadfile',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                $('#caption-' + id).addClass('text-danger');
                $('#caption-' + id).html('<i class="fa fa-times-circle fa-fw"></i> Upload failed.');
                $('#button-' + id).find('button').removeClass('disabled');
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            beforeSend: function() {
                $('#caption-' + id).html('<i class="fa fa-spinner fa-pulse"></i> Uploading... (<span id="progress-' + id + '">0</span>%)');
                $('#button-' + id).find('button:first').remove();
                $('#button-' + id).find('button:last').remove();
                $('#button-' + id).find('button').removeClass('btn-default');
                $('#button-' + id).find('button').removeClass('btn-background');
                $('#button-' + id).find('button').addClass('btn-danger');
                $('#button-' + id).find('button').addClass('disabled');
            },
            xhr: function(){
                // get the native XmlHttpRequest object
                var xhr = $.ajaxSettings.xhr() ;
                // set the onprogress event handler
                xhr.upload.onprogress = function(evt){ $('#progress-' + id).html(Math.ceil((evt.loaded / evt.total) * 100)); };
                // set the onload event handler
                xhr.upload.onload = function(){ $('#caption-' + id).html('<i class="fa fa-clock-o fa-fw"></i> Please wait...'); };
                // return the customized object
                return xhr;
            },
            success: function(obj) {
                $('#item-' + id).replaceWith(writeItem(obj));
                button.removeClass('disabled');
            }
        });
    }
    else {
        alert("File is to big");
    }
}

function writeItem(obj) {
    var item = '<li id="item-' + obj.id.toString() + '" class="item">';
        item += '<div class="input-group">';
        item += writeFile(obj.id, obj.Display);
        item += '<div id="button-' + obj.id + '" class="input-group-btn">';
        item += writeFileButton(obj.id);
        item += '</div>';
        item += '</div>';
        item += '</li>';

    return item;
}

function editFileMode(id) {
    $('#button-' + id).html(writeUploadFileButton(id));
}

function writeUploadFileButton(id) {
    var item = '<button type="button" class="btn btn-default btn-background" title="Cancel" onclick="javascript:cancelEditFile(' + id + ');">';
        item += '<i class="fa fa-ban"></i>';
        item += '</button>';
        item += '<button type="button" class="btn btn-primary" title="Browse" onclick="$(\'#input-' + id + '\').click();">';
        item += '<i class="fa fa-folder-open-o"></i>';
        item += '</button>';

    return item;
}

function cancelEditFile(id) {
    $('#button-' + id).html(writeFileButton(id));

    $.ajax({
        dataType: 'json',
        url: '/ajax/restorefile',
        type: 'get',
        cache: false,
        data: {
            'id': id
        },
        error: function(xhr, ajaxOption, thrownError) {
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        success: function(data) {
            $('#caption-' + id).html('<i class="fa fa-file-pdf-o fa-fw"></i> ' + data.display);
        } 
    });
}

function updateFile(document, id) {
    var file = document.get(0).files[0];
    var formData = new FormData();
    formData.append('ID', id);
    formData.append('File', file);

    if(file.size < 20971520) {
        $.ajax({
            dataType: 'json',
            url: '/ajax/updatefile',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                $('#caption-' + id).addClass('text-danger');
                $('#caption-' + id).html('<i class="fa fa-times-circle fa-fw"></i> Upload failed.');
                $('#button-' + id).find('button').removeClass('disabled');
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            beforeSend: function() {
                $('#caption-' + id).html('<i class="fa fa-spinner fa-pulse"></i> Uploading... (<span id="progress-' + id + '">0</span>%)');
                $('#button-' + id).find('button:first').remove();
                $('#button-' + id).find('button:last').remove();
                $('#button-' + id).find('button').removeClass('btn-default');
                $('#button-' + id).find('button').removeClass('btn-background');
                $('#button-' + id).find('button').addClass('btn-danger');
                $('#button-' + id).find('button').addClass('disabled');
            },
            xhr: function(){
                // get the native XmlHttpRequest object
                var xhr = $.ajaxSettings.xhr() ;
                // set the onprogress event handler
                xhr.upload.onprogress = function(evt){ $('#progress-' + id).html(Math.ceil((evt.loaded / evt.total) * 100)); };
                // set the onload event handler
                xhr.upload.onload = function(){ $('#caption-' + id).html('<i class="fa fa-clock-o fa-fw"></i> Please wait...'); };
                // return the customized object
                return xhr;
            },
            success: function(obj) {
                $('#item-' + id).replaceWith(writeItem(obj));
            }
        });
    }
    else {
        alert("File is to big");
    }
}

function deleteFile(id) {
    $.ajax({
        dataType: 'json',
        url: '/ajax/deletefile',
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

function updateOther(id) {
    var file = $('#file-' + id).get(0).files[0];
    var formData = new FormData();
    formData.append('id', id);
    formData.append('file', file);

    if(file.size < 20971520) {
        $.ajax({
            dataType: 'json',
            url: '/ajax/updateother',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                $('#caption-' + id).addClass('text-danger');
                $('#caption-' + id).html('<i class="fa fa-times-circle fa-fw"></i> Upload failed.');
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            beforeSend: function() {
                $('#caption-' + id).html('<i class="fa fa-spinner fa-pulse"></i> Uploading... (<span id="progress-' + id + '">0</span>%)');
            },
            xhr: function(){
                // get the native XmlHttpRequest object
                var xhr = $.ajaxSettings.xhr() ;
                // set the onprogress event handler
                xhr.upload.onprogress = function(evt){ $('#progress-' + id).html(Math.ceil((evt.loaded / evt.total) * 100)); };
                // set the onload event handler
                xhr.upload.onload = function(){ $('#caption-' + id).html('<i class="fa fa-clock-o fa-fw"></i> Please wait...'); };
                // return the customized object
                return xhr;
            },
            success: function(obj) {
                $('#caption-' + id).html(obj);
            }
        });
    }
    else {
        alert("File is to big");
    }
}

function fileTypeID(fileType) {
    switch (fileType) {
        case 'Rule':
            return 1;
            break;
        case 'Form':
            return 2;
            break;
        default:
            return 3;
            break;
    }
}