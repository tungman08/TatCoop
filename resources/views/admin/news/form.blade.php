<div class="box-body">
    <div class="form-group">
        {{ Form::label('title', 'หัวข้อข่าว', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('title', ($edit) ? $news->title : null, [
                'class'=>'form-control',
                'placeholder'=>'ป้อนหัวข้อข่าว',
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('content', 'เนื้อหาข่าว', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::textarea('content', ($edit) ? $news->content : null, [
                'class'=>'form-control textarea'])
            }}        
        </div>
    </div>
</div>

<!-- /.box-body -->
<div class="box-footer">
    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
        'type' => 'submit', 
        'class'=>'btn btn-primary btn-flat'])
    }}
    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
        'class'=>'btn btn-default btn-flat', 
        'onclick'=>'javascript:history.go(-1);'])
    }}
</div>
<!-- /.box-footer -->