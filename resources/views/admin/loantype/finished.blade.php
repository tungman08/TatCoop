@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการประเภทเงินกู้
            <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการประเภทเงินกู้', 'link' => '/admin/loantype'],
            ['item' => 'ประเภทเงินกู้', 'link' => '/admin/loantype/' . $loantype->id],
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
                                <th style="width: 10%;">#</th>
                                <th style="width: 15%;">เลขที่สัญญา</th>
                                <th style="width: 30%;">ชื่อผู้กู้</th>
                                <th style="width: 15%;">วันที่ทำสัญญา</th>
                                <th style="width: 15%;">วงเงินที่กู้</th>
                                <th style="width: 15%;">จำนวนงวดที่ผ่อนชำระ</th>
                            </tr>
                        </thead>
                        <tboby>
                            @php ($index = 0)
                            @foreach($loans as $loan)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i>{{ $loan->code }}</td>
                                    <td>{{ $loan->member->profile->fullName }}</td>
                                    <td>{{ Diamond::parse($loan->loaned_at)->thai_format('j M y') }}</td>
                                    <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->period, 0, '.', ',') }}</td>
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