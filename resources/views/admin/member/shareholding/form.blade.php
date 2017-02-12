<div class="box-body">
    <div class="form-group">
        {{ Form::label('code', 'รหัสสมาชิก', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('code', $member->memberCode, [
                'readonly', 
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('name', 'ชื่อ-นามสกุล', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('name', ($member->profile->name == '<ข้อมูลถูกลบ>') ? '' : $member->profile->fullName, [
                'readonly', 
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('shareholding', 'จำนวนหุ้นต่อเดือน', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('shareholding', $member->shareholding, [
                'readonly',
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('shareholding_total', 'ทุนเรือนหุ้นสะสม', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('shareholding_total', $member->shareholdings()->sum('amount'), [
                'readonly',
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('pay_date', 'วันที่ชำระ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" id="datepicker" style="padding: 0 15px;">
            {{ Form::text('pay_date', null, [
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
        {{ Form::label('shareholding_type_id', 'ประเภท', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::select('shareholding_type_id', $shareholding_types->lists('name', 'id'), ((!$edit) ? 2 : null), [
                'id' => 'shareholding_type_id',
                'class' => 'form-control']) 
            }}      
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('amount', 'ค่าหุ้น', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('amount', null, [
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: 10000', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('remark', 'หมายเหตุ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::textarea ('remark', null, [
                'style'=>'height:100px; min-height:100px; max-height:100px;',
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
        'onclick'=>'javascript:window.location = "/admin/member/' . $member->id . '/1";'])
    }}
    @if ($edit)
        {{ Form::button('<i class="fa fa-trash"></i> ลบ', [
            'class'=>'btn btn-danger btn-flat pull-right', 
            'onclick'=>'javascript:var result = confirm(\'คุณต้องการลบรายการนี้ใช่ไหม ?\'); if (result) { window.location = "/admin/member/' . $member->id . '/shareholding/' . $shareholding->id . '/erase"; }'])
        }}
    @endif
</div>
<!-- /.box-footer -->