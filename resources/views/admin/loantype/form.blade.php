<div class="box-body">
    <div class="form-group">
        {{ Form::label('name', 'ชื่อประเภทเงินกู้พิเศษ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('name', null, [
                'placeholder' => 'ตัวอย่าง: เงินกู้สวัสดิการเพื่อการศึกษาบุตร',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('cash_limit', 'วงเงินกู้สูงสุด', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('cash_limit', null, [
                'placeholder' => 'ตัวอย่าง: 100000',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('installment_limit', 'ระยะเวลาเผื่อชำระสูงสุด', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('installment_limit', null, [
                'placeholder' => 'ตัวอย่าง: 12',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('start_date', 'วันที่เริ่มใช้', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" id="start_date" style="padding: 0 15px;">
            {{ Form::text('start_date', null, [
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
        {{ Form::label('expire_date', 'วันที่หมดอายุ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" id="expire_date" style="padding: 0 15px;">
            {{ Form::text('expire_date', null, [
                'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                'class'=>'form-control'])
            }}       
            <span class="input-group-addon">
                <span class="fa fa-calendar">
                </span>
            </span> 
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
        'onclick'=> 'javascript:window.location = "/admin/loantype";'])
    }}
</div>
<!-- /.box-footer -->