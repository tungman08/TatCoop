<h4>2.ตรวจสอบรายละเอียด</h4>
{{ Form::hidden('step', '2') }}

<div class="form-group">
    {{ Form::label('loan_code', 'รหัสสัญญากู้ยืม', [
        'class'=>'control-label']) 
    }}
    {{ Form::text('loan_code', null, [
        'id' => 'loan_code',
        'required' => true,
        'placeholder' => 'กรุณาป้อนรหัสสัญญาเงินกู้',
        'data-inputmask' => "'mask': '9999/9999','placeholder': '0','autoUnmask': false,'removeMaskOnSubmit': false",
        'data-mask',
        'autocomplete'=>'off',
        'class'=>'form-control'])
    }}
</div>

<div class="form-group">
    {{ Form::label('loaned_at', 'วันที่ทำสัญญา', [
        'class'=>'control-label']) 
    }}
    <div class="input-group">
        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
        {{ Form::text('loaned_at', null, [
            'id' => 'loaned_at',
            'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
            'autocomplete'=>'off',
            'class'=>'form-control'])
        }}  
    </div>           
</div>

@php($loan = App\Loan::find($loan->id))
<div class="table-responsive">
    <table class="table table-info">
        <tr>
            <th style="width:20%; border-top: 1px solid #fff;">ชื่อผู้กู้:</th>
            <td style="border-top: 1px solid #fff;">{{ $loan->member->profile->fullname }}</td>
        </tr>
        <tr>
            <th>ประเภทการกู้:</th>
            <td>{{ $loan->loanType->name }}</td>
        </tr>    
        <tr>
            <th>วิธีการผ่อนชำระ:</th>
            <td>{{ $loan->paymentType->name }}</td>
        </tr>
        <tr>
            <th>ยอดเงินที่ขอกู้:</th>
            <td>{{ number_format($loan->outstanding, 2, '.', ',') }} บาท</td>
        </tr>       
        <tr>
            <th>จำนวนงวดการผ่อนชำระ:</th>
            <td>{{ number_format($loan->period, 0, '.', ',') }} งวด</td>
        </tr> 
        <tr>
            <th style="width:20%; border-top: none;">อัตราดอกเบี้ย:</th>
            <td id="rate" style="border-top: none;">0.0%</td>
        </tr>
        <tr>
            <th>จำนวนที่ต้องชำระทั้งหมด:</th>
            <td id="total_pay">0.00</td>
        </tr>
        <tr>
            <th style="border-bottom: 1px solid #f4f4f4;">จำนวนดอกเบี้ยทั้งหมด:</th>
            <td id="total_interest"  style="border-bottom: 1px solid #f4f4f4;">0.00</td>
        </tr> 
    </table>
</div>

<span style="font-weight: 700; margin-left: 8px;">ตารางการผ่อนชำระ (คำนวณเบื้องต้น):</span>
<table class="table table-striped table-hover" id="dataTables-loan">
    <thead>
        <tr>
            <th style="width: 20%;">ลำดับ</th>
            <th style="width: 20%;">จำนวนเงินที่ต้องชำระ</th>
            <th style="width: 20%;">เป็นดอกเบี้ย</th>
            <th style="width: 20%;">เป็นเงินต้น</th>
            <th style="width: 20%;">เงินต้นคงเหลือ</th>
        </tr>
    </thead>
</table>

<hr />

{{ Form::button('<i class="fa fa-file-o"></i> ตกลงทำสัญญา', [
    'id' => 'step3',
    'type' => 'submit',
    'class'=>'btn btn-success btn-flat nextBtn'])
}}