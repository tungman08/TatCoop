<tr>
    <td style="padding-left: 0px;">
        {{ Form::text('limits[' . $key . '][cash_begin]', ($key == 0 && !$edit) ? '1' : null, [
            'readonly'=>true,
            'placeholder'=>'ตัวอย่าง: 1', 
            'onkeyup'=>'javascript:check_limits(' . (($edit) ? 'true' : 'false') . ');',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control limits'])
        }}     
    </td>
    <td style="padding-left: 0px;">
        {{ Form::text('limits[' . $key . '][cash_end]', null, [
            'readonly'=>$edit,
            'placeholder'=>'ตัวอย่าง: 1000000', 
            'autocomplete'=>'off',
            'onkeyup'=>'javascript:check_limits_sp(this, ' . (($edit) ? 'true' : 'false') . ');',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control limits'])
        }}   
    </td>
    <td style="padding-left: 0px;">
        {{ Form::text('limits[' . $key . '][shareholding]', null, [
            'readonly'=>$edit,
            'placeholder'=>'ตัวอย่าง: 25 (กรณีไม่ต้องใช้หุ้นให้ใส่ 0)', 
            'autocomplete'=>'off',
            'onkeyup'=>'javascript:check_limits(' . (($edit) ? 'true' : 'false') . ');',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control limits'])
        }}  
    </td>
    <td style="padding-left: 0px;">
        {{ Form::text('limits[' . $key . '][surety]', null, [
            'readonly'=>$edit,
            'placeholder'=>'ตัวอย่าง: 1-2 (กรณีไม่ต้องใช้ผู้ค้ำให้ใส่ 0)', 
            'autocomplete'=>'off',
            'onkeyup'=>'javascript:check_limits(' . (($edit) ? 'true' : 'false') . ');',
            'class'=>'form-control limits'])
        }}  
    </td>
    <td style="padding-left: 0px;">
        {{ Form::text('limits[' . $key . '][period]', null, [
            'readonly'=>$edit,
            'placeholder'=>'ตัวอย่าง: 36', 
            'autocomplete'=>'off',
            'onkeyup'=>'javascript:check_limits(' . (($edit) ? 'true' : 'false') . ');',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control limits'])
        }}  
    </td>
</tr>