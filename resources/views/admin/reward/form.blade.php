<div class="box-body">
    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="table-responsive">
                <table id="configs" class="table" style="margin-bottom: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 25%; padding-left: 0px;">เงินรางวัล</th>
                            <th style="width: 25%; padding-left: 0px;">จำนวน</th>
                            <th style="width: 25%; padding-left: 0px;">ลงทะเบียน</th>
                            <th style="width: 25%; padding-left: 0px;">รางวัลพิเศษ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$edit)
                            @if (!is_null(old('configs')))
                                @foreach(old('configs') as $key => $config)
                                    @include('admin.reward.config', ['key' => $key, 'config' => $config])
                                @endforeach
                            @else
                                @include('admin.reward.config', ['key' => 0, 'config' => null])
                            @endif
                        @else
                            @if (!is_null(old('configs')))
                                @foreach(old('configs') as $key => $config)
                                    @include('admin.reward.config', ['key' => $key, 'config' => $config])
                                @endforeach
                            @else
                                @foreach($reward->rewardConfigs as $key => $config)
                                    @include('admin.reward.config', ['key' => $key, 'config' => $config])
                                @endforeach
                            @endif
                        @endif
                    </tbody>
                </table>  
            </div>

            {{ Form::button('<i class="fa fa-plus-circle"></i> เพิ่มรางวัล', [
                'id'=>'add_config',
                'class'=>'btn btn-default btn-flat', 
                'disabled'=>true])
            }}
            {{ Form::button('<i class="fa fa-minus-circle"></i> ลบรางวัล', [
                'id'=>'delete_config',
                'class'=>'btn btn-default btn-flat', 
                'disabled'=>true])
            }}   

            <span>* ใช้เพื่อเพิ่มช่องป้อนรางวัล สำหรับกรณีที่มีหลายรางวัล ถ้ามีรางวัลเดียวไม่ต้องกด</span>
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