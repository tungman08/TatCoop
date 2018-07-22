@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ข้อมูลเงินปันผลของสมาชิกสหกรณ์ฯ
            <small>แสดงรายละเอียดข้อมูลเงินปันผลของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลเงินปันผล', 'link' => 'service/dividend/member'],
            ['item' => 'เงินปันผล', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลเงินปันผลของสมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }}</td>
                    </tr>
                    <tr>
                        @php($rate = (!is_null($dividend_years)) ? $dividend_years->last() : 0)
                        <th>เงินปันผลปี:</th>
                        <td><span class="year">{{ $rate->rate_year + 543 }}</span></td>
                    </tr>  
                    <tr>
                        <th>เงินปันผลรวม (อัตรา {{ $rate->shareholding_rate }}%):</th>
                        <td>{{ number_format($dividends->sum('shareholding_dividend'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>เงินเฉลี่ยคืนรวม (อัตรา {{ $rate->loan_rate }}%)</td>
                        <td>{{ number_format($dividends->sum('interest_dividend'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>รวมทั้งสิ้น</td>
                        <td id="grand-total">{{ number_format($dividends->sum('shareholding_dividend') + $dividends->sum('interest_dividend'), 2, '.', ',') }} บาท</td>
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
                <button type="button" class="btn btn-block btn-warning btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/guaruntee') }}';">
                    <i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-purple btn-lg disabled">
                    <i class="fa fa-dollar fa-fw"></i> เงินปันผล
                </button>
            </div>
        </div>
        <!-- /.row -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-dollar"></i> เงินปันผลปี <span class="year">{{ $rate->rate_year + 543 }}</span></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <select class="form-control" id="selectyear" autocomplete="off">
                                @foreach($dividend_years as $year)
                                    @if ($year->rate_year == $dividend_years->last()->rate_year)
                                        <option value="{{ $year->rate_year }}" selected>เงินปันผลปี {{ $year->rate_year + 543 }}</option>
                                    @else 
                                        <option value="{{ $year->rate_year }}">เงินปันผลปี {{ $year->rate_year + 543 }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <!-- /.col -->               
                    </div>
                    <!-- /.row -->
                </div>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-dividend" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th class="text-right" style="width: 18%;">จำนวนเงินค่าหุ้น</th>
                                <th class="text-right" style="width: 18%;">จำนวนเงินปันผล</th>
                                <th class="text-right" style="width: 18%;">จำนวนดอกเบี้ยเงินกู้</th>
                                <th class="text-right" style="width: 18%;">จำนวนเงินเฉลี่ยคืน</th>
                                <th class="text-right" style="width: 18%;">รวมทั้งสิ้น</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dividends as $dividend)
                                <tr>
                                    <td class="text-primary">{{ $dividend->dividend_name }}</td>
                                    <td class="text-right">{{ number_format($dividend->shareholding, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($dividend->shareholding_dividend, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($dividend->interest, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($dividend->interest_dividend, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($dividend->shareholding_dividend + $dividend->interest_dividend, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-primary"><strong>รวม</strong></td>
                                <td class="text-right"><strong>{{ number_format($dividends->sum('shareholding'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($dividends->sum('shareholding_dividend'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($dividends->sum('interest'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($dividends->sum('interest_dividend'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($dividends->sum('total'), 2, '.', ',') }}</strong></td>
                            </tr> 
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
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('[data-tooltip="true"]').tooltip();

        $('#dataTables-dividend').dataTable({
            "iDisplayLength": 25,
            "bLengthChange": false,
            "bSort": false,
            "bFilter": false
        });     

        $('#selectyear').change(function() {
            var selected = parseInt($('#selectyear').val());

            $.ajax({
                url: '/ajax/dividend',
                type: "post",
                data: {
                    'id': {{ $member->id }},
                    'year': selected
                },
                success: function(data) {
                    $('.year').html(selected + 543);
                    $('#rate').html('(อัตราเงินปันผล: ' + data.rate.shareholding_rate.format() + '%, อัตราเงินเฉลี่ยคืน: ' + data.rate.loan_rate.format() + '%)');
                    $('#dataTables-dividend tbody>tr').remove();

                    var total_shareholding = 0;
                    var total_shareholding_dividend = 0;
                    var total_interest = 0;
                    var total_interest_dividend = 0;
                    var grand_total = 0;

                    jQuery.each(data.dividends, function(i, val) {
                        $("#dataTables-dividend tbody").append('<tr><td class="text-primary">' + 
                            val.name + '</td><td class="text-right">' + 
                            val.shareholding.format(2) + '</td><td class="text-right">' + 
                            val.shareholding_dividend.format(2) + '</td><td class="text-right">' + 
                            val.interest.format(2) + '</td><td class="text-right">' +
                            val.interest_dividend.format(2) + '</td><td class="text-right">' +
                            val.total.format(2) + '</td></tr>');

                        total_shareholding += val.shareholding;
                        total_shareholding_dividend += val.shareholding_dividend;
                        total_interest += val.interest;
                        total_interest_dividend += val.interest_dividend;
                        grand_total += val.total;
                    });

                    $("#dataTables-dividend tbody").append('<tr><td class="text-primary"><strong>รวม</strong></td><td><strong>' + 
                        total_shareholding.format(2) + '</strong></td><td class="text-right"><strong>' + 
                        total_shareholding_dividend.format(2) + ' </strong></td><td class="text-right"><strong>' + 
                        total_interest.format(2) + ' </strong></td><td class="text-right"><strong>' + 
                        total_interest_dividend.format(2) + ' </strong></td><td class="text-right"><strong>' + 
                        grand_total.format(2) + ' </strong></td></tr>');

                    $('#grand-total').html(grand_total.format(2) + ' บาท');
                }
            });
        });
    });   
    </script>
@endsection