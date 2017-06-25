<h4>1.ตรวจสอบคุณสมบัติผู้กู้</h4>
{{ Form::hidden('step', '1') }}

<div class="form-group">
    {{ Form::label('payment_type', 'วิธีผ่อนชำระ', [
        'class' => 'control-label']) 
    }}
    {{ Form::select('payment_type', App\PaymentType::all()->lists('name', 'id'), null, [
        'id' => 'payment_type',
        'class' => 'form-control']) 
    }}
</div>

<div class="form-group">
    {{ Form::label('outstanding', 'ยอดเงินที่ต้องการขอกู้', [
        'class' => 'control-label']) 
    }}
    {{ Form::text('outstanding', null, [
        'id' => 'outstanding',
        'required' => true,
        'min' => 1,
        'placeholder' => 'ตัวอย่าง: 100000',
        'autocomplete' => 'off',
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
        'class'=>'form-control'])
    }}
</div>

@if ($member->profile->employee->employee_type_id < 3)
    <div class="form-group">
        {{ Form::label('salary', 'เงินเดือนของผู้กู้', [
            'class'=>'control-label']) 
        }}
        {{ Form::text('salary', null, [
            'id' => 'salary',
            'required' => true,
            'min' => 1,
            'placeholder' => 'ตัวอย่าง: 50000',
            'autocomplete'=>'off',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control'])
        }}
    </div>

    <div class="form-group">
        {{ Form::label('net_salary', 'เงินเดือนสุทธิของผู้กู้หักทุกอย่างใน slip', [
            'class'=>'control-label']) 
        }}
        {{ Form::text('net_salary', null, [
            'id' => 'net_salary',
            'required' => true,
            'min' => 1,
            'placeholder' => 'ตัวอย่าง: 20000',
            'autocomplete'=>'off',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control'])
        }}
    </div>
@endif

<hr />

{{ Form::button('<i class="fa fa-arrow-circle-right"></i> ถัดไป', [
    'id' => 'step1',
    'type' => 'submit',
    'class'=>'btn btn-primary btn-flat nextBtn'])
}}