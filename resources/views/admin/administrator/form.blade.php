<div class="box-body">
    <div class="form-group">
        {{ Form::label('name', 'ชื่อผู้ดูแลระบบ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('name', null, [
                'required',
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: ร.ต.ท.วศิน มีปรีชา', 
                'autocomplete'=>'off'])
            }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('email', 'อีเมล/บัญชีผู้ดูแลระบบ', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('email', null, [
                'required',
                ($edit) ? 'readonly' : '',
                'class'=>'form-control', 
                'placeholder'=>'ตัวอย่าง: admin@tatcoop.com', 
                'autocomplete'=>'off'])
            }}        
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('new_password', 'รหัสผ่าน', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10 input-group" style="padding: 0 5px;">
            {{ Form::text('new_password', null, [
                ($edit) ? '' : 'required',
                'class'=>'form-control', 
                'placeholder'=>'กดปุ่ม "ตั้งรหัสผ่าน" เพื่อสร้างรหัสผ่าน', 
                'autocomplete'=>'off', 
                'id'=>'new_password', 
                'readonly'=>'true']) 
            }}
            <span class="input-group-btn">
                {{ Form::button('<i class="fa fa-lock"></i> ตั้งรหัสผ่าน', [
                    'class'=>'btn btn-default', 
                    'id'=>'genpassword'])
                }}
            </span>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('new_password_confirmation', 'ยืนยันรหัสผ่าน', [
            'class'=>'col-sm-2 control-label']) 
        }}

        <div class="col-sm-10">
            {{ Form::text('new_password_confirmation', null, [
                ($edit) ? '' : 'required',
                'class'=>'form-control', 
                'placeholder'=>'พิมพ์รหัสผ่านให้ตรงกับรหัสผ่านด้านบน', 
                'autocomplete'=>'off'])
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
        'onclick'=>'javascript:window.location = "/admin/administrator";'])
    }}
    @if ($edit)
        {{ Form::button('<i class="fa fa-trash"></i> ลบ', [
            'class'=>'btn btn-danger btn-flat pull-right', 
            'onclick'=>'javascript:window.location = "/admin/administrator/' . $id . '/erase";'])
        }}
    @endif
</div>
<!-- /.box-footer -->