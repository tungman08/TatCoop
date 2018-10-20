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
                                        <li>{{ $item->profile->fullName }} (ค้ำประกันจำนวน {{ number_format($item->pivot->amount, 2, '.', ',')  }}  บาท)</li>
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
                    onclick="javascript:window.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/edit') }}';">
                    <i class="fa fa-pencil"></i> แก้ไขสัญญา
                </button>

                @if ($loan->loan_type_id == 1 && !$loan->shareholding)
                    <button type="button" class="btn btn-primary btn-flat"
                        onclick="javascript:window.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/sureties/edit') }}';">
                        <i class="fa fa-pencil"></i> แก้ไขผู้ค้ำประกัน
                    </button>
                @endif
            @endif
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
                            onclick="javascript:window.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/create') }}';">
                            <i class="fa fa-plus-circle fa-fw"></i> ชำระเงิน
                        </button>
                    </div>

                    @if ($loan->payments->sum('principle') >= ($loan->outstanding / 10))
                        <div class="btn-group">
                            <button id="create_loan" class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
                                onclick="javascript:window.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/close') }}';">
                                <i class="fa fa-plus-circle fa-fw"></i> ปิดยอดเงินกู้
                            </button>
                        </div>

                        <div class="btn-group pull-right">
                            <button id="calculate_payment" class="btn btn-default btn-flat" style="margin-bottom: 15px;"
                                onclick="javascript:window.location.href = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/calculate') }}';">
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
                                <th style="width: 30%;">วันที่ชำระ</th>
                                <th style="width: 20%;">เงินต้น</th>
                                <th style="width: 20%;">ดอกเบี้ย</th>
                                <th style="width: 20%;">รวม</th>
                            </tr>
                        </thead>
                        <tboby>
                            @php($count = 0)
                            @foreach($loan->payments->sortByDesc('pay_date') as $payment)
                                <tr onclick="javascript: document.location = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/' . $payment->id) }}';" style="cursor: pointer;">
                                    <td>{{ ++$count }}.</td>
                                    <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i> {{ Diamond::parse($payment->pay_date)->thai_format('d M Y') }}</td>
                                    <td>{{ number_format($payment->principle, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($payment->interest, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($payment->principle + $payment->interest, 2, '.', ',') }} บาท</td>
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
            "iDisplayLength": 25
        });   
    });   
    </script>  
@endsection