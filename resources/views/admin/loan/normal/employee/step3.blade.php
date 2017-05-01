<h4>3.ตรวจสอบรายละเอียด</h4>
{{ Form::hidden('step', '3') }}

@php($loan = App\Loan::find($loan_id))
<div class="table-responsive">
    <table class="table table-info">
        <tr>
            <th style="width:20%; border-top: 1px solid #fff;">ชื่อผู้กู้:</th>
            <td style="border-top: 1px solid #fff;">{{ $loan->member->profile->fullName }}</td>
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
            <th>ผู้ค้ำประกัน:</th>
            <td>
                <ul class="list-info">
                    @foreach($loan->sureties as $surty)
                        <li>
                            @if($surty->id == $loan->member_id)
                                {{ $surty->profile->fullName }} (ค้ำประกันด้วยหุ้นตนเอง) จำนวน {{ number_format($surty->pivot->amount, 2, '.', ',') }} บาท
                            @else
                                {{ $surty->profile->fullName }} จำนวน {{ number_format($surty->pivot->amount, 2, '.', ',') }} บาท
                            @endif
                        </li>
                    @endforeach
                </ul>
            </td>
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

<span style="font-weight: 700; margin-left: 8px;">ตารางการผ่อนชำระ:</span>
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

<div class="form-group">
    {{ Form::label('loan_code', 'รหัสสัญญากู้ยืม', [
        'class'=>'control-label']) 
    }}
    {{ Form::text('loan_code', null, [
        'id' => 'loan_code',
        'required' => true,
        'placeholder' => 'ตัวอย่าง: xxxx',
        'autocomplete'=>'off',
        'class'=>'form-control'])
    }}
</div>

{{ Form::button('<i class="fa fa-file-o"></i> ตกลงทำสัญญา', [
    'id' => 'step3',
    'type' => 'submit',
    'class'=>'btn btn-success btn-flat nextBtn'])
}}