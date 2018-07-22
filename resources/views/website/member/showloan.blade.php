@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลการกู้ยืม
        <small>รายละเอียดข้อมูลกู้ยืมของสมาชิก</small>
    </h1>
    @include('website.member.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'การกู้ยืม', 'link' => '/member/' . $member->id . '/loan'],
        ['item' => 'สัญญาเงินกู้', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลการกู้ยืม</h4>
            <p>แสดงการกู้ยืม ของ {{ $member->profile->fullName }}</p>
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
                                {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullName }} 
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
                            <td>{{ Diamond::parse($loan->loaned_at)->thai_format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>จำนวนเงินที่กู้:</th>
                            <td>{{ number_format($loan->outstanding, 2, '.', ',') }} บาท</td>
                        </tr>
                        <tr>
                            <th>จำนวนเงินต้นที่ชำระแล้ว:</th>
                            <td>{{ number_format($loan->payments->sum('principle'), 2, '.', ',') }} บาท</td>
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
                                <th style="width: 18%;">วันที่ชำระ</th>
                                <th style="width: 18%;">เงินต้น</th>
                                <th style="width: 18%;">ดอกเบี้ย</th>
                                <th style="width: 18%;">รวมเป็นเงิน</th>
                                <th style="width: 18%;">ใบรับเงินค่างวด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($loan->payments as $payment) 
                                @php($date = Diamond::parse($payment->pay_date))
                                <tr>
                                    <td>{{ ++$count }}</td>
                                    <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> {{ $date->thai_format('Y-m-d') }}</td>
                                    <td>{{ number_format($payment->principle, 2, '.', ',') }}</td>
                                    <td>{{ number_format($payment->interest, 2, '.', ',') }}</td>
                                    <td>{{ number_format($payment->principle + $payment->interest, 2, '.', ',') }}</td>
                                    <td>
                                        <a href="/member/{{ $member->id }}/loan/{{ $loan->id }}/{{ $payment->id }}/billing/{{ $date->endOfMonth()->format('Y-m-d') }}"><i class="fa fa-file-o"></i> ใบรับเงินค่างวด</a>
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
            "iDisplayLength": 10
        });
    });   
    </script>
@endsection