@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จับรางวัล
            <small>สุ่มจับรางวัลให้กับสมาชิกสหกรณ์</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จับรางวัล', 'link' => action('Admin\RewardController@index')],
            ['item' => Diamond::parse($reward->created_at)->thai_format('j M Y'), 'link' => action('Admin\RewardController@show', ['id' => $reward->id])],
            ['item' => 'ลงทะเบียน', 'link' => action('Admin\RewardController@getRegister', ['id' => $reward->id])],
            ['item' => 'ลงทะเบียนล่าช้า', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจับรางวัล วันที่ {{ Diamond::parse($reward->created_at)->thai_format('j M Y') }}</h4>   

            <ul class="list-info">
                @foreach($reward->rewardConfigs as $config)
                    <li>
                        รางวัล {{ number_format($config->price, 2, '.', ',') }} บาท
                        จำนวน {{ number_format($config->amount, 0, '.', ',') }} รางวัล

                        @if ($config->register)
                            <span class="label label-info">ลงทะเบียน</span>
                        @endif

                        @if ($config->special)
                            <span class="label label-info">รางวัลพิเศษ</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-smile-o"></i> รายชื่อผู้ลงทะเบียน</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                {{ Form::hidden('reward_id', $reward->id, ['id' => 'reward_id']) }}

                @if (($is_super || $is_admin))
                    <div class="form-group">
                        {{ Form::label('member_id', 'รหัสสมาชิก', [
                            'class'=>'control-label']) 
                        }}
                        <div class="input-group">
                            {{ Form::text('member_id', null, [
                                'id' => 'member_id',
                                'placeholder' => 'รหัสสมาชิก 5 หลัก',
                                'data-inputmask' => "'mask': '99999','placeholder': '0','autoUnmask': true,'removeMaskOnSubmit': true",
                                'data-mask',
                                'autocomplete'=>'off',
                                'class'=>'form-control'])
                            }}
                            <span class="input-group-btn">
                                {{ Form::button('<i class="fa fa-plus-circle"></i> เพิ่ม', [
                                    'id' => 'check_member',
                                    'class'=>'btn btn-default btn-flat'])
                                }}
                            </span>
                        </div>
                    </div>
                @endif

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 15%;">รหัสสมาชิก</th>
                                <th style="width: 40%;">ชื่อสมาชิก</th>
                                <th style="width: 30%;">ลงทะเบียนเมื่อ</th>
                                <th style="width: 15%;"><i class="fa fa-gear"></i></th>
                            </tr>
                        </thead>
                    </table>
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

    <!-- jQuery-Comfirm CSS -->
    {{ Html::style(elixir('css/jquery-confirm.css')) }}

    @parent

    <style>
    .list-info li {
        padding-top: 0;
        padding-bottom: 4px;
    }
    </style>
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DataTable JavaScript -->
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <!-- jQuery-Comfirm -->
    {{ Html::script(elixir('js/jquery-confirm.js')) }}

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $("[data-mask]").inputmask();

            $('#check_member').click(function () {
                checkMember(parseInt($('#member_id').val(), 10));
            });

            registed();
        });

        function registed() {
            $('#dataTables').dataTable().fnDestroy();
            $('#dataTables').dataTable({
                "ajax": {
                    "url": "/admin/reward/late",
                    "type": "post",
                    "data": {
                        "reward_id": $('#reward_id').val()
                    },
                    beforeSend: function () {
                        $(".ajax-loading").css("display", "block");
                    },
                    complete: function(){
                        $(".ajax-loading").css("display", "none");
                    }       
                },
                "iDisplayLength": 25,
                "order": [[ 2, "desc" ]],
                "createdRow": function(row, data, index) {
                    $(row).find('td:eq(2)').html(monthname($(row).find('td:eq(2)').html()));
                },
                "columns": [
                    { "data": "code" },
                    { "data": "fullname" },
                    { "data": "register_at" },
                    { "data": "action" }
                ]
            });   
        }

        function checkMember(member_id) {
            if (member_id > 0) {
                var formData = new FormData();
                    formData.append('member_id', member_id);

                $.ajax({
                    dataType: 'json',
                    url: '/admin/reward/checkmember',
                    type: 'post',
                    cache: false,
                    data: formData,
                    processData: false,
                    contentType: false,
                    error: function(xhr, ajaxOption, thrownError) {
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    },
                    beforeSend: function() {
                        $(".ajax-loading").css("display", "block");
                    },
                    success: function(member) {
                        $(".ajax-loading").css("display", "none");

                        if (member != false) {
                            $.confirm({
                                icon: 'fa fa-smile-o',
                                title: 'ลงทะเบียน',
                                content: '<div style="width: 100%; margin-top: 15px; text-align: center; font-size: 24px;"><strong>' + member.name + '</strong></div>',
                                type: 'blue',
                                typeAnimated: true,
                                boxWidth: '450px',
                                useBootstrap: false,
                                buttons: {
                                    register: {
                                        text: 'ใช่',
                                        btnClass: 'btn-blue',
                                        action: function () {
                                            addMember(member);
                                        }
                                    },
                                    close: {
                                        text: 'ไม่ใช่',
                                        action: () => {
                                            $('#member_id').val('');
                                        }
                                    }
                                }
                            });
                        }
                        else {
                            $.alert({
                                icon: 'fa fa-warning',
                                title: 'เกิดข้อผิดพลาด!',
                                content: 'ไม่พบรหัสสมาชิกเลขที่ <strong>' + member_id.toString().padStart(5, "0") + '</strong>',
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    close: {
                                        text: 'ปิด',
                                        btnClass: 'btn-red',
                                        action: () => {
                                            $('#member_id').val('');
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
            }
        }

        function addMember(member) {
            var formData = new FormData();
                formData.append('member_id', member.id);
                formData.append('reward_id', $('#reward_id').val());

            $.ajax({
                dataType: 'json',
                url: '/admin/reward/addmember',
                type: 'post',
                cache: false,
                data: formData,
                processData: false,
                contentType: false,
                error: function(xhr, ajaxOption, thrownError) {
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function(success) {
                    if (success != false) {
                        registed();
                        $('#member_id').val('');
                        $(".ajax-loading").css("display", "none");
                    }
                    else {
                        $.alert({
                            icon: 'fa fa-warning',
                            title: 'เกิดข้อผิดพลาด!',
                            content: 'มีข้อมูลการลงทะเบียนแล้ว!!!',
                            type: 'red',
                            typeAnimated: true,
                            buttons: {
                                close: {
                                    text: 'ปิด',
                                    btnClass: 'btn-red',
                                    action: () => {
                                        $('#member_id').val('');
                                    }
                                }
                            }
                        });
                    }
                }
            });
        }

        function deleteMember(member_id, member_name) {
            $.confirm({
                icon: 'fa fa-warning',
                title: 'ลบข้อมูลลงทะเบียน',
                content: 'คุณต้องการลบข้อมูลของ <strong>' + member_name + '</strong> ใช่หรือไม่?',
                type: 'red',
                typeAnimated: true,
                buttons: {
                    register: {
                        text: 'ใช่',
                        btnClass: 'btn-red',
                        action: function () {
                            var formData = new FormData();
                                formData.append('member_id', member_id);
                                formData.append('reward_id', $('#reward_id').val());

                            $.ajax({
                                dataType: 'json',
                                url: '/admin/reward/deletemember',
                                type: 'post',
                                cache: false,
                                data: formData,
                                processData: false,
                                contentType: false,
                                error: function(xhr, ajaxOption, thrownError) {
                                    console.log(xhr.responseText);
                                    console.log(thrownError);
                                },
                                beforeSend: function() {
                                    $(".ajax-loading").css("display", "block");
                                },
                                success: function(success) {
                                    registed();
                                    $(".ajax-loading").css("display", "none");
                                }
                            });
                        }
                    },
                    close: {
                        text: 'ไม่ใช่',
                        action: () => {}
                    }
                }
            });
        }

        function monthname(str) {
            return str.replace('Jan', 'ม.ค.')
                .replace('Feb', 'ก.พ.')
                .replace('Mar', 'มี.ค.')
                .replace('Apr', 'เม.ย.')
                .replace('May', 'พ.ค.')
                .replace('Jun', 'มิ.ย.')
                .replace('Jul', 'ก.ค.')
                .replace('Aug', 'ส.ค.')
                .replace('Sep', 'ก.ย.')
                .replace('Oct', 'ต.ค.')
                .replace('Nov', 'พ.ย.')
                .replace('Dec', 'ธ.ค.'); 
        }
    </script>
@endsection