@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผล/เฉลี่ยคืน
            <small>เพิ่ม ลบ แก้ไข อัตราเงินปันผล/เฉลี่ยคืนประจำปี สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงินปันผล/เฉลี่ยคืน', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการอัตราเงินปันผล/เฉลี่ยคืนประจำปีของสหกรณ์</h4>
            <p>
            ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข อัตราเงินปันผล/เฉลี่ยคืนประจำปีของสหกรณ์<br />
            โดยหลังจากที่มีการป้อนข้อมูลอัตราเงินปันผล/เฉลี่ยคืนแล้ว ระบบจะทำการคำนวณเงินปันผล/เฉลี่ยคืนให้กับสมาชิกทั้งหมด<br />
            ขอให้ผู้ดูแลระบบตรวจสอบยอดเงินที่ระบบคำนวณได้ว่าถูกต้องหรือไม่<br />
            ที่ <a href="{{ action('Admin\DividendController@getMember') }}">[ตรวจสอบเงินปันผล/เฉลี่ยคืน]</a> ก่อนวันที่เผยแพร่ที่ตั้งค่าไว้
            </p>
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
                <h3 class="box-title"><i class="fa fa-heart"></i> รายการอัตราเงินปันผล/เฉลี่ยคืนประจำปี</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat margin-b-md" type="button" data-tooltip="true" title="เพิ่มอัตราเงินปันผล/เฉลี่ยคืนประจำปี"
                    onclick="javascript:document.location.href='{{ url('/database/dividend/create') }}';">
                    <i class="fa fa-plus"></i> เพิ่มอัตราเงินปันผล/เฉลี่ยคืนประจำปี
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 15%;">ปี พ.ศ.</th>
                                <th style="width: 25%;">อัตราเงินปันผล</th>
                                <th style="width: 25%;">อัตราเงินเฉลี่ยคืน</th>
                                <th style="width: 25%;">วันที่เผยแพร่</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dividends->sortByDesc('rate_year') as $index => $dividend)
                                <tr style="cursor: pointer;" onclick="javascript: document.location.href = '{{ url('/database/dividend/' . $dividend->id . '/edit') }}';">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary">ปี {{ $dividend->rate_year + 543 }}</td>
                                    <td>{{ number_format($dividend->shareholding_rate, 1, '.', ',') }} %</td>
                                    <td>{{ number_format($dividend->loan_rate, 1, '.', ',') }} %</td>
                                    <td>{{ Diamond::parse($dividend->release_date)->thai_format('Y-m-d') }}</td>
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
    {!! Html::script(elixir('js/formatted-numbers.js')) !!}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();

        $('#dataTables').dataTable({
            "iDisplayLength": 25,
            "columnDefs": [
                { type: 'formatted-num', targets: 2 },
                { type: 'formatted-num', targets: 3 }
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