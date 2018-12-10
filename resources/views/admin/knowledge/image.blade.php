<div class="box-body">
    <ul id="preview-photo" class="listsClass" style="max-height: 310px; overflow-y: auto; overflow-x: hidden;">
        @foreach ($images as $image)
            <li id="photo-{{ $image->id }}" style="border-bottom: 1px solid #f4f4f4; padding: 5px 0;">
                <div class="row">
                    <div class="col-xs-4">
                        <img src="{{ FileManager::get('attachments', $image->file) }}" class="img-responsive" style="max-height: 70px;" alt="" />
                    </div>
                    <div class="col-xs-8">
                        <input id="url-{{ $image->id }}" type="text" class="form-control" value="{{ FileManager::get('attachments', $image->file) }}" readonly />
                        <div class="btn-group" style="margin-top: 2px;">
                            <button class="btn btn-default btn-flat" onclick="copy($('#url-{{ $image->id }}'));"><i class="fa fa-copy"></i></button>
                            <button class="btn btn-default btn-flat"
                                onclick="javascript:var result = confirm('คุณต้องการลบรูปนี้ใช่หรือไม่ ?'); if (result) { deleteImage('knowledge', {{ $image->id }}); }">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
<!-- /.box-body -->

<div class="box-footer">
    <button type="button" class="btn btn-primary btn-flat" onclick="javascript:$('#uploadimage').click();">
        <i class="fa fa-plus-circle fa-fw"></i> เพิ่มรูปประกอบ
    </button>
    <input type="file" id="uploadimage" name="image-uploadimage" class="file-upload" accept="image/*"
        onchange="javascript:uploadImage($(this), 'knowledge', {{ $knowledge->id }});" />
</div>
<!-- /.box-footer -->