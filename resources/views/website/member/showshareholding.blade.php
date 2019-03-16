@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลทุนเรือนหุ้น
        <small>รายละเอียดข้อมูลทุนเรือนหุ้นของสมาชิก</small>
    </h1>
    @include('website.member.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'ทุนเรือนหุ้น', 'link' => '/member/' . $member->id . '/shareholding'],
        ['item' => Diamond::parse($shareholding_date)->thai_format('M Y'), 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">จำนวนหุ้นรายเดือน:</th>
                        <td>{{ ($member->shareholding > 0) ? number_format($member->shareholding, 0, '.', ',') . ' หุ้น': '-' }}</td>
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

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลการชำระค่าหุ้น เดือน{{ Diamond::parse($shareholding_date)->thai_format('F Y') }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table">
						<tr>
							<th style="width:20%; border-top: none;">ค่าหุ้นเดือน:</th>
							<td style="border-top: none;">{{ Diamond::parse($shareholding_date)->thai_format('F Y') }}</td>
						</tr>
						<tr>
							<th>ชำระจำนวน:</th>
							<td>{{ number_format($shareholdings->count(), 0, '.', ',') }} ครั้ง</td>
						</tr> 
						<tr>
							<th>เป็นเงิน:</th>
							<td>{{ number_format($shareholdings->sum('amount'), 2, '.', ',') }} บาท</td>
						</tr>  
						<tr>
							<th>ทุนเรือนหุ้นสะสม ณ {{ Diamond::parse($shareholding_date)->thai_format('M Y') }}:</th>
							<td>{{ number_format($total_shareholding + $shareholdings->sum('amount'), 2, '.', ',') }} บาท</td>
						</tr> 
                    </table>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลทุนเรือนหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table id="dataTables-shareholding" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 18%;">วันที่ชำระ</th>
                                <th style="width: 18%;">ประเภท</th>
                                <th style="width: 18%;">จำนวน</th>
                                <th style="width: 18%;">ทุนเรือนหุ้นสะสม</th>
								<th style="width: 18%;">ใบเสร็จรับเงินค่าหุ้น</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shareholdings as $index => $share)
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ Diamond::parse($share->paydate)->thai_format('Y-n-d') }}</td>
                                    <td><span class="label label-primary">{{ $share->shareholding_type_name }}</td>
                                    <td>{{ number_format($share->amount, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->total_shareholding + $share->amount, 2, '.', ',') }} บาท</td>
									<td>
                                        <a href="/member/{{ $member->id }}/shareholding/{{ $share->id }}/billing/{{ Diamond::parse($share->paydate)->thai_format('Y-n-d') }}"><i class="fa fa-file-o"></i> ใบรับเงินค่าหุ้น</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!-- /.table-responsive -->
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
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

         $('#dataTables-shareholding').dataTable({
            "iDisplayLength": 10,
            "columnDefs": [
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 }
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