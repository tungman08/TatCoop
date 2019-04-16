<div class="box">
    <div class="box-body">
        <div class="form-group">
            <label for="datepicker">สรุปข้อมูลปี</label>
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                <input type="text" id="datepicker" class="form-control" value="{{ Diamond::today()->format('Y') }}" />
            </div>
        </div>
        <p class="help-block">กรุณาเลือกปีที่ต้องการแสดงข้อมูล</p>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->