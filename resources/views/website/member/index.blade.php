@extends('website.member.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            สรุปข้อมูล
            <small>สรุปข้อมูลสมาชิก สอ.สรทท.</small>
        </h1>

        @include('website.member.breadcrumb', ['breadcrumb' => []])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>สมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">รหัสสมาชิก:</th>
                        <td>{{ $member->memberCode }}</td>
                    </tr> 
                    <tr>
                        <th>ชื่อสมาชิก:</th>
                        <td>{{ $member->profile->fullname }}</td>
                    </tr>  
                    <tr>
                        <th>ประเภทสมาชิก:</th>
                        <td>{{ $member->profile->employee->employee_type->name }}</td>
                    </tr> 
                    <tr>
                        <th>ผู้รับผลประโยชน์:</th>
                        @if ($member->beneficiaries->count() > 0)
                            <td>
                                <a href="{{ url(env('APP_URL') . '/storage/file/beneficiaries/' . $member->beneficiaries->first()->file) }}" target="_blank">
                                    <i class="fa fa-paperclip"></i> เอกสารแนบ
                                </a>
                            </td>
                        @else
                            <td style="color: #ff0000;">ไม่มีข้อมูล (กรุณาติดต่อเจ้าหน้าที่เพื่อแต่งตั้งผู้รับผลประโยชน์)</td>
                        @endif
                    </tr> 
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">ข้อมูลทุนเรือนหุ้น</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-info">
                                <tr>
                                    <th style="width:40%; border-top: none;">จำนวนหุ้นรายเดือน:</th>
                                    <td style="border-top: none;">{{ ($member->shareholding > 0) ? number_format($member->shareholding, 0, '.', ',') . ' หุ้น': '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ค่าหุ้นรายเดือน:</th>
                                    <td>{{ ($member->shareholding > 0) ? number_format($member->shareholding * 10, 2, '.', ',') . ' บาท': '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ทุนเรือนหุ้นสะสม:</th>
                                    <td>{{ number_format($member->shareHoldings->sum('amount'), 2, '.', ',') }} บาท</td>
                                </tr>        
                            </table>
                            <!-- /.table -->
                        </div>  
                        <!-- /.table-responsive --> 
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">ข้อมูลการกู้ยืม</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="table-responsive">
                            @php
                                $loansCount = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->count();
                                $outstanding = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum('outstanding');
                                $principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum(function ($value) { return $value->payments->sum('principle'); });
                            @endphp

                            <table class="table table-info">
                                <tr>
                                    <th style="width:40%; border-top: none;">จำนวนสัญญาทั้งหมด:</th>
                                    <td style="border-top: none;">{{ ($loansCount > 0) ? number_format($loansCount, 0, '.', ',') . ' สัญญา' : '-'}}</td>
                                </tr> 
                                <tr>
                                    <th>วงเงินที่กู้ทั้งหมด:</th>
                                    <td>{{ ($loansCount > 0) ? number_format($outstanding, 2, '.', ',') . ' บาท' : '-'}}</td>
                                </tr>  
                                <tr>
                                    <th>เงินต้นคงเหลือทั้งหมด:</th>
                                    <td>{{ ($outstanding - $principle > 0) ?number_format($outstanding - $principle, 2, '.', ',') . ' บาท' : '-' }}</td>
                                </tr>
                            </table>
                            <!-- /.table -->
                        </div>  
                        <!-- /.table-responsive --> 
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
    
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">ข้อมูลการค้ำประกัน</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-info">
                                <tr>
                                    @php($sureties = $member->sureties->filter(function ($value, $key) use ($member) { return is_null($value->completed_at) && $value->member_id == $member->id; }))
                                    <th style="width:40%; border-top: none;">ค้ำประกันตนเอง:</th>
                                    <td style="border-top: none;">{{ ($sureties->count()) > 0 ? number_format($sureties->count(), 0, '.', ',') . ' สัญญา (จำนวนหุ้นที่ใช้: ' . number_format(LoanCalculator::sureties_balance($sureties), 2, '.', ',') . ' บาท)' : '-' }}</td>
                                </tr>
                                <tr>
                                    @php($sureties = $member->sureties->filter(function ($value, $key) use ($member) { return !is_null($value->code) && is_null($value->completed_at) && $value->member_id != $member->id; }))
                                    <th>ค้ำประกันให้ผู้อื่น:</th>
                                    <td>{{ ($sureties->count()) > 0 ? number_format($sureties->count(), 0, '.', ',') . ' สัญญา (จำนวนเงินที่ใช้: ' . number_format(LoanCalculator::sureties_balance($sureties), 2, '.', ',') . ' บาท)' : '-' }}</td>
                                </tr> 
                                <tr>
                                    @php($sureties = $member->sureties->filter(function ($value, $key) use ($member) { return !is_null($value->code) && is_null($value->completed_at) && $value->member_id != $member->id; }))
                                    <th>สามารถค้ำประกันผู้อื่น:</th>
                                    <td>{{ (2 - $sureties->count() > 0) ? 'ค้ำได้อีก ' . number_format(2 - $sureties->count(), 0, '.', ',') . ' สัญญา' : 'เต็มแล้ว' }}</td>
                                </tr>  
                            </table>
                            <!-- /.table -->
                        </div>  
                        <!-- /.table-responsive --> 
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">ข้อมูลยอดลูกหนี้ เงินรับฝากและทุนเรือนหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-cashflow" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 90%;">ปี</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($index = 0)
                            @for ($year = Diamond::today()->year - 1; $year >= $startYear; $year--)
                                <tr onclick="javascript: document.location.href  = '{{ action('Website\CashflowController@show', ['id'=>$year]) }}';"
                                    style="cursor: pointer;">
                                    <td>{{ ++$index }}.</td>
                                    <td class="text-primary"><i class="fa fa-file-o fa-fw"></i> ข้อมูลปี {{ $year + 543 }}</td>
                                </tr>
                            @endfor
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
@endsection

@section('styles')
    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

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
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
    
            $('[data-tooltip="true"]').tooltip();
    
            $('#dataTables-cashflow').dataTable({
                "iDisplayLength": 10
            });     
        });
    </script>
@endsection