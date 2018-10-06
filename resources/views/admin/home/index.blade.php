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
        <div class="row">
            @include('admin.home.info')
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
            <div class="col-md-12">
                @include('admin.home.member')
            </div>    
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
            <div class="col-md-12">
                @include('admin.home.shareholding')
            </div>    
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
            <div class="col-md-12">
                @include('admin.home.loan')
            </div>    
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent

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

    <!-- jQuery Flot Chart JavaScript -->
    {!! Html::script(elixir('js/jquery.flot.js')) !!}
    {!! Html::script(elixir('js/jquery.flot.tooltip.js')) !!}

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            
            dashboard();
        });   

        function dashboard() {
            $.ajax({
                dataType: 'json',
                url: '/dashboard',
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

                    var dataset_loans = [
                        { label: "&nbsp;เงินที่ให้กู้", data: data.chart.loans[1], color: "#337ab7" },
                        { label: "&nbsp;ดอกเบี้ยที่เก็บได้", data: data.chart.loans[2], color: "#3c763d" },
                        { label: "&nbsp;ดอกเบี้ยที่เก็บได้ของปีก่อน", data: data.chart.loans[3], color: "#a1a1a1" },
                    ];
                    $.plot($("#loan-flot-line-chart"), dataset_loans, options_line_chart(data.chart.loans[0], data_length));
                    $("<div class='axisLabel yaxisLabel'></div>").text("จำนวนเงิน (บาท)").appendTo($("#loan-flot-line-chart"))
                        .css("margin-top", $("<div class='axisLabel yaxisLabel'></div>").width() / 2 - 20);

                    $.each(data.summary.members, function(index, value) {
                        append_item("#newmembers", index + 1, value);
                    });

                    $.each(data.summary.shareholdings, function(index, value) {
                        append_item("#topshareholdings", index + 1, value);
                    });

                    $.each(data.summary.loans, function(index, value) {
                        append_item("#toploans", index + 1, value);
                    });
                }
            });
        }

        function append_item(parent, index, value) {
            let str = '<li class="item"><div class="product-info"></div><a href="' + value.link + '" class="product-title">' + 
                index + '. ' + value.fullname + 
                '<span class="label ' + ((value.employee_type == 'พนักงาน/ลูกจ้าง ททท.') ? 'label-success' : 'label-warning') + ' pull-right">' + value.employee_type + 
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
                    min: 0,
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