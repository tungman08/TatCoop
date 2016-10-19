@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการประเภทเงินกู้พิเศษ
        <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
    </h1>

    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการประเภทเงินกู้พิเศษ', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการประเภทเงินกู้พิเศษของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ประเภทเงินกู้พิเศษของสหกรณ์</p>
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
                <h3 class="box-title">รายชื่อประเภทเงินกู้พิเศษ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat" type="button" data-tooltip="true" title="เพิ่มประเภทเงินกู้พิเศษ"
                    style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.location.href='{{ url('/admin/loantype/create') }}';">
                    <i class="fa fa-plus"></i> เพิ่มประเภทเงินกู้พิเศษ
                </button>
                <button class="btn btn-default btn-flat pull-right" type="button" data-tooltip="true" title="สมาชิกประเภทเงินกู้พิเศษที่หมดอายุ"
                    style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.location.href='{{ url('/admin/loantype/expire') }}';">
                    <i class="fa fa-ban"></i> แสดงประเภทเงินกู้พิเศษที่หมดอายุ
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-loantypes" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 30%;">ชื่อประเภทเงินกู้พิเศษ</th>
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
                            <tr onclick="javascript: document.location = '{{ url('/admin/loantype/' . $type->id . '/edit') }}';"
                                style="cursor: pointer;">
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

    $('#dataTables-loantypes').dataTable({
        "iDisplayLength": 25
    });
    </script>
@endsection