@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการประเภทเงินกู้พิเศษ
        <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
    </h1>

    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการประเภทเงินกู้พิเศษ', 'link' => '/admin/loantype'],
        ['item' => 'หมดอายุ', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ประเภทเงินกู้พิเศษของสหกรณ์ที่หมดอายุ</h4>
            <p>แสดงรายชื่อประเภทเงินกู้พิเศษของสหกรณ์ที่หมดอายุทั้งหมด</p>
        </div>

        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title">รายชื่อประเภทเงินกู้พิเศษที่หมดอายุ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-default btn-flat" style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.history.go(-1);">
                    <i class="fa fa-reply"></i> ถอยกลับ
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ลำดับ</th>
                                <th style="width: 30%;">ชื่อเงินกู้พิเศษ</th>
                                <th style="width: 15%;">วงเงินกู้สูงสุด</th>
                                <th style="width: 15%;">ระยะเวลาผ่อนชำระสูงสุด</th>
                                <th style="width: 10%;">วันที่เริ่มใช้</th>
                                <th style="width: 10%;">วันที่หมดอายุ</th>
                                <th style="width: 10%;">จำนวนสัญญาเงินกู้</th>
                            </tr>
                        </thead>
                        <tbody>
                            @eval($count = 0)
                            @foreach($loantypes as $type)
                            <tr>
                                <td>{{ ++$count }}</td>
                                <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ $type->name }}</td>
                                <td>{{ number_format($type->cash_limit, 2,'.', ',') }} บาท</td>
                                <td>{{ number_format($type->installment_limit, 0,'.', ',') }} งวด</td>
                                <td>{{ Diamond::parse($type->start_date)->thai_format('j M Y') }}</td>
                                <td>{{ Diamond::parse($type->expire_date)->thai_format('j M Y') }}</td>
                                <td></td>
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
    });

    $('#dataTables-users').dataTable({
        "iDisplayLength": 25
    });
    </script>
@endsection