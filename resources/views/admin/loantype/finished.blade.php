@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการประเภทเงินกู้
            <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการประเภทเงินกู้', 'link' => '/database/loantype'],
            ['item' => 'ประเภทเงินกู้', 'link' => '/database/loantype/' . $loantype->id],
            ['item' => 'สัญญาเงินกู้ที่ชำระหมดแล้ว', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>สัญญาเงินกู้ที่ชำระหมดแล้ว</h4>
            <p>สัญญาเงินกู้ทั้งหมดที่ใช้ ประเภทเงินกู้ชื่อ {{ $loantype->name }} ที่ผ่อนชำระหมดแล้ว</p>
        </div>

       <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card"></i> สัญญาเงินกู้ที่ผ่อนชำระหมดแล้ว</h3>          
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-finished" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 8%;">#</th>
                                <th style="width: 8%;">เลขที่สัญญา</th>
                                <th style="width: 18%;">ชื่อผู้กู้</th>
                                <th style="width: 10%;">วันที่ทำสัญญา</th>
                                <th style="width: 12%;">วงเงินที่กู้</th>
                                <th style="width: 12%;">เงินต้น</th>
                                <th style="width: 12%;">ดอกเบี้ยรวม</th>
                                <th style="width: 12%;">จำนวนงวดที่ผ่อนชำระ</th>
                                <th style="width: 8%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php ($index = 0)
                            @foreach($loans->sortByDesc(function ($value, $key) {
                                    $code = explode('/', $value->code);
                                    return $code[1] . $code[0];
                                }) as $loan)
                                <tr style="cursor: pointer;" onclick="javascript: document.location.href  = '/service/{{ $loan->member->id }}/loan/{{ $loan->id }}';">
                                    <td>{{ ++$index }}</td>
                                    <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i>{{ $loan->code }}</td>
                                    <td>{{ $loan->member->profile->fullname }}</td>
                                    <td>{{ Diamond::parse($loan->loaned_at)->thai_format('Y-m-d') }}</td>
                                    <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->payments->sum('principle'), 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->payments->sum('interest'), 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->payments->count(), 0, '.', ',') }}/{{ number_format($loan->period, 0, '.', ',') }}</td>
                                    <td>{!! (!is_null($loan->completed_at)) ? '<span class="label label-success">ปิดยอดแล้ว</span>' : '<span class="label label-danger">กำลังผ่อนชำระ</span>' !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
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
    });

    $('#dataTables-finished').dataTable({
        "iDisplayLength": 25
    });
    </script>
@endsection