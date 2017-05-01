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
                    <div class="col-md-2" style="padding-left: 0px; margin-bottom: 15px;">
                        <select class="form-control" id="selectyear" autocomplete="off">
                            @forelse($dividend_years as $year)
                                <option value="{{ $year->pay_year }}"{{ (intval($year->pay_year) == Diamond::today()->year) ? ' selected' : '' }}>เงินปันผลปี {{ $year->pay_year + 543 }}</option>
                            @empty
                                <option value="{{ Diamond::today()->year }}">เงินปันผลปี {{ Diamond::today()->year + 543 }}</option>
                            @endforelse
                        </select>
                    </div>
                    @php($rate = App\Dividend::where('rate_year', Diamond::today()->year)->first())
                    <strong id="dividend_rate" class="col-md-10" style="margin-top: 8px;">{{ !is_null($rate) ? 'อัตราเงินปันผล ' . $rate->rate . '%' : 'ยังไม่ได้กำหนดอัตราเงินปันผล' }}</strong>
                </div>

                <table id="dividend" class="table table-hover dataTable">
                    <thead>
                        <tr>
                            <th style="width: 20%;">#</th>
                            <th style="width: 20%;">จำนวนหุ้น</th>
                            <th style="width: 20%;">จำนวนเงิน</th>
                            <th style="width: 20%;">เงินปันผล</th>
                            <th style="width: 20%;">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $index = 0;
                            $total_amount = 0;
                            $total_shareholding = 0;
                            $total_dividend = 0;
                        @endphp

                        @foreach ($dividends as $dividend)
                            <tr>
                                <td class="text-primary">{{ ($index == 0) ? $dividend->name : Diamond::parse($dividend->name)->thai_format('F Y') }}</td>
                                <td>{{ number_format($dividend->shareholding, 0, '.', ',') }} หุ้น</td>
                                <td>{{ number_format($dividend->amount, 2, '.', ',') }} บาท</td>
                                <td>{{ ((!is_null($member->leave_date)) ? 
                                        (Diamond::parse($member->leave_date)->year <= Diamond::today()->year) ? 
                                            '0.00' : 
                                            number_format($dividend->dividend, 2, '.', ',') : 
                                            number_format($dividend->dividend, 2, '.', ',')) 
                                    }} 
                                </td>
                                <td{!! ((is_null($member->leave_date)) ? (!is_null($rate)) ? ' ' : ' class="text-danger"' : ' class="text-danger"') !!}>
                                    {{ ((is_null($member->leave_date)) ? $dividend->remark : 'ลาออกแล้ว') }}
                                </td>
                            </tr>

                            @php
                                $index++;
                                $total_amount += $dividend->amount;
                                $total_shareholding += $dividend->shareholding;
                                $total_dividend += $dividend->dividend;
                            @endphp

                        @endforeach
                        <tr>
                            <td class="text-primary"><strong>รวม</strong></td>
                            <td><strong>{{ number_format($total_shareholding, 0, '.', ',') }} หุ้น</strong></td>
                            <td><strong>{{ number_format($total_amount, 2, '.', ',') }} บาท</strong></td>
                            <td class="text-success"><strong>{{ 
                                ((!is_null($member->leave_date)) ? 
                                    (Diamond::parse($member->leave_date)->year <= Diamond::today()->year) ? 
                                        '0.00' :
                                        number_format($total_dividend, 2, '.', ',') : 
                                        number_format($total_dividend, 2, '.', ','))
                                }} บาท</strong></td>
                            <td{!! (is_null($member->leave_date)) ? (!is_null($rate)) ? (Diamond::today()->mount == 12) ? '' : ' class="text-warning' : ' class="text-danger"' : ' class="text-danger"' !!}>
                                {{ (is_null($member->leave_date)) ? 
                                    (!is_null($rate)) ? 
                                        (Diamond::today()->mount == 12) ? 
                                            '' : 
                                            'อัตราเงินปันผล ' . Diamond::today()->mount . ' เดือน' : 
                                            'ยังไม้ได้กำหนดอัตราเงินปันผล' :
                                            'ลาออกแล้ว' }}
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
            type: "post",
            data: {
                'id': {{ $member->id }},
                'year': $('#selectyear').val()
            },
            success: function(data) {
                $('#dividend_rate').html((data.dividend_rate == 0 ? 'ยังไม่ได้กำหนดอัตราเงินปันผล' : 'อัตราเงินปันผล ' + data.dividend_rate + '%' ));
                $('#dividend tbody>tr').remove();
                var selected_year = $('#selectyear').val();
                var index = 0;
                var total_amount = 0;
                var total_shareholding = 0;
                var total_dividend = 0;

                jQuery.each(data.dividends, function(i, val) {
                    var amount = (val.amount == null) ? 0 : val.amount;

                    $("#dividend tbody").append('<tr><td class="text-primary">' + 
                        ((index == 0) ? val.name : thai_date(moment(val.name, 'YYYY-MM-DD'))) + '</td><td>' + 
                        val.shareholding.format() + ' หุ้น</td><td>' + 
                        amount.format(2) + ' บาท</td><td>' + 
                        ((data.member.leave_date != null) ? 
                            (moment(data.member.leave_date).format('YYYY') <= selected_year) ? 
                                '0.00' : 
                                val.dividend.format(2) : 
                                val.dividend.format(2)) + ' บาท</td>' +
                        '<td' + ((data.member.leave_date == null) ? (data.dividend_rate > 0) ? '' : ' class="text-danger"' : ' class="text-danger"') + '>' + 
                        ((data.member.leave_date == null) ? val.remark : 'ลาออกแล้ว') + '</td></tr>');

                    index++;
                    total_amount += amount;
                    total_shareholding += val.shareholding;
                    total_dividend += val.dividend;
                });

                $("#dividend tbody").append('<tr><td class="text-primary"><strong>รวม</strong></td><td><strong>' + 
                    total_shareholding.format() + ' หุ้น</strong></td><td><strong>' + 
                    total_amount.format(2) + ' บาท</strong></td><td class="text-success"><strong>' + 
                    ((data.member.leave_date != null) ? 
                        (moment(data.member.leave_date).format('YYYY') <= selected_year) ? 
                            '0.00' :
                            total_dividend.format(2) : 
                            total_dividend.format(2)) + ' บาท</strong></td>' +
                    '<td' + ((data.member.leave_date == null) ? (data.dividend_rate > 0) ? '' : ' class="text-danger"' : ' class="text-danger"') + '>' + 
                    ((data.member.leave_date == null) ? (data.dividend_rate > 0) ? '' : 'ยังไม้ได้กำหนดอัตราเงินปันผล' : 'ลาออกแล้ว') + '</td></tr>');
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
    </script>
@endsection