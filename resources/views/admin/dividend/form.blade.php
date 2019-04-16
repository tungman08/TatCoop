<div class="box-body">
    <div class="form-group">
        {{ Form::label('rate_year', 'ประจำปี', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                {{ Form::text('rate_year', null, [
                    'id' => 'rate_year',
                    ($edit) ? 'readonly' : '',
                    'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                    'autocomplete'=>'off',
                    'class'=>'form-control'])
                }}    
            </div>   
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('shareholding_rate', 'อัตราเงินปันผล (%)', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('shareholding_rate', null, [
                'placeholder' => 'ตัวอย่าง: 0-100',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div> 
    <div class="form-group">
        {{ Form::label('loan_rate', 'อัตราเงินเฉลี่ยคืน (%)', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('loan_rate', null, [
                'placeholder' => 'ตัวอย่าง: 0-100',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('release_date', 'วันที่เผยแพร่', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                {{ Form::text('release_date', null, [
                    'id'=>'release_date',
                    'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                    'autocomplete'=>'off',
                    'class'=>'form-control'])
                }}   
            </div>    
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
        'onclick'=>'javascript:history.go(-1);'])
    }}
</div>
<!-- /.box-footer -->