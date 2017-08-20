<div class="box-body">
    <div class="form-group">
        {{ Form::label('pay_date', 'วันที่ชำระ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" id="datepicker" style="padding: 0 5px;">
            {{ Form::text('pay_date', Diamond::today()->format('Y-m-d'), [
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
        {{ Form::label('amount', 'จำนวนเงินที่ต้องการชำระ', [
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
        <div class="col-md-offset-2 padding-l-xs">
            {{ Form::button('<i class="fa fa-calculator"></i> คำนวณ', [
                'id'=>'calculate',
                'type' => 'button', 
                'data-id' => $loan->id,
                'class'=>'btn btn-default btn-flat'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('principle', 'จำนวนเงินต้น', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('principle', null, [
                'readonly' => true,
                'class'=>'form-control', 
                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('interest', 'จำนวนดอกเบี้ย', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('interest', null, [
                'readonly' => true,
                'class'=>'form-control', 
                'placeholder'=>'กรุณากดปุมคำนวณ...', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
</div>
<!-- /.box-body -->

<div class="box-footer">
    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
        'id'=>'save',
        'disabled' => true,
        'type' => 'submit', 
        'class'=>'btn btn-primary btn-flat'])
    }}
    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
        'class'=>'btn btn-default btn-flat', 
        'onclick'=> 'javascript:history.go(-1);'])
    }}
</div>
<!-- /.box-footer -->