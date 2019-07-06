<div class="box-body">
    <div class="form-group">
        {{ Form::label('start_date', 'วันที่สมัครเป็นสมาชิก', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                {{ Form::text('start_date', null, [
                    'id' => 'start_date',
                    'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                    'class'=>'form-control'])
                }} 
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[member_code]', 'รหัสสมาชิก', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('profile[member_code]', ($edit) ? $member->member_code : 'รหัสจะถูกสร้างโดยอัตโนมัติ', [
                'readonly',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[employee][code]', 'รหัสพนักงาน', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('profile[employee][code]', null, [
                'id'=>'employee_code',
                'placeholder' => 'รหัสพนักงาน 5 หลัก...',
                'data-inputmask'=>"'mask': '99999','placeholder': '0','autoUnmask': true,'removeMaskOnSubmit': true",
                'data-mask',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    @if (!$edit)
        <div class="form-group">
            {{ Form::label('fee', 'ค่าธรรมเนียม', [
                'class'=>'col-sm-2 control-label']) 
            }}

            <div class="col-sm-10">
                {{ Form::text('fee', null, [
                    'id'=>'fee',
                    'readonly',
                    'class'=>'form-control'])
                }}
            </div>
        </div>
    @endif
    <div class="form-group">
        {{ Form::label('profile[prefix_id]', 'คำนำหน้าชื่อ', [
            'placeholder' => 'กรุณาเลือกคำนำหน้าชื่อ...',
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::select('profile[prefix_id]', $prefixs->lists('name', 'id'), null, [
                'id' => 'prefix',
                'class' => 'form-control']) 
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[name]', 'ชื่อ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('profile[name]', null, [
                'id' => 'name',
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: วศิน', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[lastname]', 'นามสกุล', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('profile[lastname]', null, [
                'id' => 'lastname',
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: มีปรีชา', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[citizen_code]', 'หมายเลขบัตรประชาชน', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('profile[citizen_code]', null, [
                'id' => 'citizen_code',
                'class'=>'form-control', 
                'placeholder'=>'หมายเลขบัตรประชาชน 13 หลัก...', 
                'data-inputmask'=>"'mask': '9-9999-99999-99-9','placeholder':'0','autoUnmask': true,'removeMaskOnSubmit':true",
                'data-mask',
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[employee][employee_type_id]', 'ประเภทสมาชิก', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::select('profile[employee][employee_type_id]', $employee_types->lists('name', 'id'), null, [
                'id' => 'employee_type',
                'class' => 'form-control']) 
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[birth_date]', 'วันเกิด', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                {{ Form::text('profile[birth_date]', null, [
                    'id' => 'birth_date',
                    'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                    'autocomplete'=>'off',
                    'class'=>'form-control'])
                }}   
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[address]', 'ที่อยู่', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('profile[address]', null, [
                'id' => 'address',
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: 1600 ถ.เพชรบุรีตัดใหม่', 
                'autocomplete'=>'off'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[province_id]', 'จังหวัด', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::select('profile[province_id]', $provinces->lists('name', 'id'), null, [
                'id' => 'province_id',
                'class' => 'form-control']) 
            }}
        </div>
    </div>    
    <div class="form-group">
        {{ Form::label('profile[district_id]', 'อำเภอ/เขต', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::select('profile[district_id]', $districts->lists('name', 'id'), null, [
                'id' => 'district_id',
                'class' => 'form-control']) 
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[subdistrict_id]', 'ตำบล/แขวง', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::select('profile[subdistrict_id]', $subdistricts->lists('name', 'id'), null, [
                'id' => 'subdistrict_id',
                'class' => 'form-control']) 
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('profile[postcode][code]', 'รหัสไปรษณีย์', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('profile[postcode][code]', null, [
                'readonly',
                'id'=>'postcode',
                'placeholder' => 'รหัสไปรษณีย์...',
                'class'=>'form-control'])
            }}
        </div>
    </div>
</div>
<!-- /.box-body -->

<div class="box-footer">
    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
        'id'=>'save',
        'type' => 'submit', 
        'class'=>'btn btn-primary btn-flat'])
    }}
    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
        'class'=>'btn btn-default btn-flat', 
        'onclick'=> 'javascript:history.go(-1);'])
    }}
</div>
<!-- /.box-footer -->