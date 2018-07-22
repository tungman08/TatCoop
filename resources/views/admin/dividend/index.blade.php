@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผล
            <small>เพิ่ม ลบ แก้ไข อัตราเงินปันผลประจำปี สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงินปันผล', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการอัตราเงินปันผลประจำปีของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข อัตราเงินปันผลประจำปีของสหกรณ์</p>
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
                <h3 class="box-title"><i class="fa fa-dollar"></i> รายการอัตราเงินปันผลประจำปี</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat margin-b-md" type="button" data-tooltip="true" title="เพิ่มอัตราเงินปันผลประจำปี"
                    onclick="javascript:window.location.href='{{ url('/admin/dividend/create') }}';">
                    <i class="fa fa-plus"></i> เพิ่มอัตราเงินปันผลประจำปี
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-loantypes" class="table table-hover dataTable" width="100%">
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
                            @eval($count = 0)
                            @foreach($dividends->sortByDesc('rate_year') as $dividend)
                            <tr style="cursor: pointer;" onclick="javascript: window.location = '{{ url('/admin/dividend/' . $dividend->id . '/edit') }}';">
                                <td>{{ ++$count }}</td>
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

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();
    });

    $('#dataTables-loantypes').dataTable({
        "iDisplayLength": 25
    });
    </script>
@endsection