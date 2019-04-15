@extends('website.member.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลการกู้ยืม
        <small>รายละเอียดข้อมูลกู้ยืมของสมาชิก</small>
    </h1>
    @include('website.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'การกู้ยืม', 'link' => '/member/loan'],
        ['item' => 'สัญญาเงินกู้', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลการกู้ยืม</h4>
            <p>แสดงการกู้ยืม ของ {{ $member->profile->fullname }}</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลการกู้ยืม สัญญาเลขที่ {{ $loan->code }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:20%; border-top: none;">สัญญาเลขที่:</th>
                            <td style="border-top: none;">{{ $loan->code }}</td>
                        </tr>
                        <tr>
                            <th>ผู้กู้:</th>
                            <td>
                                {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullname }} 
                            </td>
                        </tr>
                        <tr>
                            <th>ประเภท:</th>
                            <td>{{ $loan->loantype->name }}</td>
                        </tr>
                        <tr>
                            <th>อัตราดอกเบี้ย:</th>
                            <td>{{ $loan->rate }}%</td>
                        </tr>
                        <tr>
                            <th>จำนวนงวดที่ผ่อน:</th>
                            <td>{{ number_format($loan->period, 0, '.', ',') }} งวด</td>
                        </tr>
                        <tr>
                            <th>วันที่กู้:</th>
                            <td>{{ Diamond::parse($loan->loaned_at)->thai_format('j F Y') }}</td>
                        </tr>
                        <tr>
                            <th>จำนวนเงินที่กู้:</th>
                            <td>{{ number_format($loan->outstanding, 2, '.', ',') }} บาท</td>
                        </tr>
                        <tr>
                            <th>จำนวนเงินต้นที่ชำระแล้ว:</th>
                            <td>{{ number_format($loan->payments->sum('principle'), 2, '.', ',') }} บาท</td>
                        </tr>
                        <tr>
                            <th>คงเหลือ</th>
                            <td>{{ number_format($loan->outstanding - $loan->payments->sum('principle'), 2, '.', ',') }} บาท</td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดการผ่อนชำระ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table id="dataTables-loans" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 15%;">งวดที่</th>
                                <th style="width: 15%;">วันที่ชำระ</th>
                                <th style="width: 15%;">เงินต้น</th>
                                <th style="width: 15%;">ดอกเบี้ย</th>
                                <th style="width: 15%;">รวมเป็นเงิน</th>
                                <th style="width: 15%;">ใบรับเงินค่างวด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $index => $payment) 
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> งวดที่ {{ $payment->period }}</td>
                                    <td>{{ Diamond::parse($payment->pay_date)->thai_format('Y-m-d') }}</td>
                                    <td>{{ number_format($payment->principle, 2, '.', ',') }}</td>
                                    <td>{{ number_format($payment->interest, 2, '.', ',') }}</td>
                                    <td>{{ number_format($payment->principle + $payment->interest, 2, '.', ',') }}</td>
                                    <td>
                                        <a href="/member/loan/{{ $loan->id }}/{{ $payment->id }}/billing/{{ Diamond::parse($payment->pay_date)->endOfMonth()->format('Y-m-d') }}"><i class="fa fa-file-o"></i> ใบรับเงินค่างวด</a>
                                    </td>
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

         $('#dataTables-loans').dataTable({
            "iDisplayLength": 10,
            "columnDefs": [
                { type: 'formatted-num', targets: 0 },
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 },
                { type: 'formatted-num', targets: 5 }
            ]
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