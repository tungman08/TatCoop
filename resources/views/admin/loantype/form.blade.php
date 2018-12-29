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
                'autocomplete'=>'off',
                'readonly'=>($edit) ? ($loantype->id == 1 || $loantype->id == 2) ? true : false : false,
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
                'autocomplete'=>'off',
                'readonly'=>($edit) ? ($loantype->id == 1 || $loantype->id == 2) ? true : false : false,
                'class'=>'form-control'])
            }}       
            <span class="input-group-addon">
                <span class="fa fa-calendar">
                </span>
            </span> 
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-10">
            <strong>เงื่อนไขเฉพาะพนักงาน/ลูกจ้าง ททท.</strong>
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('employee_ratesalary', 'จำนวนเท่าเงินเดือนที่สามารถกู้ได้ (เท่า)', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('employee_ratesalary', null, [
                'placeholder' => 'ตัวอย่าง: 40',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('employee_netsalary', 'เงินเดือนสุทธิต่ำสุดเมื่อหักค่างวดแล้ว', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('employee_netsalary', null, [
                'placeholder' => 'ตัวอย่าง: 3000',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-10">
            <strong>เงื่อนไขเฉพาะบุคคลภายนอก</strong>
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('outsider_rateshareholding', 'จำนวนหุ้นที่ต้องใช้ (%)', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('outsider_rateshareholding', ($edit) ? $loantype->outsider_rateshareholding * 100 : null, [
                'placeholder' => 'ตัวอย่าง: 80',
                'autocomplete'=>'off',
                'class'=>'form-control'])
            }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-10">
            <strong>เงื่อนไขอื่นๆ</strong>
        </div>
    </div>

    @if (!$edit)
        <div class="form-group">
            {{ Form::label('max_loansummary', 'ผลรวมยอดเงินกู้ทุกประเภทสูงสุด', [
                'class'=>'col-sm-2 control-label']) 
            }}

            <div class="col-sm-10">
                {{ Form::text('max_loansummary', null, [
                    'placeholder' => 'ตัวอย่าง: 1000000',
                    'autocomplete'=>'off',
                    'class'=>'form-control'])
                }}
            </div>
        </div>
    @else
        @if ($loantype->id != 2)
            <div class="form-group">
                {{ Form::label('max_loansummary', 'ผลรวมยอดเงินกู้ทุกประเภทสูงสุด', [
                    'class'=>'col-sm-2 control-label']) 
                }}

                <div class="col-sm-10">
                    {{ Form::text('max_loansummary', null, [
                        'placeholder' => 'ตัวอย่าง: 1000000',
                        'autocomplete'=>'off',
                        'class'=>'form-control'])
                    }}
                </div>
            </div>
        @else
            {{ Form::hidden('max_loansummary', 0) }}
        @endif
    @endif

    <div class="form-group">
        {{ Form::label('limits', 'เงื่อนไขขอบเขต', [
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
                                    @include('admin.loantype.limits', ['key' => $key, 'limit' => $limit])
                                @endforeach
                            @else
                                @include('admin.loantype.limits', ['key' => 0, 'limit' => null])
                            @endif
                        @else
                            @if (!is_null(old('limits')))
                                @foreach(old('limits') as $key => $limit)
                                    @include('admin.loantype.limits', ['key' => $key, 'limit' => $limit])
                                @endforeach
                            @else
                                @foreach($loantype->limits as $key => $limit)
                                    @include('admin.loantype.limits', ['key' => $key, 'limit' => $limit])
                                @endforeach
                            @endif
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

            <span>* ใช้เพื่อเพิ่มช่องป้อนเงื่อนไข สำหรับกรณีที่ประเภทเงินกู้มีหลายเงื่อนไข ถ้ามีเงื่อนไขเดียวไม่ต้องกด</span>
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
        'onclick'=> 'javascript:history.go(-1);'])
    }}
</div>
<!-- /.box-footer -->