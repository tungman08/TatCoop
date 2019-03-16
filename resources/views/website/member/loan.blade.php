@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลการกู้ยืม
        <small>รายละเอียดข้อมูลกู้ยืมของสมาชิก</small>
    </h1>
    @include('website.member.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'การกู้ยืม', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลการกู้ยืม</h4>

            <div class="table-responsive">
                @php
                    $loansCount = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->count();
                    $outstanding = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum('outstanding');
                    $principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum(function ($value) { return $value->payments->sum('principle'); });
                @endphp

                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">จำนวนสัญญาที่กำลังผ่อนชำระ:</th>
                        <td>{{ ($loansCount > 0) ? number_format($loansCount, 0, '.', ',') . ' สัญญา' : '-'}}</td>
                    </tr> 
                    <tr>
                        <th>วงเงินที่กู้ทั้งหมด:</th>
                        <td>{{ ($loansCount > 0) ? number_format($outstanding, 2, '.', ',') . ' บาท' : '-'}}</td>
                    </tr>  
                    <tr>
                        <th>เงินต้นคงเหลือทั้งหมด:</th>
                        <td>{{ ($outstanding - $principle > 0) ?number_format($outstanding - $principle, 2, '.', ',') . ' บาท' : '-' }}</td>
                    </tr>
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลการกู้ยืม</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table id="dataTables-loans" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 12%;">เลขที่สัญญา</th>
                                <th style="width: 12%;">ประเภทเงินกู้</th>
                                <th style="width: 10%;">วันที่กู้</th>
                                <th style="width: 12%;">วงเงินที่กู้</th>
                                <th style="width: 12%;">จำนวนงวด</th>
                                <th style="width: 12%;">ชำระแล้ว</th>
                                <th style="width: 12%;">คงเหลือ</th>
                                <th style="width: 8%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $index => $loan) 
                            <tr onclick="javascript: document.location.href  = '{{ url('/member/' . $member->id . '/loan/' . $loan->id) }}';"
                                style="cursor: pointer;">
                                <td>{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> {{ $loan->code }}</td>
                                <td>{{ $loan->loanType->name }}</td>
                                <td>{{ Diamond::parse($loan->loaned_at)->thai_format('Y-m-d') }}</td>
                                <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                <td>{{ number_format($loan->payments->max('period'), 0, '.', ',') }}/{{ number_format($loan->period, 0, '.', ',') }}</td>
                                <td>{{ number_format($loan->payments->sum('principle'), 2, '.', ',') }}</td>
                                <td>{{ number_format(round($loan->outstanding - $loan->payments->sum('principle'), 2), 2, '.', ',') }}</td>
                                <td>{!! (!is_null($loan->completed_at)) ? '<span class="label label-primary">ปิดยอดแล้ว</span>' : '<span class="label label-danger">กำลังผ่อนชำระ</span>' !!}</td>
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
                { type: 'formatted-num', targets: 4 },
                { type: 'formatted-num', targets: 6 },
                { type: 'formatted-num', targets: 7 }
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