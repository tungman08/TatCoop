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
                            onclick="javascript:document.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/edit') }}';">
                            <i class="fa fa-pencil"></i> แก้ไขสัญญา
                        </button>

                        @if ($loan->loan_type_id == 1 && !$loan->shareholding)
                            <button type="button" class="btn btn-primary btn-flat"
                                {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                                onclick="javascript:document.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/sureties/edit') }}';">
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
                        <table id="loan_attachments" class="table">
                            <tbody>
                                @forelse ($loan->attachments as $attachment)
                                    <tr>
                                        @if ($is_super || $is_admin)
                                            <td>{{ $attachment->display }}</td>
                                        @else
                                            <td>{{ $attachment->display }}</td>
                                        @endif
                                    </tr>                    
                                @empty
                                    <tr>
                                        <td class="text-center">=== ไม่มีเอกสารแนบ ===</td>
                                    </tr> 
                                @endforelse
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
                            onclick="javascript:document.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/create') }}';">
                            <i class="fa fa-plus-circle fa-fw"></i> ชำระเงิน
                        </button>
                    </div>

                    @if ($loan->payments->sum('principle') >= ($loan->outstanding / 10))
                        <div class="btn-group">
                            <button id="create_loan" class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
                                {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                                onclick="javascript:document.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/close') }}';">
                                <i class="fa fa-plus-circle fa-fw"></i> ปิดยอดเงินกู้
                            </button>
                        </div>

                        <div class="btn-group pull-right">
                            <button id="calculate_payment" class="btn btn-default btn-flat" style="margin-bottom: 15px;"
                                onclick="javascript:document.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/calculate') }}';">
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
                                <th style="width: 17%;">งวดที่</th>
                                <th style="width: 17%;">วันที่ชำระ</th>
                                <th style="width: 17%;">เงินต้น</th>
                                <th style="width: 17%;">ดอกเบี้ย</th>
                                <th style="width: 17%;">รวม</th>
                                <th style="width: 5%;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $index => $payment)
                                <tr onclick="javascript: document.location.href  = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/' . $payment->id) }}';" style="cursor: pointer;">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i> งวดที่ {{ $payment->period }}</td>
                                    <td>{{ Diamond::parse($payment->pay_date)->thai_format('d M Y') }}</td>
                                    <td>{{ number_format($payment->principle, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($payment->interest, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($payment->principle + $payment->interest, 2, '.', ',') }} บาท</td>
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

                    <button class="btn btn-primary btn-flat margin-t-lg margin-b-lg">
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

        $('#add_attachment').click(function () {
            $('#attachmentModal').modal('show');
        });  
    }); 

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