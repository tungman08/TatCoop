@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการประเภทเงินกู้
            <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการประเภทเงินกู้', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการประเภทเงินกู้ของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของสหกรณ์</p>
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
                <h3 class="box-title"><i class="fa fa-credit-card"></i> รายชื่อประเภทเงินกู้</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat margin-b-md" type="button" data-tooltip="true" title="เพิ่มประเภทเงินกู้"
                    onclick="javascript:document.location.href='{{ url('/database/loantype/create') }}';">
                    <i class="fa fa-plus"></i> เพิ่มประเภทเงินกู้
                </button>
                <button class="btn btn-default btn-flat margin-b-md pull-right" type="button" data-tooltip="true" title="สมาชิกประเภทเงินกู้ที่ถูกลบ"
                    onclick="javascript:document.location.href='{{ url('/database/loantype/inactive') }}';">
                    <i class="fa fa-trash"></i> แสดงประเภทเงินกู้ที่ถูกลบ
                </button>
                <button class="btn btn-default btn-flat margin-b-md margin-r-sm pull-right" type="button" data-tooltip="true" title="สมาชิกประเภทเงินกู้ที่หมดอายุ"
                    onclick="javascript:document.location.href='{{ url('/database/loantype/expired') }}';">
                    <i class="fa fa-ban"></i> แสดงประเภทเงินกู้ที่สิ้นสุดการใช้
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-loantypes" class="table table-hover dataTable" width="100%">
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
                            <tr onclick="javascript: document.location.href  = '{{ url('/database/loantype/' . $type->id) }}';"
                                style="cursor: pointer;">
                                <td>{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i> {{ $type->name }}</td>
                                <td>{{ number_format($type->rate, 2, '.', ',') }}%</td>
                                <td>{{ (Diamond::minValue()->diffInDays(Diamond::parse($type->start_date)) > 0) ? Diamond::parse($type->start_date)->thai_format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ (Diamond::maxValue()->diffInDays(Diamond::parse($type->expire_date)) > 0) ? Diamond::parse($type->expire_date)->thai_format('Y-m-d') : 'N/A' }}</td>
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

        $('#dataTables-loantypes').dataTable({
            "iDisplayLength": 25
        });
    });
    </script>
@endsection