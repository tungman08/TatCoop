<tr>
    <td style="padding-left: 0px;">
        {{ Form::text('rewardConfigs[' . $key . '][price]', null, [
            'placeholder'=>'ตัวอย่าง: 1000', 
            'autocomplete'=>'off',
            'onkeyup'=>'javascript:check_configs();',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control configs'])
        }}     
    </td>
    <td style="padding-left: 0px;">
        {{ Form::text('rewardConfigs[' . $key . '][amount]', null, [
            'placeholder'=>'ตัวอย่าง: 100', 
            'autocomplete'=>'off',
            'onkeyup'=>'javascript:check_configs();',
            'onkeypress' => 'javascript:return isNumberKey(event);',
            'class'=>'form-control configs'])
        }}   
    </td>
    <td style="padding-left: 0px; text-align:center; vertical-align: middle;">
        {{ Form::checkbox('rewardConfigs[' . $key . '][register]', (1 or true), null) }}  
    </td>
    <td style="padding-left: 0px; text-align:center; vertical-align: middle;">
        {{ Form::checkbox('rewardConfigs[' . $key . '][special]', (1 or true), null) }}  
    </td>
</tr>