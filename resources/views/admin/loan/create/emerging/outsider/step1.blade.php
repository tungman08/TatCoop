<h4>1.ตรวจสอบคุณสมบัติผู้กู้</h4>
{{ Form::hidden('step', '1') }}

<div class="form-group">
    {{ Form::label('outstanding', 'ยอดเงินที่ต้องการขอกู้', [
        'class' => 'control-label']) 
    }}
    {{ Form::text('outstanding', null, [
        'id' => 'outstanding',
        'required' => true,
        'min' => 1,
        'placeholder' => 'ตัวอย่าง: 100000',
        'autocomplete'=>'off',
        'onkeypress' => 'javascript:return isNumberKey(event);',
        'class' => 'form-control'])
    }}
</div>

<div class="form-group">
    {{ Form::label('period', 'จำนวนงวดการผ่อนชำระ', [
        'class' => 'control-label']) 
    }}
    {{ Form::text('period', null, [
        'id' => 'period',
        'required' => true,
        'min' => 1,
        'placeholder' => 'ตัวอย่าง: 24',
        'autocomplete'=>'off',
        'onkeypress' => 'javascript:return isNumberKey(event);',
        'class' => 'form-control'])
    }}
</div>

<div class="form-group">
    {{ Form::label('payment_type_id', 'วิธีผ่อนชำระ', [
        'class' => 'control-label']) 
    }}
    {{ Form::select('payment_type_id', App\PaymentType::all()->lists('name', 'id'), null, [
        'id' => 'payment_type_id',
        'class' => 'form-control']) 
    }}
</div>

<hr />

{{ Form::button('<i class="fa fa-arrow-circle-right"></i> ถัดไป', [
    'id' => 'step1',
    'type' => 'submit',
    'class'=>'btn btn-primary btn-flat nextBtn'])
}}