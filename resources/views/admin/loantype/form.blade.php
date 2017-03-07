<div class="box-body">
    <div class="form-group">
        {{ Form::label('name', 'ชื่อประเภทเงินกู้', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('name', null, [
                'placeholder' => 'ตัวอย่าง: เงินกู้สวัสดิการเพื่อการศึกษาบุตร',
                'autocomplete'=>'off',
                'readonly'=>$edit,
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('rate', 'อัตราดอกเบี้ย', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('rate', null, [
                'placeholder' => 'ตัวอย่าง: 6.5',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('start_date', 'วันที่เริ่มใช้', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" id="start_date" style="padding: 0 5px;">
            {{ Form::text('start_date', null, [
                'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                'readonly'=>$edit,
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

        <div class="col-sm-10 input-group" id="expire_date" style="padding: 0 5px;">
            {{ Form::text('expire_date', null, [
                'placeholder'=>'กรุณาเลือกจากปฏิทิน...', 
                'readonly'=>$edit,
                'class'=>'form-control'])
            }}       
            <span class="input-group-addon">
                <span class="fa fa-calendar">
                </span>
            </span> 
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('limits', 'เงื่อนไข', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            <div class="table-responsive">
                <table id="limits" class="table" style="margin-bottom: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 20%; padding-left: 0px;">วงเงินกู้เริ่มต้น (บาท)</th>
                            <th style="width: 20%; padding-left: 0px;">ถึง (บาท)</th>
                            <th style="width: 20%; padding-left: 0px;">จำนวนหุ้นที่ใช้ขอกู้ (%)</th>
                            <th style="width: 20%; padding-left: 0px;">จำนวนผู้ค้ำประกัน (คน)</th>
                            <th style="width: 20%; padding-left: 0px;">จำนวนงวดผ่อนชำระสูงสุด (เดือน)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$edit)
                            @if (!is_null(old('limits')))
                                @foreach(old('limits') as $key => $limit)
                                    @include('admin.loantype.limits', ['edit' => $edit, 'key' => $key, 'limit' => $limit])
                                @endforeach
                            @else
                                @include('admin.loantype.limits', ['edit' => $edit, 'key' => 0, 'limit' => null])
                            @endif
                        @else
                            @foreach($loantype->limits as $key => $limit)
                                @include('admin.loantype.limits', ['edit' => $edit, 'key' => $key, 'limit' => $limit])
                            @endforeach
                        @endif
                    </tbody>
                </table>  
            </div>

            {{ Form::button('<i class="fa fa-plus-circle"></i> เพิ่มเงื่อนไขการกู้', [
                'id'=>'add_limit',
                'class'=>'btn btn-default btn-flat', 
                'disabled'=>true])
            }}
            {{ Form::button('<i class="fa fa-minus-circle"></i> ลบเงื่อนไขการกู้', [
                'id'=>'delete_limit',
                'class'=>'btn btn-default btn-flat', 
                'disabled'=>true])
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
        'onclick'=> 'javascript:window.location = "/admin/loantype' . (($edit) ? ('/' . $loantype->id) : '') . '";'])
    }}
</div>
<!-- /.box-footer -->