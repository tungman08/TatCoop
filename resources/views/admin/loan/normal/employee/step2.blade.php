<h4>2.ตรวจสอบคุณสมบัติผู้ค้ำประกัน</h4>

<div class="form-group">
    {{ Form::label('add_surety', 'รหัสสมาชิกของผู้ค้ำ (ถ้าผู้กู้ต้องการใช้หุ้นตัวของตนเองค้ำ ให้ใส่รหัสสมาชิกของผู้กู้)', [
        'class'=>'control-label']) 
    }}
    <div class="input-group">
        {{ Form::text('add_surety', null, [
            'id' => 'add_surety',
            'placeholder' => 'รหัสสมาชิก 5 หลัก',
            'data-inputmask' => "'mask': '99999','placeholder': '0','removeMaskOnSubmit': true",
            'data-mask',
            'autocomplete'=>'off',
            'class'=>'form-control'])
        }}
        <span class="input-group-btn">
            {{ Form::button('<i class="fa fa-plus-circle"></i> เพิ่ม', [
                'id' => 'button_add_surety',
                'class'=>'btn btn-default btn-flat'])
            }}
        </span>
    </div>
</div>

<hr />
{{ Form::button('<i class="fa fa-arrow-circle-right"></i> ถัดไป', [
    'id' => 'step2',
    'class'=>'btn btn-default btn-flat nextBtn'])
}}
<span id="message_step2" class="text-danger margin-l-lg"><span>