<div class="box">
    <div class="box-body">
        <form>
            <div class="form-group">
                <label for="filter_year">สรุปข้อมูลปี</label>
                <select id="filter_year" name="filter_year" class="form-control">
                    @for ($year = Diamond::today()->year; $year >= 2018; $year--)
                        <option value="{{ $year }}">พ.ศ. {{ $year + 543 }}</option>
                    @endfor
                </select>
            </div>             
        </form>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->