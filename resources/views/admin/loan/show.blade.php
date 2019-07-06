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
            ['item' => 'การกู้ยืม', 'link' => action('Admin\LoanController@index', ['member_id'=>$member->id])],
            ['item' => 'สัญญากู้ยืม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <div class="row">
                <div class="col-md-8">
                    <h4>
                        รายละเอียดสัญญากู้ยืมเลขที่ {{ $loan->code }}
                        @if(!is_null($loan->completed_at))
                            <span class="label label-success pull-right">ปิดยอดแล้ว เมื่อ {{ Diamond::parse($loan->completed_at)->thai_format('j M Y') }}</span>
                        @endif
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-info">
                            <tr>
                                <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                                <td>{{ $member->profile->fullname }}</td>
                            </tr>
                            <tr>
                                <th>ประเภทเงินกู้:</th>
                                <td><span class="label label-primary">{{ $loan->loanType->name }}<span></td>
                            </tr>  
                            <tr>
                                <th>วันที่กู้:</th>
                                <td>{{ Diamond::parse($loan->loaned_at)->thai_format('j F Y') }}</td>
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
                                <td>{{ number_format(round($loan->outstanding - $loan->payments->sum('principle'), 2), 2, '.', ',') }} บาท</td>
                            </tr>
                            <tr>
                                <th>ดอกเบี้ยสะสม:</th>
                                <td>{{ number_format($loan->payments->sum('interest'), 2, '.', ',') }} บาท</td>
                            </tr>
                            
                            @if ($loan->loan_type_id == 1)
                                <tr>
                                    <th>ผู้ค้ำประกัน:</th>
                                    <td>
                                        <ul class="list-info">
                                            @foreach($loan->sureties as $item)
                                                <li>{{ $item->profile->fullname }} (ค้ำประกันจำนวน {{ number_format($item->pivot->amount, 2, '.', ',')  }}  บาท)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        </table>
                        <!-- /.table -->
                    </div>  
                    <!-- /.table-responsive --> 

                    @if (is_null($loan->completed_at))
                        <button type="button" class="btn btn-primary btn-flat"
                            {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                            onclick="javascript:document.location.href = '{{ action('Admin\LoanController@edit', ['member_id'=>$member->id, 'id'=>$loan->id]) }}';">
                            <i class="fa fa-pencil"></i> แก้ไขสัญญา
                        </button>

                        @if ($loan->loan_type_id == 1 && !$loan->shareholding)
                            <button type="button" class="btn btn-primary btn-flat"
                                {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                                onclick="javascript:document.location.href = '{{ action('Admin\LoanController@getEditSureties', ['member_id'=>$member->id, 'id'=>$loan->id]) }}';">
                                <i class="fa fa-pencil"></i> แก้ไขผู้ค้ำประกัน
                            </button>
                        @endif
                    @endif
                </div>
                <!-- /.col -->

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>
                                <i class="fa fa-paperclip"></i> เอกสารแนบ
                            </h4>
                        </div>
                        <!-- /.col -->

                        <div class="col-md-6">
                            @if (is_null($loan->completed_at))
                                <button type="button" id="add_attachment" class="btn btn-primary btn-xs btn-flat pull-right" {{ (($is_super || $is_admin) ? '' : 'disabled') }}>
                                    <i class="fa fa-plus-circle"></i>
                                </button>
                            @endif
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="table-responsive">
                        <table id="loan_attachments" class="table table-info">
                            <tbody>
                            </tbody>
                        </table>
                        <!-- /.table -->
                    </div>  
                    <!-- /.table-responsive --> 
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>

        @if(Session::has('flash_message'))
            <div class="callout {{ Session::get('callout_class') }}">
                <h4>แจ้งข้อความ!</h4>
                <p>
                    {{ Session::get('flash_message') }}

                    @if(Session::has('flash_link'))
                        <a href="{{ Session::get('flash_link') }}">Undo</a>
                    @endif
                </p>
            </div>
        @endif

       <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card"></i> รายละเอียดผ่อนชำระ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                @if (is_null($loan->completed_at))
                    <div class="btn-group">
                        <button id="create_loan" class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
                            {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                            onclick="javascript:document.location.href = '{{ action('Admin\PaymentController@create', ['loan_id'=>$loan->id]) }}';">
                            <i class="fa fa-plus-circle fa-fw"></i> ชำระเงิน
                        </button>
                    </div>

                    @if ($loan->payments->sum('principle') >= ($loan->outstanding / 10))
                        <div class="btn-group">
                            <button id="create_loan" class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
                                {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                                onclick="javascript:document.location.href = '{{ action('Admin\PaymentController@getClose', ['loan_id'=>$loan->id]) }}';">
                                <i class="fa fa-plus-circle fa-fw"></i> ปิดยอดเงินกู้
                            </button>
                        </div>

                        <div class="btn-group pull-right">
                            <button id="calculate_payment" class="btn btn-default btn-flat" style="margin-bottom: 15px;"
                                onclick="javascript:document.location.href = '{{ action('Admin\PaymentController@getCalculate', ['loan_id'=>$loan->id]) }}';">
                                <i class="fa fa-calculator fa-fw"></i> คำนวณยอดเงินที่ต้องการปิดยอดเงินกู้
                            </button>
                        </div>
                    @endif
                @endif

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-payment" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 10%;">งวดที่</th>
                                <th style="width: 15%;">วันที่ชำระ</th>
                                <th style="width: 15%;">เงินต้น</th>
                                <th style="width: 15%;">ดอกเบี้ย</th>
                                <th style="width: 15%;">รวม</th>
                                <th style="width: 15%;">หมายเหตุ</th>
                                <th style="width: 5%;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $index => $payment)
                                <tr onclick="javascript: document.location.href  = '{{ action('Admin\PaymentController@show', ['loan_id'=>$loan->id, 'id'=>$payment->id]) }}';" style="cursor: pointer;">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i> งวดที่ {{ $payment->period }}</td>
                                    <td>{{ Diamond::parse($payment->pay_date)->thai_format('d M Y') }}</td>
                                    <td>{{ number_format($payment->principle, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($payment->interest, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($payment->principle + $payment->interest, 2, '.', ',') }} บาท</td>
                                    <td>{{ $payment->remark }}</td>
									<td>{!! ($payment->attachments->count() > 0) ? '<i class="fa fa-paperclip"></i>' : '&nbsp;' !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- /.table -->
                </div>
                <!-- /.table-responsive -->
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

    <!-- Attachment Modal -->
    <div id="attachmentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">เอกสารแนบ</h4>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" id="loan_id" value="{{ $loan->id }}" />

                    <input type="file" id="attachment" accept="application/pdf" />

                    <button id="btn_attachment" class="btn btn-primary btn-flat margin-t-lg margin-b-lg">
                        <i class="fa fa-save"></i> บันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DataTable JavaScript -->
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('[data-tooltip="true"]').tooltip();

        $('#dataTables-payment').dataTable({
            "iDisplayLength": 25,
            "columnDefs": [
                { type: 'formatted-num', targets: 0 },
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 },
                { type: 'formatted-num', targets: 5 }
            ]
        }); 

        show_attachment();

        $('#add_attachment').click(function () {
            $('#attachment').val('');
            $('#attachmentModal').modal('show');
        });  

        $('#btn_attachment').click(function () {
            if ($('#attachment').get(0).files.length === 0) {
                alert('กรุณาเลือกเอกสาร!!');
            }
            else {
                $('#attachmentModal').modal('hide');

                upload_attachment();
            }
        });
    }); 

    function show_attachment() {
        $.ajax({
            url: '/service/loan/showfiles',
            type: "post",
            data: {
                'id': $('#loan_id').val()
            },
            success: function(files) {
                let target = $('#loan_attachments > tbody');
                target.empty();

                if (files.length > 0) {
                    $.each(files, function(index, value) {
                        let str = '<tr>';
                        str += "<td><a href=\"https://www.tatcoop.com/storage/file/loans/" + value.file + "\" target=\"_blank\">";
                        str += "<i class=\"fa fa-paperclip\"></i> " + value.display + "</a></td>";

                        @if ($is_super || $is_admin)
                            str += "<td class=\"text-right\"><a class=\"text-danger\" href=\"javascript: void();\" ";
                            str += "onclick=\"javascript: var result = confirm('คุณต้องการลบเอกสารนี้ใช่ไหม?'); if (result) { delete_attachment(" + value.id + ") }\">";
                            str += "<i class=\"fa fa-times-circle\"></i></a></td>"
                        @endif

                        str += '</tr>';

                        target.append(str);
                    });
                }
                else {
                    target.append("<tr><td class=\"text-center\">=== ไม่มีเอกสารแนบ ===</td></tr>");
                }
            }
        });
    }

    function upload_attachment() {
        var file = $('#attachment').get(0).files[0];
        var loan_id = $('#loan_id').val();

        var formData = new FormData();
        formData.append('file', file);
        formData.append('loan_id', loan_id);
 
        if(file.size < 20971520) {
            $.ajax({
                dataType: 'json',
                url: '/service/loan/uploadfile',
                type: 'post',
                cache: false,
                data: formData,
                processData: false,
                contentType: false,
                error: function (xhr, ajaxOption, thrownError) {
                    alert('Upload failed');
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function () {
                    show_attachment();
                }
            });
        }
        else {
            alert("File is to big");
        }
    }

    function delete_attachment(attachment_id) {
        $.ajax({
            dataType: 'json',
            url: '/service/loan/deletefile',
            type: 'post',
            cache: false,
            data: {
                'id': attachment_id
            },
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function() {
                show_attachment();
            }
        });
    }

    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "formatted-num-pre": function ( a ) {
            a = (a === "-" || a === "") ? 0 : a.replace(/[^\d\-\.]/g, "");
            return parseFloat( a );
        },

        "formatted-num-asc": function ( a, b ) {
            return a - b;
        },

        "formatted-num-desc": function ( a, b ) {
            return b - a;
        }
    });  
    </script>  
@endsection