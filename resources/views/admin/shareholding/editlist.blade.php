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
            ['item' => 'ทุนเรือนหุ้น', 'link' => '/service/' . $member->id . '/shareholding'],
            ['item' => 'รายละเอียด', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>
            <p>แก้ไขข้อมูลชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullName }}</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-money"></i> รายละเอียดการชำระค่าหุ้น เดือน{{ Diamond::parse($shareholding_date)->thai_format('F Y') }}</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style="margin-top: 15px;">
                    <table id="dataTables-shareholding" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 20%;">วันที่ชำระ</th>
                                <th style="width: 20%;">ประเภท</th>
                                <th style="width: 20%;">จำนวน</th>
                                <th style="width: 30%;">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @eval($count = 0)
                            @foreach($shareholdings->sortByDesc('name') as $share)
                                <tr onclick="javascript: document.location = '{{ url('service/' . $member->id . '/shareholding/' . $share->id . '/edit') }}';" style="cursor: pointer;">
                                    <td>{{ ++$count }}.</td>
                                    <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ Diamond::parse($share->pay_date)->thai_format('Y-m-d') }}</td>
                                    <td><span class="label label-primary">{{ $share->shareholding_type->name }}</td>
                                    <td>{{ number_format($share->amount, 2, '.', ',') }} บาท</td>
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