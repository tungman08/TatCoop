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
        {{ Form::label('code', 'ชื่อ-นามสกุล', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('code', $member->profile->fullName, [
                'readonly', 
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('shareholding', 'หุ้นเรือนหุ้นสะสม', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('shareholding', null, [
                'readonly',
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('debt', 'ยอดหนี้คงเหลือ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('debt', null, [
                'readonly',
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('code', 'เลขที่สัญญา', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('code', ($edit) ? null : 'รหัสจะถูกสร้างโดยอัตโนมัติ', [
                'readonly',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('loan_type_id', 'ประเภทการกู้', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::select('loan_type_id', ['L' => 'กู้สามัญ', 'S' => 'กู้ฉุกเฉิน', 'M' => 'กู้เฉพาะกิจ'], null, [
                'class' => 'form-control']) 
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('loan_date', 'วันที่กู้', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" id="datepicker" style="padding: 0 15px;">
            {{ Form::text('loan_date', null, [
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
        {{ Form::label('salary', 'เงินเดือนปัจจุบัน', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('salary', null, [
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: 25000', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('amount', 'จำนวนเงินที่กู้', [
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
        {{ Form::label('preriod', 'จำนวนงวดผ่อนชำระ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('preriod', null, [
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: 12', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('rate', 'อัตตราดอกเบี้ย', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('rate', '6.5', [
                'class'=>'form-control'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('surety', 'ผู้ค้ำประกัน', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::button('<i class="fa fa-plus"></i> เพิ่มผู้ค้ำประกัน', [
                'class'=>'btn btn-default btn-flat'])
            }}  
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('documents', 'เอกสาร', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::button('<i class="fa fa-plus"></i> เพิ่มเอกสาร', [
                'class'=>'btn btn-default btn-flat'])
            }}  
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('calc', '&nbsp;', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::button('<i class="fa fa-calculator"></i> คำนวณ', [
                'class'=>'btn btn-default btn-flat'])
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
        'onclick'=>'javascript:window.location = "/admin/member/' . $member->id . '/2";'])
    }}
    @if ($edit)
        {{ Form::button('<i class="fa fa-trash"></i> ลบ', [
            'class'=>'btn btn-danger btn-flat pull-right', 
            'onclick'=>'javascript:window.location = "/admin/member/' . $id . '/delete";'])
        }}
    @endif
</div>
<!-- /.box-footer -->