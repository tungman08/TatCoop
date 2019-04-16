@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            สรุปข้อมูลของ สอ.สรทท.
            <small>สรุปข้อมูลรายละเอียดของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => []])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        @include('admin.home.info')

        <!-- Filter boxes -->
        @include('admin.home.filter')

        <!-- Main row -->
        @include('admin.home.member')

        <!-- Main row -->
        @include('admin.home.shareholding')

        <!-- Main row -->
        @include('admin.home.loan')
    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    <style>
        .flot-chart {
            height: 320px;
        }
    </style>
@endsection

@section('scripts')
    @parent

    <!-- Moment JS JavaScript -->
    {{ Html::script(elixir('js/moment.js')) }}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <!-- jQuery Flot Chart JavaScript -->
    {!! Html::script(elixir('js/jquery.flot.js')) !!}
    {!! Html::script(elixir('js/jquery.flot.tooltip.js')) !!}

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            
            dashboard($('#datepicker').val());

            $('#datepicker').datetimepicker({
                locale: moment.locale("th"),
                viewMode: 'years',
                minDate: moment('2018-01-01'),
                maxDate: moment(),
                format: 'YYYY',
                useCurrent: false,
                focusOnShow: false,
                buddhism: true
            }).on("dp.change", function (e) {
                dashboard($(this).val());
            }).on('dp.hide', function(e){
                setTimeout(function() {
                    $('#datepicker').data('DateTimePicker').viewMode('years');
                }, 1);
            });
        });   

        function dashboard(year) {
            $('.filter-year').html(parseInt(year, 10) + 543);

            $.ajax({
                dataType: 'json',
                url: '/dashboard',
                data: {
                    'year': year
                },
                type: 'post',
                cache: false,
                beforeSend: function () {
                    $(".ajax-loading").css("display", "block");
                },
                success: function (data) {
                    $(".ajax-loading").css("display", "none");

                    var data_length = 12;

                    var dataset_members = [
                        { label: "&nbsp;จำนวนสมาชิกใหม่", data: data.chart.members[1], color: "#337ab7" },
                        { label: "&nbsp;จำนวนสมาชิกที่ลาออก", data: data.chart.members[2], color: "#dd4b39" }
                    ];
                    $.plot($("#member-flot-line-chart"), dataset_members, options_line_chart(data.chart.members[0], data_length));
                    $("<div class='axisLabel yaxisLabel'></div>").text("จำนวนสมาชิก (คน)").appendTo($("#member-flot-line-chart"))
                        .css("margin-top", $("<div class='axisLabel yaxisLabel'></div>").width() / 2 - 20);

                    var dataset_shareholdings = [
                        { label: "&nbsp;เงินค่าหุ้นที่เก็บได้", data: data.chart.shareholdings[1], color: "#3c763d" },
                        { label: "&nbsp;เงินค่าหุ้นที่สมาชิกถอนออก", data: data.chart.shareholdings[2], color: "#dd4b39" },
                        { label: "&nbsp;เงินค่าหุ้นที่เก็บได้ของปีก่อน", data: data.chart.shareholdings[3], color: "#a1a1a1" }
                    ];
                    $.plot($("#shareholding-flot-line-chart"), dataset_shareholdings, options_line_chart(data.chart.shareholdings[0], data_length));
                    $("<div class='axisLabel yaxisLabel'></div>").text("จำนวนเงินค่าหุ้น (บาท)").appendTo($("#shareholding-flot-line-chart"))
                        .css("margin-top", $("<div class='axisLabel yaxisLabel'></div>").width() / 2 - 20);

                    var dataset_totalshareholdings = [
                        { label: "&nbsp;เงินค่าหุ้นสะสม", data: data.chart.totalshareholdings[1], color: "#337ab7" },
                    ];
                    $.plot($("#totalshareholding-flot-line-chart"), dataset_totalshareholdings, options_line_chart(data.chart.totalshareholdings[0], data_length));
                    $("<div class='axisLabel yaxisLabel'></div>").text("จำนวนเงินค่าหุ้น (บาท)").appendTo($("#totalshareholding-flot-line-chart"))
                        .css("margin-top", $("<div class='axisLabel yaxisLabel'></div>").width() / 2 - 20);

                    var dataset_loans = [
                        { label: "&nbsp;ยอดชำระเงินกู้ที่เก็บได้", data: data.chart.loans[1], color: "#3c763d" },
                        { label: "&nbsp;ยอดชำระเงินกู้ที่เก็บได้ของปีก่อน", data: data.chart.loans[2], color: "#a1a1a1" },
                    ];
                    $.plot($("#loan-flot-line-chart"), dataset_loans, options_line_chart(data.chart.loans[0], data_length));
                    $("<div class='axisLabel yaxisLabel'></div>").text("จำนวนเงิน (บาท)").appendTo($("#loan-flot-line-chart"))
                        .css("margin-top", $("<div class='axisLabel yaxisLabel'></div>").width() / 2 - 20);

                    var dataset_loantypes = [
                        { label: "&nbsp;เงินที่ให้กู้ทั้งหมด", data: data.chart.loantypes[1], color: "#337ab7" },
                        { label: "&nbsp;เงินกู้สามัญที่ให้กู้", data: data.chart.loantypes[2], color: "#3c763d" },
                        { label: "&nbsp;เงินกู้ฉุกเฉินที่ให้กู้", data: data.chart.loantypes[3], color: "#dd4b39" },
                        { label: "&nbsp;เงินกู้เฉพาะกิจที่ให้กู้", data: data.chart.loantypes[4], color: "#ac58fa" }
                    ];
                    $.plot($("#loantype-flot-line-chart"), dataset_loantypes, options_line_chart(data.chart.loantypes[0], data_length));
                    $("<div class='axisLabel yaxisLabel'></div>").text("จำนวนเงิน (บาท)").appendTo($("#loantype-flot-line-chart"))
                        .css("margin-top", $("<div class='axisLabel yaxisLabel'></div>").width() / 2 - 20);

                    $('#newmembers').empty();
                    $.each(data.summary.members, function(index, value) {
                        append_item("#newmembers", index + 1, value);
                    });

                    $('#topmonthshareholdings').empty();
                    $.each(data.summary.monthshareholding, function(index, value) {
                        append_item("#topmonthshareholdings", index + 1, value);
                    });

                    $('#topshareholdings').empty();
                    $.each(data.summary.shareholdings, function(index, value) {
                        append_item("#topshareholdings", index + 1, value);
                    });

                    $('#toploans').empty();
                    $.each(data.summary.loans, function(index, value) {
                        append_item("#toploans", index + 1, value);
                    });

                    $('#toploantypes').empty();
                    $.each(data.summary.loantypes, function(index, value) {
                        append_item("#toploantypes", index + 1, value);
                    });
                }
            });
        }

        function append_item(parent, index, value) {
            let str = '<li class="item"><div class="product-info"></div><a href="' + value.link + '" class="product-title">' + 
                index + '. ' + value.fullname + 
                '<span class="label ' + ((value.employee_type != 'พนักงาน/ลูกจ้าง ททท.') ? (value.employee_type != 'บุคคลภายนอก') ? 'label-info' : 'label-warning' : 'label-success') + ' pull-right">' + value.employee_type +
                '</span></a><span class="product-description">' + value.message + '</span></li>';

            $(parent).append(str);
        }

        function options_line_chart(ticks, length) {
            var options = {
                series: {
                    lines: {
                        show: true,
                        fill: false
                    },
                    points: { show: true }
                },
                legend: {
                    position: "ne"
                },
                grid: {
                    hoverable: true, //IMPORTANT! this is needed for tooltip to work
                    minBorderMargin: 20,
                    labelMargin: 10,
                    margin: {
                        top: 0,
                        bottom: 20,
                        left: 20
                    }
                },
                xaxis: {
                    min: 0,
                    max: length + 1,
                    labelWidth: 30,
                    tickDecimals: 0,
                    minTickSize: 1,
                    ticks: ticks
                },
                yaxis: {
                    labelWidth: 50,
                    tickDecimals: 0,
                    minTickSize: 1,
                    tickFormatter: function numberWithCommas(x) {
                                        return x.toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ",");
                                    }
                },
                tooltip: true,
                tooltipOpts: {
                    content: customTooltip
                }
            };

            return options;
        }

        function customTooltip(label, x, y) {
            return $.number(y, 0);
        }

        function getMin($data) {
            var min = 0;

            $.each($data, function(key, value) {
                min = (min > value[1]) ? value[1] : min;
            });

            return min + (min * 0.1);
        }
    </script>
@endsection