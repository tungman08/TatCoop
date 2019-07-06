@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ชำระค่าหุ้นปกติ
            <small>ชำระค่าหุ้นปกติ สำหรับสมาชิกประเภทพนักงาน/ลูกจ้าง ททท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ชำระค่าหุ้นปกติ', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ค่าหุ้นปกติเพื่อตัดบัญชีเงินเดือน</h4>    
            <p>
                คำนวณยอดเงินที่ต้องชำระค่าค่าหุ้นปกติรายเดือนของสมาชิกประเภทพนักงาน/ลูกจ้าง ททท.<br />
                โดยระบบจะทำการคำนวณยอดเงินใน<u>ทุกๆ วันที่ 1 ของเดือน</u><br />
                เพื่อให้เจ้าหน้าที่ทำการส่งตัดบัญชีเงินเดือนล่วงหน้า<u>ในทุกๆ วันที่ 10 ของเดือน</u><br />
                และทำการบันทึกยอดเงินลงฐานข้อมูลจริงใน<u>ทุกๆ วันสุดท้ายของเดือนนั้นๆ</u><br />
            </p> 
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-sticky-note-o"></i> รายการนำส่งค่าหุ้นปกติเพื่อตัดบัญชีเงินเดือน</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 18%;">ค่าหุ้นปกติ</th>
                                <th style="width: 18%;">จำนวนสมาชิก</th>
                                <th style="width: 18%;">จำนวนหุ้นรวม</th>
                                <th style="width: 18%;">ค่าหุ้นรวม</th>
                                <th style="width: 18%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routines as $index => $routine) 
                                <tr onclick="javascript: document.location.href  = '{{ action('Admin\RoutineShareholdingController@show', ['id' => $routine->id]) }}';"
                                    style="cursor: pointer;">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-asterisk fa-fw"></i> ค่าหุ้นเดือน{{ $routine->name }}</td>
                                    <td>{{ number_format($routine->details->count(), 0, '.', ',') }} คน</td>
                                    <td>{{ number_format($routine->details->sum('amount') / 10, 2, '.', ',') }} หุ้น</td>
                                    <td>{{ number_format($routine->details->sum('amount'), 2, '.', ',') }} บาท</td>
                                    <td>
                                        @if (!is_null($routine->approved_date) && $routine->status) 
                                            <span class="label label-primary">บันทึกข้อมูลแล้ว</span>
                                        @elseif (!is_null($routine->approved_date) && !$routine->status)
                                            <span class="label label-info">นำส่งข้อมูลแล้ว</span>
                                        @elseif (is_null($routine->approved_date) && !$routine->status)
                                            <span class="label label-warning">รอนำข้อมูลส่ง</span>
                                        @else
                                            <span class="label label-danger">หมดอายุ</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

    <!-- Toggle Switch CSS -->
    {!! Html::style(elixir('css/toggle-switch.css')) !!}

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

            $('#dataTables').dataTable({
                "iDisplayLength": 10,
                "columnDefs": [
                    { type: 'formatted-num', targets: 2 },
                    { type: 'formatted-num', targets: 3 },
                    { type: 'formatted-num', targets: 4 }
                ]
            });

            // $('#auto_calculate').click(() => {
            //     let status = $('#calculate').is(':checked');

            //     toggle_calculate(status);
            // });

            // $('#auto_save').click(() => {
            //     let status = $('#save').is(':checked');

            //     toggle_save(status);
            // });

            // init();
        });

        function init() {
            $.ajax({
                dataType: 'json',
                url: '/ajax/autoshareholdingsetting',
                type: 'post',
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function(result) {
                    let cal_switch = $('#auto_calculate');
                    let sav_switch = $('#auto_save');

                    set_toggle_switch(cal_switch, result.calculate_status);
                    set_toggle_switch(sav_switch, result.save_status);

                    if (!result.calculate_status) {
                        sav_switch.parent('.form-group').hide();
                    }
                    else {
                        sav_switch.parent('.form-group').show();
                    }

                    $(".ajax-loading").css("display", "none");
                }
            });
        }

        function toggle_calculate(status) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/autoshareholdingcal',
                type: 'post',
                data: { status: status },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function(result) {
                    let cal_switch = $('#auto_calculate');
                    let sav_switch = $('#auto_save');

                    set_toggle_switch(cal_switch, result.calculate_status);
                    set_toggle_switch(sav_switch, result.save_status);

                    if (!result.calculate_status) {
                        sav_switch.parent('.form-group').hide();
                    }
                    else {
                        sav_switch.parent('.form-group').show();
                    }

                    $(".ajax-loading").css("display", "none");
                }
            });
        }

        function toggle_save(status) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/autoshareholdingsav',
                type: 'post',
                data: { status: status },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function(result) {
                    let cal_switch = $('#auto_calculate');
                    let sav_switch = $('#auto_save');

                    set_toggle_switch(cal_switch, result.calculate_status);
                    set_toggle_switch(sav_switch, result.save_status);

                    $(".ajax-loading").css("display", "none");
                }
            });
        }

        function set_toggle_switch(target, status) {
            let cb = target.find('input.cb-value');

            cb.prop("checked", status);

            if (status) {
                target.addClass('active');
            }
            else {
                target.removeClass('active');
            }
        }
    </script>
@endsection