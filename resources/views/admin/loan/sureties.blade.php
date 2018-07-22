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
            ['item' => 'สัญญากู้ยืม', 'link' => 'service/' . $member->id . '/loan/' . $loan->id],
            ['item' => 'ผู้ค้ำประกัน', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดสัญญากู้ยืมเลขที่ {{ $loan->code }}</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ $member->profile->fullName }}</td>
                    </tr>
                    <tr>
                        <th>ประเภทเงินกู้:</th>
                        <td>{{ $loan->loanType->name }}</td>
                    </tr>  
                    <tr>
                        <th>วงเงินที่กู้:</th>
                        <td>{{ number_format($loan->outstanding, 2, '.', ',') }} บาท</td>
                    </tr>  
                    <tr>
                        <th>จำนวนงวดผ่อนชำระ:</th>
                        <td>{{ number_format($loan->period, 0, '.', ',') }} งวด (ชำระงวดละ {{ number_format(LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period), 2, '.', ',') }} บาท)</td>
                    </tr> 
                    <tr>
                        <th>เงินต้นคงเหลือ:</th>
                        <td>{{ number_format($loan->outstanding - $loan->payments->sum('principle'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>ดอกเบี้ยสะสม:</th>
                        <td>{{ number_format($loan->payments->sum('interest'), 2, '.', ',') }} บาท</td>
                    </tr>
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

       <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-users"></i> แก้ไขผู้ค้ำประกัน</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                {{ Form::hidden('loan_id', $loan->id, ['id' => 'loan_id']) }}
                {{ Form::hidden('member_id', $member->id, ['id' => 'member_id']) }}

                <div class="form-group">
                    {{ Form::label('check_surety_id', 'รหัสสมาชิกของผู้ค้ำ (ถ้าผู้กู้ต้องการใช้หุ้นตัวของตนเองค้ำ ให้ใส่รหัสสมาชิกของผู้กู้)', [
                        'class'=>'control-label']) 
                    }}
                    <div class="input-group">
                        {{ Form::text('check_surety_id', null, [
                            'id' => 'check_surety_id',
                            'placeholder' => 'รหัสสมาชิก 5 หลัก',
                            'data-inputmask' => "'mask': '99999','placeholder': '0','autoUnmask': true,'removeMaskOnSubmit': true",
                            'data-mask',
                            'autocomplete'=>'off',
                            'class'=>'form-control'])
                        }}
                        <span class="input-group-btn">
                            {{ Form::button('<i class="fa fa-plus-circle"></i> เพิ่ม', [
                                'id' => 'check_surety',
                                'class'=>'btn btn-default btn-flat'])
                            }}
                        </span>
                    </div>
                </div>

                <div id="sureties" class="form-group">
                    @foreach($loan->sureties as $surety)
                        <div id="surety_{{ $surety->id }}" class="box box-primary" style="border-left: 1px solid #d2d6de; border-right: 1px solid #d2d6de;">
                            <div class="box-header with-border">
                                <h4 class="box-title" style="font-size: 14px; font-weight: 700;">ผู้ค้ำประกัน</h4>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" onclick="javascript:var result = confirm('คุณต้องการลบผู้ค้ำประกันรายนี้ใช่ไหม?'); if (result) { removeSurety({{ $loan->id }}, {{ $surety->id }}); }"><i class="fa fa-times"></i></button>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ $surety->profile->fullName }}

                                        @if ($surety->pivot->yourself)
                                            @php($available = LoanCalculator::shareholding_available($surety))
                                            <span>(ค้ำประกันด้วยหุ้นตนเอง เหลือหุ้นที่สามาถค้ำประกันได้ {{ ($available > 1200000) ? number_format(1200000, 2, '.', ',') : number_format($available, 2, '.', ',') }} บาท)</span>
                                        @else
                                            <span>(ค้ำประกันด้วยเงินเดือน)</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <span>{{ number_format($surety->pivot->amount, 2, '.', ',') }} บาท</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    @endforeach
                </div>
            </div>
            <!-- /.box-body -->
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
                        {{ Form::label('surety_salary', 'เงินเดือนปัจจุบันของผู้ค้ำประกัน', [
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
@endsection

@section('scripts')
    @parent

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $("[data-mask]").inputmask();

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
    });
    
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
                        $('#surety_title').html("สมาชิกหมายเลข "  + surety.memberCode + " (" + surety.fullName + ") ค้ำประกันด้วยหุ้นตนเอง");
                        $('#yourself').val(true);
                        $('.salary').hide();
                    }
                    else if (!surety.employee) {
                        $('#surety_title').html("สมาชิกหมายเลข "  + surety.memberCode + " (" + surety.fullName + ") บุคคลภายนอกใช้หุ้นค้ำ");
                        $('.salary').hide();
                    }
                    else {
                        $('#surety_title').html("สมาชิกหมายเลข "  + surety.memberCode + " (" + surety.fullName + ")");
                        $('.salary').show();
                    }

                    $('#surety_loan_id').val(surety.loan_id);
                    $('#surety_id').val(surety.id);
                    $('#addSurety').modal('show');
                }
                else {
                    alert(surety.message);
                }    
            }
        });
    }
   
    $('#add-surety').click(function () {
        var loan_id = parseInt($('#surety_loan_id').val(), 10);
        var surety_id = parseInt($('#surety_id').val(), 10);
        var salary = parseInt($('#surety_salary').val(), 10);
        var netSalary = parseInt($('#surety_net_salary').val(), 10);
        var amount = parseInt($('#surety_amount').val(), 10);
        var yourself = $('#yourself').val();

        if (yourself && !amount || !yourself && (!salary || !netSalary || !amount)) {
            alert('กรุณาป้อนข้อมูลในครบก่อนก่อน');
        }
        else {
            addSurety(loan_id, surety_id, salary, netSalary, amount);
            $('#addSurety').modal('hide');
        }
    });

    function addSurety(loan_id, member_id, salary, netSalary, amount) {
        var formData = new FormData();
            formData.append('loan_id', loan_id);
            formData.append('member_id', member_id);
            formData.append('salary', salary);
            formData.append('netSalary', netSalary);
            formData.append('amount', amount);

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

                if (result.id > 0) {
                    var surety = '<div id="surety_' + result.id + '" class="box box-primary" style="border-left: 1px solid #d2d6de; border-right: 1px solid #d2d6de;">';
                        surety += '<div class="box-header with-border">';
                        surety += '<h4 class="box-title" style="font-size: 14px; font-weight: 700;">ผู้ค้ำประกัน</h4>';
                        surety += '<div class="box-tools pull-right">';
                        surety += '<button type="button" class="btn btn-box-tool" onclick="javascript:var result = confirm(\'คุณต้องการลบผู้ค้ำประกันรายนี้ใช่ไหม?\'); if (result) { removeSurety(' + result.loan_id + ', ' + result.id + '); }"><i class="fa fa-times"></i></button>';
                        surety += '</div>';
                        surety += '</div>';
                        surety += '<div class="box-body">';
                        surety += '<div class="row">';
                        surety += '<div class="col-md-6">' + result.name;
                        
                        if (result.yourself) {
                            surety += ' <span>(ค้ำประกันด้วยหุ้นตนเอง)</span>';
                        }

                        surety += '</div>';
                        surety += '<div class="col-md-6 text-right"><span>' + result.amount + ' บาท (สามาถค้ำประกันได้ ' + result.available + ' บาท)</span></div>';
                        surety += '</div>';
                        surety += '</div>';
                        surety += '</div>';

                    $('#sureties').append(surety);
                }
                else {
                    alert(result.message);
                }
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

        function clearAddSurety() {
        $('#surety_salary').val('');
        $('#surety_net_salary').val('');
        $('#surety_amount').val('');
    }

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode != 8 && charCode != 127 && charCode != 46 && (charCode < 48 || charCode > 57))
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