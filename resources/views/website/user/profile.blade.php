@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลผู้ใช้งาน
        <small>รายละเอียดข้อมูลผู้ใช้งานระบบ</small>
    </h1>

    @include('website.user.breadcrumb', ['breadcrumb' => 'ข้อมูลผู้ใช้งาน'])

    </section>

    <!-- Main content -->
    <section class="content">

        <!-- change password flash session data -->
        @if (session('password_changed'))
            <div class="callout callout-success">
                <h4>{{ session('password_changed') }}</h4>
                <p>การเปลี่ยนรหัสผ่านเสร็จสมบูรณ์แล้ว คุณสามารถใช้รหัสผ่านใหม่ในการเข้าสู่ระบบงานในครั้งถัดไป</p>
            </div>
        @endif

        <div class="row">
            <div class="col-md-3">

                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('images/user.png') }}" alt="User profile picture">
                        <h3 class="profile-username text-center">{{ $user->name }}</h3>
                        <p class="text-muted text-center">บัญชีผู้ใช้: {{ $user->email }}</p>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>เข้าระบบครั้งล่าสุด</b> 
                                <a class="pull-right display-number">
                                    {{ Diamond::parse($user->user_statistics->max('create_at'))->thai_format('j M Y') }}
                                </a>
                            </li>
                        </ul>
                        <a href="{{ url('/user/password') }}" class="btn btn-primary btn-block"><b>เปลี่ยนรหัสผ่าน</b></a>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">บันทึกการปฏิบัติงาน</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-title -->

                    <div class="box-body">
                        <div class="panel-body">
                            @include('website.user.timeline')
                        </div>
                    </div>          
                    <!-- /.box-body-->
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
@endsection

@section('scripts')
    @parent

    {{ Html::script(elixir('js/moment.js')) }}

    <script>
        $(document).ready(function () {
            $('[data-tooltip="true"]').tooltip();
        });

        function loadmore(index) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/loadmore',
                type: 'get',
                cache: false,
                data: {
                    'index': index
                },
                error: function(xhr, ajaxOption, thrownError) {
                    console.log(xhr.responseText);
                    console.log(thrownError);
                },
                success: function(obj) {
                    $('#end').remove();
                    moment.locale('th')
                    var history = obj.histories[0];

                    var item = '<li class="time-label">';
                        item += '<span class="bg-red">' + moment(history.date.date).add(543, 'years').format('D MMM YYYY') + '</span>';
                        item += '</li>';

                        $.each(history.items, function(i, action) {
                            item += '<li><i class="fa ' + action.history_type.icon + ' ' + action.history_type.color + '"></i>';

                            if (action.description == null) {
                                item += '<div class="timeline-item">';
                                item += '<span class="time"><i class="fa fa-clock-o"></i> ';
                                item += moment(action.created_at).add(543, 'years').format('D MMM YYYY');
                                item += '</span>';
                                item += '<h3 class="timeline-header no-border">' + action.history_type.name + '</h3>';
                                item += '</div>';
                            }
                            else {
                                item += '<div class="timeline-item">';
                                item += '<span class="time"><i class="fa fa-clock-o"></i> ';
                                item += moment(action.created_at).add(543, 'years').format('D MMM YYYY');
                                item += '</span>';
                                item += '<h3 class="timeline-header no-border">' + action.history_type.name + '</h3>';
                                item += '<div class="timeline-body">' + action.description + '</div>';
                                item += '</div>';
                            }

                            item += '</li>';
                        });

                        item += '<li id="end">';
                        item += '<i class="fa fa-clock-o bg-gray"></i>';

                        if (obj.index < obj.count - 1) {
                            item += '<div class="timeline-item" style="border: none; background: none;">';
                            item += '<button id="more" onclick="javascript:loadmore(' + (obj.index + 1) + ');" data-tooltip="true" title="Load more...">...</button>';
                            item += '</div>';
                        }

                        item += '</li>';

                    $('.timeline').append(item);
                }
            });
        }
    </script>
@endsection