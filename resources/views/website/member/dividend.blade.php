@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลเงินปันผล
        <small>รายละเอียดข้อมูลเงินปันผลของสมาชิก</small>
    </h1>
    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'ข้อมูลสมาชิก', 'link' => '/member'],
        ['item' => 'เงินปันผล', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลเงินปันผล</h4>
            <p>แสดงเงินปันผล ของ {{ $member->profile->fullName }}</p>
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
                <h3 class="box-title">รายละเอียดข้อมูลเงินปันผล</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <strong class="col-md-1" style="padding-left: 0px; margin-top: 8px;">เงินปันผลปี: </strong>
                    <div class="col-md-1" style="padding-left: 0px; margin-bottom: 15px;">
                        <select class="form-control" id="selectyear">
                            @if ($dividend_years->count() > 0)
                                @foreach($dividend_years as $year)
                                    <option value="{{ $year->pay_year }}"{{ ($year->pay_year == Diamond::today()->format('Y')) ? 'selected' : '' }}>{{ $year->pay_year }}</option>
                                @endforeach
                            @else
                                <option value="{{ Diamond::today()->format('Y') }}">{{ Diamond::today()->format('Y') }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                @eval($rate = App\Dividend::where('rate_year', Diamond::today()->year)->first())

                <table id="dividend" class="table table-hover dataTable">
                    <thead>
                        <tr>
                            <th style="width: 10%;">#</th>
                            <th style="width: 20%;">จำนวนหุ้น</th>
                            <th style="width: 20%;">จำนวนเงิน</th>
                            <th style="width: 20%;">เงินปันผล <span id="dividend_rate">({{ !is_null($rate) ? $rate->rate : '0' }}%)</span></th>
                            <th style="width: 30%;">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @eval($index = 0)
                        @eval($total_amount = 0)
                        @eval($total_shareholding = 0)
                        @eval($total_dividend = 0)
                        @foreach ($dividends as $dividend)
                            <tr>
                                <td class="text-primary">{{ ($index == 0) ? $dividend->name : Diamond::parse($dividend->name)->thai_format('F Y') }}</td>
                                <td>{{ number_format($dividend->shareholding, 0, '.', ',') }} หุ้น</td>
                                <td>{{ number_format($dividend->amount, 2, '.', ',') }} บาท</td>
                                <td>{{ number_format($dividend->dividend, 2, '.', ',') }} บาท</td>
                                <td{!! (is_null($rate)) ? ' class="text-danger"' : '' !!}>{{ $dividend->remark }}</td>
                            </tr>
                            @eval($index++)
                            @eval($total_amount += $dividend->amount)
                            @eval($total_shareholding += $dividend->shareholding)
                            @eval($total_dividend += $dividend->dividend)  
                        @endforeach
                        <tr>
                            <td class="text-primary"><strong>รวม</strong></td>
                            <td><strong>{{ number_format($total_shareholding, 0, '.', ',') }} หุ้น</strong></td>
                            <td><strong>{{ number_format($total_amount, 2, '.', ',') }} บาท</strong></td>
                            <td class="text-success"><strong>{{ number_format($total_dividend, 2, '.', ',') }} บาท</strong></td>
                            <td{!! (!is_null($rate)) ? (Diamond::today()->mount == 12) ? '' : ' class="text-warning' : ' class="text-danger"' !!}>
                                {{ (!is_null($rate)) ? 
                                    (Diamond::today()->mount == 12) ? 
                                        '' : 
                                        'อัตราเงินปันผล ' . Diamond::today()->mount . ' เดือน' : 
                                        'ยังไม้ได้กำหนดอัตราเงินปันผล' }}
                            </td>
                        </tr> 
                    </tbody>
                </table>
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
    });

    $('#selectyear').change(function() {
        $.ajax({
            url: '/ajax/dividend',
            type: "get",
            data: {
                'id': {{ $member->id }},
                'year': $('#selectyear').val()
            },
            success: function(data) {
                $('#dividend_rate').html('(' + (data.dividend_rate == null ? '0' : data.dividend_rate ) + '%)');
                $('#dividend tbody>tr').remove();
                var index = 0;
                var total_amount = 0;
                var total_shareholding = 0;
                var total_dividend = 0;

                jQuery.each(data.dividends, function(i, val) {
                    $("#dividend tbody").append('<tr><td class="text-primary">' + 
                        ((index == 0) ? val.name : thai_date(moment(val.name, 'YYYY-MM-DD'))) + '</td><td>' + 
                        val.shareholding.format() + ' หุ้น</td><td>' + 
                        val.amount.format(2) + ' บาท</td><td>' + 
                        val.dividend.format(2) + ' บาท</td><td' + (data.dividend_rate == null ? ' class="text-danger"' : '') + '>' + 
                        val.remark + '</td></tr>');

                    index++;
                    total_amount += val.amount;
                    total_shareholding += val.shareholding;
                    total_dividend += val.dividend;
                });

                $("#dividend tbody").append('<tr><td class="text-primary"><strong>รวม</strong></td><td><strong>' + 
                    total_shareholding.format() + ' หุ้น</strong></td><td><strong>' + 
                    total_amount.format(2) + ' บาท</strong></td><td class="text-success"><strong>' + 
                    total_dividend.format(2) + ' บาท</strong></td><td' + (data.dividend_rate == null ? ' class="text-danger"' : '') + '>' + 
                    (data.dividend_rate == null ? 'ยังไม้ได้กำหนดอัตราเงินปันผล' : '') + '</td></tr>');
            }
        });
    });

    Number.prototype.format = function(n, x) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
        return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    };

    function thai_date(date) {
        var months = { 'January': 'มกราคม', 'February': 'กุมภาพันธ์', 'March': 'มีนาคม', 'April': 'เมษายน', 'May': 'พฤษภาคม', 'June': 'มิถุนายน', 'July': 'กรกฎาคม', 'August': 'สิงหาคม', 'September': 'กันยายน', 'October': 'ตุลาคม', 'November': 'พฤศจิกายน', 'December': 'ธันวาคม' };
        var month = months[date.format("MMMM")];
        var year = parseInt(date.format("YYYY"), 10) + 543;

        return month + " " + year.toString();
    }
@endsection