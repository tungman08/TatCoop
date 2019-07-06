@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ตั้งค่าการนำส่ง
            <small>ตั้งค่าการนำส่งจำนวนเงินเพื่อหักบัญชีเงินเดือน สำหรับสมาชิกประเภทพนักงาน/ลูกจ้าง ททท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ตั้งค่าการนำส่ง', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ตั้งค่าการนำส่ง</h4>    
            <p>ให้เจ้าหน้าที่สามารถจัดการ เปิด/ปิด ระบบนำส่งจำนวนเงินเพื่อหักบัญชีเงินเดือน สำหรับสมาชิกประเภทพนักงาน/ลูกจ้าง ททท. แบบบอัตโนมัติได้</p>  
            <p>คำแนะนำ: *** <u>เพื่อให้ระบบทำงานได้อย่างถูกต้อง ควรเปิดใช้งานทุกฟังก์ชั่นไว้ตลอดเวลา</u></p>  
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-gear"></i> ตั้งค่าการนำส่งค่าหุ้น</h3>
                    </div>
                    <!-- /.box-header -->
        
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px;">
                            <div id="shareholding_calculate" class="toggle-btn{{ ($shareholding->calculate_status) ? ' active' : '' }}">
                                <label class="toggle-label">คำนวณจำนวนเงินค่าหุ้นปกติทุกๆ วันที่ 1 ของเดือน เวลา 1.00 น.</label>
                                <input type="checkbox" id="cb_shareholding_calculate" class="cb-value" style="display: none;" {{ ($shareholding->calculate_status) ? ' checked' : '' }}/>
                                <span class="round-btn"></span>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0px;">
                            <div id="shareholding_approve" class="toggle-btn{{ ($shareholding->approve_status) ? ' active' : '' }}">
                                <label class="toggle-label">ทำรายงานการนำส่งจำนวนเงินค่าหุ้นปกติทุกๆ วันที่ 10 ของเดือน เวลา 1.00 น.</label>
                                <input type="checkbox" id="cb_shareholding_approve" class="cb-value" style="display: none;" {{ ($shareholding->approve_status) ? ' checked' : '' }}/>
                                <span class="round-btn"></span>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0px;">
                            <div id="shareholding_save" class="toggle-btn{{ ($shareholding->save_status) ? ' active' : '' }}">
                                <label class="toggle-label">บันทึกจำนวนเงินค่าหุ้นปกติทุกๆ วันสุดท้ายของเดือน เวลา 23.00 น.</label>
                                <input type="checkbox" id="cb_shareholding_save" class="cb-value" style="display: none;" {{ ($shareholding->save_status) ? ' checked' : '' }}/>
                                <span class="round-btn"></span>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!--/.col-->

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-gear"></i> ตั้งค่าการนำส่งค่าชำระเงินกู้</h3>
                    </div>
                    <!-- /.box-header -->
        
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px;">
                            <div id="payment_calculate" class="toggle-btn{{ ($payment->calculate_status) ? ' active' : '' }}">
                                <label class="toggle-label">คำนวณจำนวนเงินค่าชำระเงินกู้ปกติทุกๆ วันที่ 1 ของเดือน เวลา 1.00 น.</label>
                                <input type="checkbox" id="cb_payment_calculate" class="cb-value" style="display: none;" {{ ($payment->calculate_status) ? ' checked' : '' }}/>
                                <span class="round-btn"></span>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0px;">
                            <div id="payment_approve" class="toggle-btn{{ ($payment->approve_status) ? ' active' : '' }}">
                                <label class="toggle-label">ทำรายงานการนำส่งจำนวนเงินค่าชำระเงินกู้ปกติทุกๆ วันที่ 10 ของเดือน เวลา 1.00 น.</label>
                                <input type="checkbox" id="cb_payment_approve" class="cb-value" style="display: none;" {{ ($payment->approve_status) ? ' checked' : '' }}/>
                                <span class="round-btn"></span>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0px;">
                            <div id="payment_save" class="toggle-btn{{ ($payment->save_status) ? ' active' : '' }}">
                                <label class="toggle-label">บันทึกจำนวนเงินค่าชำระเงินกู้ปกติทุกๆ วันสุดท้ายของเดือน เวลา 23.00 น.</label>
                                <input type="checkbox" id="cb_payment_save" class="cb-value" style="display: none;" {{ ($payment->save_status) ? ' checked' : '' }}/>
                                <span class="round-btn"></span>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!--/.col-->
        </div>
        <!--/.row-->
    </section>
    <!-- /.content -->
@endsection

@section('styles')
    @parent

    <!-- Toggle Switch CSS -->
    {!! Html::style(elixir('css/toggle-switch.css')) !!}
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('#shareholding_calculate').click(() => {
                let status = $('#cb_shareholding_calculate').is(':checked');

                toggle_update('shareholding', 'calculate', status);
            });

            $('#shareholding_approve').click(() => {
                let status = $('#cb_shareholding_approve').is(':checked');

                toggle_update('shareholding', 'approve', status);
            });

            $('#shareholding_save').click(() => {
                let status = $('#cb_shareholding_save').is(':checked');

                toggle_update('shareholding', 'save', status);
            });

            $('#payment_calculate').click(() => {
                let status = $('#cb_payment_calculate').is(':checked');

                toggle_update('payment', 'calculate', status);
            });

            $('#payment_approve').click(() => {
                let status = $('#cb_payment_approve').is(':checked');

                toggle_update('payment', 'approve', status);
            });

            $('#payment_save').click(() => {
                let status = $('#cb_payment_save').is(':checked');

                toggle_update('payment', 'save', status);
            });
        });
       
        function toggle_update(name, action, status) {
            id = (name == 'shareholding') ? 1 : 2;

            $.ajax({
                dataType: 'json',
                url: '/routine/setting/' + id,
                type: 'post',
                data: { 
                    action: action,
                    status: status,
                },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function(result) {
                    let cal_switch = $('#' + name + '_calculate');
                    let apv_switch = $('#' + name + '_approve');
                    let sav_switch = $('#' + name + '_save');

                    set_toggle_switch(cal_switch, result.calculate_status);
                    set_toggle_switch(apv_switch, result.approve_status);
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