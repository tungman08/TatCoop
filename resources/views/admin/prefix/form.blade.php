<div class="box-body">
    <div class="form-group">
        {{ Form::label('name', 'คำนำหน้าชื่อ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('name', null, ['class'=>'form-control']) }}
        </div>
    </div>
</div>
<!-- /.box-body -->

<div class="box-footer">
    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
        'id'=>'save',
        'type' => 'submit', 
        'class'=>'btn btn-primary btn-flat'])
    }}
    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
        'class'=>'btn btn-default btn-flat', 
        'onclick'=> 'javascript:history.go(-1);'])
    }}
</div>
<!-- /.box-footer -->