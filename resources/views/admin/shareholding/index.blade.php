@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการทุนเรือนหุ้นของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข ทุนเรือนหุ้นของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการทุนเรือนหุ้น', 'link' => '/service/shareholding/member'],
            ['item' => 'ทุนเรือนหุ้น', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>
            <p>แสดงการชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullName }}</p>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลทุนเรือนหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
                    onclick="javascript:window.location = '/service/{{ $member->id }}/shareholding/create';">
                    <i class="fa fa-plus-circle"></i> ชำระเงินค่าหุ้น
                </button>

                <div class="table-responsive" style="margin-top: 15px;">
                    <table id="dataTables-shareholding" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 20%;">เดือน</th>
                                <th style="width: 15%;">ค่าหุ้นปกติ</th>
                                <th style="width: 15%;">ค่าหุ้นเงินสด</th>
                                <th style="width: 15%;">รวมเป็นเงิน</th>
                                <th style="width: 25%;">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @eval($count = 0)
                            @foreach($shareholdings->sortByDesc('name') as $share)
                                @eval($date = Diamond::parse($share->name))
                                <tr onclick="javascript: document.location = '{{ url('service/' . $member->id . '/shareholding/' . $share->id . '/edit') }}';" style="cursor: pointer;">
                                    <td>{{ ++$count }}</td>
                                    <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ $date->thai_format('F Y') }}</td>
                                    <td>{{ number_format($share->amount, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->amount_cash, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->amount + $share->amount_cash, 2, '.', ',') }} บาท</td>
                                    <td>{{ !empty($share->remark) ? $share->remark : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!-- /.table-responsive -->
                    </table>
                </div>
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
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();

        $('#dataTables-shareholding').dataTable({
            "iDisplayLength": 10
        });
    });
    </script>
@endsection