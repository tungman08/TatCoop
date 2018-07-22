@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            การค้ำประกันของสมาชิกสหกรณ์ฯ
            <small>แสดงรายละเอียดข้อมูลการค้ำประกันของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลการค้ำประกัน', 'link' => '/service/guaruntee/member'],
            ['item' => 'รายละเอียด', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลการค้ำประกัน</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้ค้ำประกัน:</th>
                        <td>{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }}</td>
                    </tr>
                    <tr>
                        <th>จำนวนหุ้นที่ใช้ค้ำประกันตนเอง:</th>
                        @php($sureties = $member->sureties->filter(function ($value, $key) use ($member) { return is_null($value->completed_at) && $value->member_id == $member->id; }))
                        <td>{{ ($sureties->count()) > 0 ? number_format(LoanCalculator::sureties_balance($sureties), 2, '.', ',') . '/' . number_format($sureties->sum('pivot.amount'), 2, '.', ',') . ' บาท (จากสัญญาเงินกู้ที่อยู่ในระหว่างผ่อนชำระจำนวน ' . number_format($sureties->count(), 0, '.', ',') . ' สัญญา)' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>จำนวนเงินที่ใช้คำประกันผู้อื่น:</th>
                        @php($sureties = $member->sureties->filter(function ($value, $key) use ($member) { return !is_null($value->code) && is_null($value->completed_at) && $value->member_id != $member->id; }))
                        <td>{{ ($sureties->count() > 0) ? number_format(LoanCalculator::sureties_balance($sureties), 2, '.', ',') . '/' . number_format($sureties->sum('pivot.amount'), 2, '.', ',') . ' บาท (จากสัญญาเงินกู้ที่อยู่ในระหว่างผ่อนชำระจำนวน ' . number_format($sureties->count(), 0, '.', ',') . ' สัญญา)' : '-' }}</td>
                    </tr>     
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive -->          
        </div>

        <div class="row margin-b-md">
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-primary btn-lg" onclick="javascript:window.location.href='{{ url('/service/member/' . $member->id) }}';">
                    <i class="fa fa-user fa-fw"></i> ข้อมูลสมาชิก
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-success btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/shareholding') }}';">
                    <i class="fa fa-money fa-fw"></i> ทุนเรือนหุ้น
                </button>
            </div>            
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-danger btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/loan') }}';">
                    <i class="fa fa-credit-card fa-fw"></i> การกู้ยืม
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-warning btn-lg disabled">
                    <i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-purple btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/dividend') }}';">
                    <i class="fa fa-dollar fa-fw"></i> เงินปันผล
                </button>
            </div>
        </div>
        <!-- /.row -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-share-alt"></i> สัญญาเงินกู้ที่ค้ำประกันไว้</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-guaruntee" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 15%;">เลขที่สัญญา</th>
                                <th style="width: 25%;">ชื่อผู้กู้</th>
                                <th style="width: 10%;">วันที่กู้</th>
                                <th style="width: 15%;">วงเงินที่กู้</th>
                                <th style="width: 15%;">จำนวนหุ้นที่ค้ำประกันคงเหลือ</th>
                                <th style="width: 10%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); }) as $loan)
                                <tr onclick="javascript: document.location = '{{ url('service/' . $loan->member->id . '/loan/' . $loan->id) }}';" style="cursor: pointer;">
                                    <td>{{ ++$count }}</td>
                                    <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> {{ $loan->code }}</td>
                                    <td>{{ $loan->member->profile->fullName }}</td>
                                    <td>{{ Diamond::parse($loan->loaned_at)->thai_format('Y-m-d') }}</td>
                                    <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                    <td>{{ number_format(LoanCalculator::surety_balance($loan), 2, '.', ',') . '/' .number_format($loan->pivot->amount, 2, '.', ',') }} บาท</td>
                                    <td>{!! (!is_null($loan->completed_at)) ? '<span class="label label-success">ปิดยอดแล้ว</span>' : '<span class="label label-danger">กำลังผ่อนชำระ</span>' !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- /.table -->
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

        $('#dataTables-guaruntee').dataTable({
            "iDisplayLength": 10
        });     
    });   
    </script>
@endsection