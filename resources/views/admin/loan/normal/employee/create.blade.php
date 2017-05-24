@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => '/service/loan/member'],
            ['item' => 'การกู้ยืม', 'link' => 'service/' . $member->id . '/loan'],
            ['item' => 'ทำสัญญาเงินกู้', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การทำสัญญาการกู้ยืมของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullName }}</h4>

            @include('admin.loan.info', ['member' => $member])
        </div>

        <div class="box box-default collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card fa-fw"></i> รายละเอียดประเภทสินเชื่อเงินกู้</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-title -->

            <div class="box-body">
                @include('admin.loan.loantype', ['loantype' => $loantype])
            </div>          
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-file-text-o fa-fw"></i> ทำสัญญาเงินกู้ประเภทกู้สามัญ สำหรับพนักงาน/ลูกจ้าง ททท.</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/service/' . $member->id . '/loan/' . $loantype->id . '/create/normal/employee', 'method' => 'post', 'class' => 'form-horizontal']) }}
                {{ Form::hidden('member_id', $member->id, ['id' => 'member_id']) }}
                {{ Form::hidden('loan_id', $loan_id, [ 'id' => 'loan_id' ]) }}

                @include('admin.loan.normal.employee.form')
            {{ Form::close() }}
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>

    <!-- Modal -->
    <div id="addSurety" class="modal fade" role="dialog">
        <form id="addSuretyForm" name="addSuretyForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="ปิด">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 id="surety_title" class="modal-title">Default Modal</h4>
                </div>
                <div class="modal-body">
                    {{ Form::hidden('surety_loan_id', null, [ 'id' => 'surety_loan_id' ]) }}
                    {{ Form::hidden('surety_id', null, [ 'id' => 'surety_id' ]) }}
                    {{ Form::hidden('yourself', null, [ 'id' => 'yourself' ]) }}
                    <div class="form-group salary">
                        {{ Form::label('surety_salary', 'เงินเดือนของผู้ค้ำประกัน', [
                            'class'=>'control-label']) 
                        }}
                        {{ Form::text('surety_salary', null, [
                            'id' => 'surety_salary',
                            'required' => true,
                            'min' => 1,
                            'placeholder' => 'ตัวอย่าง: 50000',
                            'autocomplete'=>'off',
                            'onkeypress' => 'javascript:return isNumberKey(event);',
                            'class'=>'form-control'])
                        }}
                    </div>

                    <div class="form-group salary">
                        {{ Form::label('surety_net_salary', 'เงินเดือนสุทธิของผู้ค้ำประกันหักทุกอย่างใน slip', [
                            'class'=>'control-label']) 
                        }}
                        {{ Form::text('surety_net_salary', null, [
                            'id' => 'surety_net_salary',
                            'required' => true,
                            'min' => 1,
                            'placeholder' => 'ตัวอย่าง: 20000',
                            'autocomplete'=>'off',
                            'onkeypress' => 'javascript:return isNumberKey(event);',
                            'class'=>'form-control'])
                        }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('surety_amount', 'จำนวนเงินที่ต้องการค้ำประกัน', [
                            'class'=>'control-label']) 
                        }}
                        {{ Form::text('surety_amount', null, [
                            'id' => 'surety_amount',
                            'required' => true,
                            'min' => 1,
                            'placeholder' => 'ตัวอย่าง: 20000',
                            'autocomplete'=>'off',
                            'onkeypress' => 'javascript:return isNumberKey(event);',
                            'class'=>'form-control'])
                        }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="add-surety" class="btn btn-flat btn-primary"><i class="fa fa-plus-circle"></i> เพิ่ม</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
        </from>
    </div>
    <!-- /.modal -->
@endsection

@section('styles')
    @parent

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <!-- Bootstrap DataTable JavaScript -->
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <!-- Wizard Step CSS -->
    {!! Html::style(elixir('css/stepwizard.css')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $("[data-mask]").inputmask();
        //$('form').submit(function() {
        //    $("[data-mask]").inputmask('remove');
        //});

        $('#check_surety').click(function () {
            var member_id = parseInt($('#member_id').val(), 10);
            var loan_id = parseInt($('#loan_id').val(), 10);
            var surety_id = parseInt($('#check_surety_id').val(), 10);

            if (!surety_id) {
                alert('กรุณาป้อนรหัสสมาชิกของผู้ค้ำประกันก่อน');
            }
            else {
                checkSurety(member_id, loan_id, surety_id);
                $('#check_surety_id').val('');
            }
        });

        $('#add-surety').click(function () {
            var loan_id = parseInt($('#surety_loan_id').val(), 10);
            var surety_id = parseInt($('#surety_id').val(), 10);
            var salary = parseInt($('#surety_salary').val(), 10);
            var netSalary = parseInt($('#surety_net_salary').val(), 10);
            var amount = parseInt($('#surety_amount').val(), 10);
            var yourself = (parseInt($('#yourself').val(), 10) == 1) ? true : false;

            if (yourself && !amount || !yourself && (!salary || !netSalary || !amount)) {
                alert('กรุณาป้อนข้อมูลในครบก่อนก่อน');
            }
            else {
                addSurety(loan_id, surety_id, salary, netSalary, amount, yourself);
                $('#addSurety').modal('hide');
            }
        });

        if ($('#dataTables-loan').length) {
            var loan = $('#dataTables-loan').DataTable({
                "searching": false,
                "ordering": false,
                "bLengthChange": false,
                "responsive": true,
                "iDisplayLength": 10,
                "createdRow": function(row, data, index) {
                    $(row).find('td:eq(1)').addClass('text-primary');
                },
            });

            calculate(loan, $('#loan_id').val());
        }
    });

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }     

    function checkSurety(member_id, loan_id, surety_id) {
        var formData = new FormData();
            formData.append('member_id', member_id);
            formData.append('loan_id', loan_id);
            formData.append('surety_id', surety_id);

        $.ajax({
            dataType: 'json',
            url: '/ajax/checksurety',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            beforeSend: function() {
                $(".ajax-loading").css("display", "block");
            },
            success: function(surety) {
                $(".ajax-loading").css("display", "none");
                clearAddSurety();

                if (surety.id > 0) {
                    if (surety.yourself) {
                        $('.salary').hide();
                        $('#surety_title').html("สมาชิกหมายเลข "  + surety.memberCode + " (" + surety.fullName + ") ค้ำประกันด้วยหุ้นตนเอง");
                        $('#surety_loan_id').val(surety.loan_id);
                        $('#surety_id').val(surety.id);
                        $('#yourself').val(surety.yourself ? '1' : '0');
                        $('#addSurety').modal('show');
                    }
                    else {
                        $('.salary').show();
                        $('#surety_title').html("สมาชิกหมายเลข "  + surety.memberCode + " (" + surety.fullName + ")");
                        $('#surety_loan_id').val(surety.loan_id);
                        $('#surety_id').val(surety.id);
                        $('#yourself').val(surety.yourself ? '1' : '0');
                        $('#addSurety').modal('show');
                    }
                }
                else {
                    alert(surety.message);
                }    
            }
        });
    }

    function addSurety(loan_id, member_id, salary, netSalary, amount, yourself) {
        var formData = new FormData();
            formData.append('loan_id', loan_id);
            formData.append('member_id', member_id);
            formData.append('salary', salary);
            formData.append('netSalary', netSalary);
            formData.append('amount', amount);
            formData.append('yourself', yourself);

        $.ajax({
            dataType: 'json',
            url: '/ajax/addsurety',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            beforeSend: function() {
                $(".ajax-loading").css("display", "block");
            },
            success: function(result) {
                $(".ajax-loading").css("display", "none");

                var surety = '<div id="surety_' + result.id + '" class="box box-primary" style="border-left: 1px solid #d2d6de; border-right: 1px solid #d2d6de;">';
                    surety += '<div class="box-header with-border">';
                    surety += '<h4 class="box-title" style="font-size: 14px; font-weight: 700;">ผู้ค้ำประกัน</h4>';
                    surety += '<div class="box-tools pull-right">';
                    surety += '<button type="button" class="btn btn-box-tool" onclick="javascript:var result = confirm(\'คุณต้องการลบผู้ค้ำประกันรายนี้ใช่ไหม?\'); if (result) { removeSurety(' + result.loan_id + ', ' + result.id + '); }"><i class="fa fa-times"></i></button>';
                    surety += '</div>';
                    surety += '</div>';
                    surety += '<div class="box-body">';
                    surety += '<div class="row">';
                    surety += '<div class="col-md-3">' + result.name + '</div>';
                    surety += '<div class="col-md-3 text-right">' + result.amount + ' บาท</div>';
                    surety += '</div>';
                    surety += '</div>';
                    surety += '</div>';

                $('#sureties').append(surety);
            }
        });
    }

    function removeSurety(loan_id, member_id) {
        var formData = new FormData();
            formData.append('loan_id', loan_id);
            formData.append('member_id', member_id);

        $.ajax({
            dataType: 'json',
            url: '/ajax/removesurety',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            beforeSend: function() {
                $(".ajax-loading").css("display", "block");
            },
            success: function(id) {
                $(".ajax-loading").css("display", "none");
                $('#surety_' + id).remove();
            }
        });
    }

    function calculate(tb, loan_id) {
        var formData = new FormData();
            formData.append('loan_id', loan_id);

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
                tb.clear().draw();

                if (data.info.payment_type == 1) {
                    $.each(data.payment, function(index, value) {
                        tb.row.add([
                            value.month,
                            number_format(value.pay, 2),
                            number_format(value.interest, 2),
                            number_format(value.principle, 2),
                            number_format(value.balance, 2)
                        ]).draw(false);
                    });
                }
                else {
                    $.each(data.payment, function(index, value) {
                        tb.row.add([
                            value.month,
                            number_format(value.pay + value.addon, 2) + ((value.addon > 0) ? 
                            ' <span class="text-muted" style="cursor: pointer;" data-tooltip="true" title="ปัดเศษ (' + number_format(value.pay, 2) + '+' + number_format(value.addon, 2) + ') ซึ่งจะนำไปบวกกับเงินต้นที่ชำระ"><i class="fa fa-info-circle"></i>' : ''),
                            number_format(value.interest, 2),
                            (value.addon > 0) ? number_format(value.principle, 2) + ' <span class="text-muted">+' + number_format(value.addon, 2) + '</span>' : number_format(value.principle, 2),
                            number_format(value.balance, 2)
                        ]).draw(false);
                    });
                }

                $('#rate').html(data.info.rate + '%');
                $('#total_pay').html(number_format(data.info.total.total_pay, 2) + ' บาท');
                $('#total_interest').html(number_format(data.info.total.total_interest, 2) + ' บาท');

                $('[data-tooltip="true"]').tooltip(); 
            }
        });
    }

    function clearAddSurety() {
        $('#surety_salary').val('');
        $('#surety_net_salary').val('');
        $('#surety_amount').val('');
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