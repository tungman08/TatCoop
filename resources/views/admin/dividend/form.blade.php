<div class="box-body">
    <div class="form-group">
        {{ Form::label('rate_year', 'ประจำปี ค.ศ.', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" id="rate_year" style="padding: 0 5px;">
            {{ Form::text('rate_year', null, [
                ($edit) ? 'readonly' : '',
                'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                'class'=>'form-control'])
            }}       
            <span class="input-group-addon">
                <span class="fa fa-calendar">
                </span>
            </span> 
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('rate', 'อัตราเงินปันผล (%)', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('rate', null, [
                'placeholder' => 'ตัวอย่าง: 0-100',
                'autocomplete'=>'off',
                'class'=>'form-control'])
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
        'onclick'=>'javascript:window.location = "/admin/dividend";'])
    }}
    @if ($edit)
        {{ Form::button('<i class="fa fa-trash"></i> ลบ', [
            'class'=>'btn btn-danger btn-flat pull-right', 
            'onclick'=>'javascript:var result = confirm(\'คุณต้องการลบรายการนี้ใช่ไหม ?\'); if (result) { window.location = "/admin/dividend/' . $dividend->id . '/erase"; }'])
        }}
    @endif
</div>
<!-- /.box-footer -->