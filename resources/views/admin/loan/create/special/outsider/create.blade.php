@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => action('Admin\LoanController@getMember')],
            ['item' => 'การกู้ยืม', 'link' => action('Admin\LoanController@index', ['member_id'=>$loan->member_id])],
            ['item' => 'ทำสัญญาเงินกู้', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การทำสัญญาการกู้ยืมของ {{ ($loan->member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$loan->member->profile->fullname }}</h4>

            @include('admin.loan.info', ['member' => $loan->member])
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="row">      
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-file-text-o fa-fw"></i> ทำสัญญาเงินกู้ประเภทกู้เฉพาะกิจ</h3>
                    </div>
                    <!-- /.box-header -->

                    <!-- form start -->
                    {{ Form::model($loan, ['action' => ['Admin\SpecialLoanController@getCreateOutsiderLoan', $loan->member_id], 'method' => 'post', 'class' => 'form-horizontal']) }}
                        {{ Form::hidden('id', null, ['id' => 'id']) }}
                        {{ Form::hidden('member_id', null, ['id' => 'member_id']) }}

                        @include('admin.loan.create.special.outsider.form')
                    {{ Form::close() }}
                </div>
                <!-- /.box -->
            </div>
            <!-- ./col -->

            <div class="col-md-4">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-credit-card fa-fw"></i> รายละเอียดประเภทสินเชื่อเงินกู้</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-title -->

                    <div class="box-body">
                        @include('admin.loan.loantype', ['loantype' => App\LoanType::find($loan->loan_type_id)])
                    </div>          
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- ./col -->
        </div>
        <!-- ./row -->

    </section>
    <!-- /.content -->
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

        $('#loaned_at').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: false,
            focusOnShow: false,
            buddhismEra: true
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

            calculate(loan, $('#id').val());
        }
    });

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

    function number_format(n, dp){
        var w = n.toFixed(dp), k = w|0, b = n < 0 ? 1 : 0,
            u = Math.abs(w-k), d = (''+u.toFixed(dp)).substr(2, dp),
            s = ''+k, i = s.length, r = '';
            
        while ( (i-=3) > b ) { r = ',' + s.substr(i, 3) + r; }

        return s.substr(0, i + 3) + r + (d ? '.'+d: '');
    }
    </script>
@endsection