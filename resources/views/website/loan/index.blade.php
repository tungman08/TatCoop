@extends('website.loan.layout')

@section('content')
    <h3 class="page-header">
        <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="fa fa-home fa-fw"></i></a></li>
            <li class="active">คำนวณสินเชื่อเงินกู้เบื้องต้น</li>
        </ol>
    </h3>

    <div class="panel panel-default">
        <div class="panel-heading">คำนวณดอกเบี้ยและยอดเงินที่ต้องผ่อนชำระ</div>
        <div class="panel-body">
            <div class="row setup-content">
                <div class="form-group">
                    <label for="loan_type" class="control-label">ประเภทการกู้</label>
                    <select id="loan_type" class="form-control">
                        @foreach($loan_types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="outstanding"class="control-label">วงเงินที่ต้องการขอกู้ (บาท)</label>
                    <input id="outstanding" type="text" class="form-control" placeholder="ตัวอย่าง: 100000" required onkeypress="javascript:return isNumberKey(event);">
                </div>
                <div class="form-group">
                    <label for="period"class="control-label">จำนวนงวดการผ่อนชำระ (เดือน)</label>
                    <input id="period" type="text" class="form-control" placeholder="ตัวอย่าง: 12" required onkeypress="javascript:return isNumberKey(event);">
                </div>
                <button id="calculate" class="btn btn-primary"><i class="fa fa-calculator"></i> คำนวณ</button>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            ตารางการผ่อนชำระค่างวดเงินกู้
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#general" data-toggle="tab">ประเภทผ่อนชำระแบบคงยอด</a></li>
                <li><a href="#stable" data-toggle="tab">ประเภทผ่อนชำระแบบคงต้น</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade in active" id="general">
                    <table class="table margin-t-md">
                        <tr>
                            <th style="width:20%; border-top: none;">อัตราดอกเบี้ย:</th>
                            <td id="general_rate" style="border-top: none;">0.0%</td>
                        </tr>
                        <tr>
                            <th>จำนวนที่ต้องชำระทั้งหมด:</th>
                            <td id="total_general_pay">0.00</td>
                        </tr>
                        <tr>
                            <th>จำนวนดอกเบี้ยทั้งหมด:</th>
                            <td id="total_general_interest">0.00</td>
                        </tr>                                                        
                    </table>

                    <table class="table table-striped table-hover" id="dataTables-general">
                        <thead>
                            <tr>
                                <th style="width: 20%;">ลำดับ</th>
                                <th style="width: 20%;">จำนวนเงินที่ต้องชำระ</th>
                                <th style="width: 20%;">เป็นดอกเบี้ย</th>
                                <th style="width: 20%;">เป็นเงินต้น</th>
                                <th style="width: 20%;">เงินต้นคงเหลือ</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane fade" id="stable">
                    <table class="table margin-t-md">
                        <tr>
                            <th style="width:20%; border-top: none;">อัตราดอกเบี้ย:</th>
                            <td id="stable_rate" style="border-top: none;">0.0%</td>
                        </tr>
                        <tr>
                            <th>จำนวนที่ต้องชำระทั้งหมด:</th>
                            <td id="total_stable_pay">0.00</td>
                        </tr>
                        <tr>
                            <th>จำนวนดอกเบี้ยทั้งหมด:</th>
                            <td id="total_stable_interest">0.00</td>
                        </tr>                                                        
                    </table>

                    <table width="100%" class="table table-striped table-hover" id="dataTables-stable">
                        <thead>
                            <tr>
                                <th style="width: 20%;">ลำดับ</th>
                                <th style="width: 20%;">จำนวนเงินที่ต้องชำระ</th>
                                <th style="width: 20%;">เป็นดอกเบี้ย</th>
                                <th style="width: 20%;">เป็นเงินต้น</th>
                                <th style="width: 20%;">เงินต้นคงเหลือ</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <span class="text-danger">
                    <p>* ผลการประเมินจากเครื่องคำนวนสินเชื่อเป็นเพียงการประเมินความสามารถในการกู้เบื้องต้นเท่านั้น การอนุมัติสินเชื่อสงวนสิทธิ์เป็นไปตามหลักเกณฑ์ของสหกรณ์ฯ </p>
                </span>
            </div>
        </div>
<!-- /.panel-body -->
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function () {
            $('[data-tooltip="true"]').tooltip();
            $(".ajax-loading").css("display", "none");

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            var general = $('#dataTables-general').DataTable({
                    "searching": false,
                    "ordering": false,
                    "bLengthChange": false,
                    "responsive": true,
                    "iDisplayLength": 10,
                    "createdRow": function(row, data, index) {
                        $(row).find('td:eq(1)').addClass('text-primary');
                    },
                });

            var stable = $('#dataTables-stable').DataTable({
                    "searching": false,
                    "ordering": false,
                    "bLengthChange": false,
                    "responsive": true,
                    "iDisplayLength": 10,
                    "createdRow": function(row, data, index) {
                        $(row).find('td:eq(1)').addClass('text-primary');
                    },
                });

            $('#calculate').click(function() {
                var curStep = $(this).closest(".setup-content"),
                    curInputs = curStep.find("input[type='text'],input[type='url']"),
                    isValid = true;

                $(".form-group").removeClass("has-error");
                for(var i=0; i<curInputs.length; i++){
                    if (!curInputs[i].validity.valid){
                        isValid = false;
                        $(curInputs[i]).closest(".form-group").addClass("has-error");
                    }
                }

                if (isValid) {
                    calculate(general, stable);
                }
            });
        });

        function calculate(general, stable) {
            var formData = new FormData();
                formData.append('loan_type', $('#loan_type').val());
                formData.append('outstanding', $('#outstanding').val());
                formData.append('period', $('#period').val());

            $.ajax({
                dataType: 'json',
                url: '/ajax/loan',
                type: 'post',
                cache: false,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $(".ajax-loading").css("display", "block");
                },
                complete: function(){
                    $(".ajax-loading").css("display", "none");
                },  
                error: function(xhr, ajaxOption, thrownError) {
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function(data) {
                    general.clear().draw();
                    stable.clear().draw();

                    $.each(data.general, function(index, value) {
                        general.row.add([
                            value.month,
                            number_format(value.pay, 2),
                            number_format(value.interest, 2),
                            number_format(value.principle, 2),
                            number_format(value.balance, 2)
                        ]).draw(false);
                    });

                    $.each(data.stable, function(index, value) {
                        stable.row.add([
                            value.month,
                            number_format(value.pay + value.addon, 2) + ((value.addon > 0) ? 
                            ' <span class="text-muted" style="cursor: pointer;" data-tooltip="true" title="ปัดเศษ (' + number_format(value.pay, 2) + '+' + number_format(value.addon, 2) + ') ซึ่งจะนำไปบวกกับเงินต้นที่ชำระ"><i class="fa fa-info-circle"></i>' : ''),
                            number_format(value.interest, 2),
                            (value.addon > 0) ? number_format(value.principle, 2) + ' <span class="text-muted">+' + number_format(value.addon, 2) + '</span>' : number_format(value.principle, 2),
                            number_format(value.balance, 2)
                        ]).draw(false);
                    });

                    $('#general_rate').html(data.info.rate + '%');
                    $('#total_general_pay').html(number_format(data.info.general.total_pay, 2) + ' บาท');
                    $('#total_general_interest').html(number_format(data.info.general.total_interest, 2) + ' บาท');

                    $('#stable_rate').html(data.info.rate + '%');
                    $('#total_stable_pay').html(number_format(data.info.stable.total_pay, 2) + ' บาท');
                    $('#total_stable_interest').html(number_format(data.info.stable.total_interest, 2) + ' บาท');

                    $('[data-tooltip="true"]').tooltip(); 
                }
            });
        }

        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        function number_format(n, dp){
            var w = n.toFixed(dp), k = w|0, b = n < 0 ? 1 : 0,
                u = Math.abs(w-k), d = (''+u.toFixed(dp)).substr(2, dp),
                s = ''+k, i = s.length, r = '';
            while ( (i-=3) > b ) { r = ',' + s.substr(i, 3) + r; }
            return s.substr(0, i + 3) + r + (d ? '.'+d: '');
        }
    </script>
@endsection