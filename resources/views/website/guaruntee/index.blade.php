@extends('website.member.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลการค้ำประกัน
        <small>รายละเอียดข้อมูลทุนเรือนหุ้นของสมาชิก</small>
    </h1>
    @include('website.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'การค้ำประกัน', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลการค้ำประกัน</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        @php($sureties = $member->sureties->filter(function ($value, $key) use ($member) { return is_null($value->completed_at) && $value->member_id == $member->id; }))
                        <th style="width:20%;">สัญญาเงินกู้ที่ค้ำประกันตนเอง:</th>
                        <td>{{ ($sureties->count()) > 0 ? number_format($sureties->count(), 0, '.', ',') . ' สัญญา' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>จำนวนหุ้นที่ใช้ค้ำประกันตนเอง:</th>
                        <td>{{ ($sureties->count()) > 0 ? number_format(LoanCalculator::sureties_balance($sureties), 2, '.', ',') . '/' . number_format($sureties->sum('pivot.amount'), 2, '.', ',') . ' บาท' : '-' }}</td>
                    </tr>
                    <tr>
                        @php($sureties = $member->sureties->filter(function ($value, $key) use ($member) { return !is_null($value->code) && is_null($value->completed_at) && $value->member_id != $member->id; }))
                        <th>สัญญาเงินกู้ที่ค้ำประกันให้ผู้อื่น:</th>
                        <td>{{ ($sureties->count()) > 0 ? number_format($sureties->count(), 0, '.', ',') . ' สัญญา' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>จำนวนเงินที่ใช้คำประกันผู้อื่น:</th>
                        <td>{{ ($sureties->count() > 0) ? number_format(LoanCalculator::sureties_balance($sureties), 2, '.', ',') . '/' . number_format($sureties->sum('pivot.amount'), 2, '.', ',') . ' บาท' : '-' }}</td>
                    </tr>     
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลการค้ำประกันผู้อื่น</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-guaruntee" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 10%;">เลขที่สัญญา</th>
                                <th style="width: 26%;">ชื่อผู้กู้</th>
                                <th style="width: 15%;">วันที่กู้</th>
                                <th style="width: 10%;">วงเงินที่กู้</th>
                                <th style="width: 12%;">จำนวนเงินค้ำประกัน</th>
                                <th style="width: 12%;">เงินค้ำประกันคงเหลือ</th>
                                <th style="width: 10%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
							@php($loans = $member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); }))
                            @foreach($loans as $index => $loan)
                                @if ($loan->member->id <> $member->id)
                                    <tr>
                                        <td>{{ $index + 1 }}.</td>
                                        <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> {{ $loan->code }}</td>
                                        <td>{{ $loan->member->profile->fullname }}</td>
                                        <td>{{ Diamond::parse($loan->loaned_at)->thai_format('Y-m-d') }}</td>
                                        <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                        <td>{{ number_format($loan->pivot->amount, 2, '.', ',') }}</td>
                                        <td>{{ number_format(LoanCalculator::surety_balance($loan), 2, '.', ',') }}</td>
                                        <td>{!! is_null($loan->completed_at) ? '<span class="label label-danger">กำลังผ่อนชำระ</span>' : '<span class="label label-primary">ผ่อนชำระหมดแล้ว</span>' !!}</td>
                                    </tr>
                                @endif
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
        $('[data-tooltip="true"]').tooltip();

        $('#dataTables-guaruntee').dataTable({
            "iDisplayLength": 10
        });     
    });   
    </script>
@endsection