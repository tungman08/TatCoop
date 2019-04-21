@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการประเภทเงินกู้
            <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลประเภทเงินกู้', 'link' => '/database/loantype'],
            ['item' => 'ประเภทเงินกู้', 'link' => '/database/loantype/' . $loantype->id],
            ['item' => 'แก้ไขประเภทสัญญา', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการประเภทเงินกู้ของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของสหกรณ์</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <!-- Horizontal Form -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">เพิ่มประเภทเงินกู้</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::model($loantype, ['route' => ['database.loantype.update', $loantype->id], 'method' => 'put', 'class' => 'form-horizontal']) }}
                @include('admin.loantype.form', ['edit' => true])
            {{ Form::close() }}
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('styles')

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <script>
        $(document).ready(function() {
            $('#add_limit').prop("disabled", false);
            $('#delete_limit').prop("disabled", $('#limits tbody tr').length == 1);

            $('#start_date').datetimepicker({
                locale: moment.locale('th'),
                viewMode: 'days',
                format: 'YYYY-MM-DD',
                useCurrent: false,
                focusOnShow: false,
                buddhismEra: true
            });

            $('#expire_date').datetimepicker({
                locale: moment.locale('th'),
                viewMode: 'days',
                format: 'YYYY-MM-DD',   
                useCurrent: false,
                focusOnShow: false,
                buddhismEra: true
            });

            $('#add_limit').click(function () {
                var childs = $('#limits tbody tr').length;
                var begin = parseInt($('#limits tbody tr:last-child td:eq(1) input:text').val(), 10) + 1;

                var row = '<tr>';
                    row += '<td style="padding-left: 0px;">';
                    row += '<input name="limits[' + childs + '][cash_begin]" placeholder="ตัวอย่าง: 1" ';
                    row += 'class="form-control limits" value="' + begin + '" type="text" onkeyup="javascript:check_limits();" ';
                    row += 'onkeypress="javascript:return isNumberKey(event);" autocomplete="off" readonly>';
                    row += '</td>';
                    row += '<td style="padding-left: 0px;">';
                    row += '<input name="limits[' + childs + '][cash_end]" placeholder="ตัวอย่าง: 1000000" ';
                    row += 'class="form-control limits" type="text" onkeyup="javascript:check_limits_sp(this);" ';
                    row += 'onkeypress="javascript:return isNumberKey(event);" autocomplete="off">';   
                    row += '</td>';
                    row += '<td style="padding-left: 0px;">';
                    row += '<input name="limits[' + childs + '][shareholding]" placeholder="ตัวอย่าง: 25 (กรณีไม่ต้องใช้หุ้นให้ใส่ 0)" ';
                    row += 'class="form-control limits" type="text" onkeyup="javascript:check_limits();" ';
                    row += 'onkeypress="javascript:return isNumberKey(event);" autocomplete="off">';  
                    row += '</td>';
                    row += '<td style="padding-left: 0px;">';
                    row += '<input name="limits[' + childs + '][surety]" placeholder="ตัวอย่าง: 1-2 (กรณีไม่ต้องใช้ผู้ค้ำให้ใส่ 0)" ';
                    row += 'class="form-control limits" type="text" onkeyup="javascript:check_limits();" autocomplete="off">';  
                    row += '</td>';
                    row += '<td style="padding-left: 0px;">';
                    row += '<input name="limits[' + childs + '][period]" placeholder="ตัวอย่าง: 36" ';
                    row += 'class="form-control limits" type="text" onkeyup="javascript:check_limits();" ';
                    row += 'onkeypress="javascript:return isNumberKey(event);" autocomplete="off">';
                    row += '</td>';                    
                    row += '</tr>';

                $('#limits tbody').append(row);
                $(this).prop("disabled", true);
                childs = $('#limits tbody tr').length;

                if (childs > 1) {
                    $('#delete_limit').prop("disabled", false);
                }
            });

            $('#delete_limit').click(function () {
                $('#limits tbody tr:last-child').remove();
                childs = $('#limits tbody tr').length;
                check_limits(false);

                if (childs < 2) {
                    $('#delete_limit').prop("disabled", true);
                }
            });
        });

        function check_limits() {
            var empty = 0;

            $('.limits').each(function () {
                if (this.value == "") {
                    empty++;
                } 
            });

            $('#add_limit').prop("disabled", (empty != 0));
        }

        function check_limits_sp(element,) {
            var tr = $(element).closest('tr');

            if (!tr.is(':last-child')) {
                var cash = parseInt(tr.children().eq(1).children('input:text').val(), 10);
                    cash = (!isNaN(cash)) ? cash : 0; 
                    
                tr.next().children().eq(0).children('input:text').val(cash + 1);
            }

            check_limits();
        }

        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 8 && charCode != 127&& charCode != 45 && charCode != 46 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>
@endsection