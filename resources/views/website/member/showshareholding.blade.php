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
            <p>แสดงการชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullName }}</p>
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
							<th style="width:20%; border-top: none;">ชื่อผู้สมาชิก:</th>
							<td style="border-top: none;">{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }}</td>
						</tr>
						<tr>
							<th>ค่าหุ้นเดือน:</th>
							<td>{{ Diamond::parse($shareholding_date)->thai_format('F Y') }}</td>
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
                            @php($count = 0)
                            @foreach($shareholdings->sortByDesc('id')->sortByDesc('paydate') as $share)
                                <tr>
                                    <td>{{ ++$count }}.</td>
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
            "iDisplayLength": 10
        });
    });   
    </script>
@endsection