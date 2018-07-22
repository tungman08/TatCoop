@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผลประจำปีของสมาชิกสหกรณ์ฯ
            <small>คำนวณเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงินปันผลประจำปีของสมาชิก', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การคำนวณเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ</h4>
            <p>ให้ผู้ดูแลระบบสามารถจัดการเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ ที่ระบบคำนวณอัตโนมัติ</p>
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
                <h3 class="box-title"><i class="fa fa-baht"></i> เงินปันผลประจำปี</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 10%;">ปี พ.ศ.</th>
                                <th style="width: 10%;">อัตราเงินปันผล</th>
                                <th style="width: 20%;">เงินปันผลรวม</th>
                                <th style="width: 10%;">อัตราเงินเฉลี่ยคืน</th>
                                <th style="width: 20%;">เงินเฉลี่ยคืนรวม</th>
                                <th style="width: 20%;">รวมทั้งสิ้น</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($dividends as $dividend)
                                <tr style="cursor: pointer;" onclick="javascript: window.location = '{{ url('/admin/dividendmember/' . $dividend->id) }}';">
                                    <td>{{ ++$count }}</td>
                                    <td class="text-primary">ปี {{ $dividend->rate_year + 543 }}</td>
                                    <td>{{ $dividend->shareholding_rate }}%</td>
                                    <td>{{ number_format($dividend->shareholding_dividend, 2, '.', ',') }}</td>
                                    <td>{{ $dividend->loan_rate }}%</td>
                                    <td>{{ number_format($dividend->interest_dividend, 2, '.', ',') }}</td>
                                    <td>{{ number_format($dividend->shareholding_dividend + $dividend->interest_dividend, 2, '.', ',') }}</td>
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

        $('#dataTables').dataTable({
            "iDisplayLength": 25
        });
    });
    </script>
@endsection