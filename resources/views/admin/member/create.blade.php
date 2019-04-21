@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => action('Admin\MemberController@index')],
            ['item' => 'เพิ่ม', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข้อมูลสมาชิกสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ตั้งรหัสผ่านใหม่ และกำหนดสิทธิ์การเข้าถึงข้อมูลสมาชิกสหกรณ์</p>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <!-- Horizontal Form -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-user-plus"></i> เพิ่มสมาชิกสหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['action' => 'Admin\MemberController@store', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.member.form', ['edit' => false])
            {{ Form::close() }}
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

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <!-- Custom JavaScript -->
    {!! Html::script(elixir('js/member-form.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $("[data-mask]").inputmask();
        $('form').submit(function() {
            $("[data-mask]").inputmask('remove');
        });

        $('#start_date').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: false,
            focusOnShow: false,
            buddhismEra: true
        });

        $('#birth_date').datetimepicker({
            locale: moment.locale('th'),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            useCurrent: false,
            focusOnShow: false,
            buddhismEra: true
        });

        $('#employee_code').blur(function() {
            if (parseInt($('#employee_code').val(), 10) != 0) {
                $.ajax({
                    url: '/ajax/status',
                    type: "post",
                    data: {
                        'code': $('#employee_code').val()
                    },
                    success: function (data) {
                        $('#fee').val(data.message);

                        if (data.message == 'ยังคงเป็นสมาชิกอยู่'){
                            $('#save').addClass('disabled');
                            $('#save').prop('disabled', true);
                        }
                        else {
                            if (data.message == '200') {
                                $('#prefix').val(data.member.profile.prefix_id);
                                $('#name').val(data.member.profile.name);
                                $('#lastname').val(data.member.profile.lastname);
                                $('#citizen_code').val(data.member.profile.citizen_code);
                                $('#employee_type').val(data.member.employee.employee_type_id);
                                $('#birth_date').datetimepicker({
                                    defaultDate: data.member.profile.birth_date,
                                    viewMode: 'days',
                                    format: 'YYYY-MM-DD'
                                });
                                $('#address').val(data.member.profile.address);
                                $('#province_id').val(data.member.profile.province_id);
                                $('#district_id').empty();
                                $.each(data.member.districts, function (i, item) {
                                    $('#district_id').append($('<option>', { 
                                        value: item.id,
                                        text : item.name 
                                    }));
                                });
                                $('#district_id').val(data.member.profile.district_id);
                                $('#subdistrict_id').empty();
                                $.each(data.member.subdistricts, function (i, item) {
                                    $('#subdistrict_id').append($('<option>', { 
                                        value: item.id,
                                        text : item.name 
                                    }));
                                });
                                $('#subdistrict_id').val(data.member.profile.subdistrict_id);
                                $('#postcode').val(data.member.postcode);
                            }

                            $('#save').removeClass('disabled');
                            $('#save').prop('disabled', false);
                        }
                    }
                });
            }
        });
    });
    </script>
@endsection