@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลการค้ำประกัน
        <small>รายละเอียดข้อมูลทุนเรือนหุ้นของสมาชิก</small>
    </h1>
    @include('website.member.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'ข้อมูลสมาชิก', 'link' => '/member'],
        ['item' => 'การค้ำประกัน', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลการค้ำประกัน</h4>
            <p>แสดงการค้ำประกันการกู้ยืม ของ {{ $member->profile->fullName }}</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลการค้ำประกัน</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-guaruntee" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 15%;">เลขที่สัญญา</th>
                                <th style="width: 25%;">ชื่อผู้กู้</th>
                                <th style="width: 15%;">วันที่กู้</th>
                                <th style="width: 15%;">วงเงินที่กู้</th>
                                <th style="width: 15%;">จำนวนเงินค้ำประกัน</th>
                                <th style="width: 10%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($member->sureties as $loan)
                                @if ($loan->member->id <> $member->id)
                                    <tr onclick="javascript: document.location = '{{ url('/member/' . $member->id . '/quaruntee/' . $loan->id) }}';" style="cursor: pointer;">
                                        <td>{{ ++$count }}</td>
                                        <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> {{ $loan->code }}</td>
                                        <td>{{ $loan->member->profile->fullName }}</td>
                                        <td>{{ Diamond::parse($loan->loaned_at)->thai_format('j M Y') }}</td>
                                        <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                        <td>{{ number_format($loan->pivot->amount, 2, '.', ',') }}</td>
                                        <td class="{{ is_null($loan->completed_at) ? 'text-danger' : 'text-success' }}">{{ is_null($loan->completed_at) ? 'กำลังผ่อนชำระ' : 'ผ่อนชำระหมดแล้ว' }}</td>
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