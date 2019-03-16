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
            ['item' => 'หมดอายุ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ประเภทเงินกู้ของสหกรณ์ที่หมดอายุ</h4>
            <p>แสดงรายชื่อประเภทเงินกู้ของสหกรณ์ที่หมดอายุทั้งหมด</p>
        </div>

        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card"></i> รายชื่อประเภทเงินกู้ที่หมดอายุ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-default btn-flat margin-b-md"
                    onclick="javascript:window.history.go(-1);">
                    <i class="fa fa-reply"></i> ถอยกลับ
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 25%;">ชื่อประเภทเงินกู้</th>
                                <th style="width: 20%;">อัตราดอกเบี้ย</th>
                                <th style="width: 15%;">วันที่เริ่มใช้</th>
                                <th style="width: 15%;">วันที่สิ้นสุดการใช้</th>
                                <th style="width: 15%;">จำนวนสัญญาเงินกู้</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loantypes as $index => $type)
                            <tr onclick="javascript: document.location.href  = '{{ url('/database/loantype/expired/' . $type->id) }}';"
                                style="cursor: pointer;">
                                <td>{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i> {{ $type->name }}</td>
                                <td>{{ number_format($type->rate, 2, '.', ',') }}%</td>
                                <td>{{ Diamond::parse($type->start_date)->thai_format('Y-m-d') }}</td>
                                <td>{{ Diamond::parse($type->expire_date)->thai_format('Y-m-d') }}</td>
                                <td>{{ number_format($type->loans->filter(function ($value, $key) { return !empty($value->code); })->count()) }}</td>
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