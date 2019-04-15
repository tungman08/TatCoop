@extends('website.member.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ข้อมูลสมาชิก
            <small>รายละเอียดของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('website.member.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลสมาชิก', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลสมาชิกสหกรณ์</h4>
            <p>ข้อมูลของ {{ $member->profile->fullname }}</p>
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

        <!-- change password flash session data -->
        @if (session('password_changed'))
            <div class="callout callout-success">
                <h4>{{ session('password_changed') }}</h4>
                <p>การเปลี่ยนรหัสผ่านเสร็จสมบูรณ์แล้ว คุณสามารถใช้รหัสผ่านใหม่ในการเข้าสู่ระบบงานในครั้งถัดไป</p>
            </div>
        @endif

        <div class="row">
            <div class="col-md-9">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">รายละเอียดข้อมูลสมาชิก</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <button class="btn btn-primary btn-flat"
                            style="margin-bottom: 10px;"
                            title="แก้ไขข้อมูล"
                            onclick="javascript:document.location.href = '{{ action('Website\ProfileController@getEdit') }}';">
                            <i class="fa fa-edit"></i> แก้ไขข้อมูล
                        </button>

                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width:20%; border-top: none;">หมายเลขสมาชิก:</th>
                                    <td style="border-top: none;">{{ $member->member_code }}</td>
                                </tr>
                                <tr>
                                    <th>ชื่อ:</th>
                                    <td>
                                        {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullname }} 
                                        {!! !is_null($member->leave_date) ? ' <span class="text-danger">(ออกจากสมาชิกแล้ว)<span>' : '' !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>หมายเลขบัตรประชาชน:</th>
                                    <td>{{ $member->profile->citizen_code }}</td>
                                </tr>
                                <tr>
                                    <th>รหัสพนักงาน:</th>
                                    <td>{{ $member->profile->employee->code }}</td>
                                </tr>
                                <tr>
                                    <th>ประเภทสมาชิก:</th>
                                    <td>{{ $member->profile->employee->employee_type->name }}</td>
                                </tr>
                                <tr>
                                    <th>จำนวนหุ้นต่อเดือน:</th>
                                    <td>{{ number_format($member->shareholding, 0,'.', ',') }} หุ้น ({{ number_format($member->shareholding * 10, 2,'.', ',') }} บาท)</td>
                                </tr>
                                <tr>
                                    <th>ทุนเรือนหุ้นสะสม:</th>
                                    <td>{{ number_format($member->shareholdings()->sum('amount'), 2,'.', ',') }} บาท</td>
                                </tr>
                                <tr>
                                    <th>วันเกิด:</th>
                                    <td>{{ (!empty($member->profile->birth_date)) ? Diamond::parse($member->profile->birth_date)->thai_format('j F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ที่อยู่:</th>
                                    <td>{{ ($member->profile->address == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullAddress }}</td>
                                </tr>
                                <tr>
                                    <th>เป็นสมาชิกเมื่อ:</th>
                                    <td>{{ Diamond::parse($member->start_date)->thai_format('j F Y') }}</td>
                                </tr>
                                @if (!is_null($member->leave_date))
                                    <tr>
                                        <th>ออกจากสมาชิกเมื่อ:</th>
                                        <td>{{ Diamond::parse($member->leave_date)->thai_format('j F Y') }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>ประวัติการเป็นสมาชิก:</th>
                                    <td style="padding: 0px;">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>หมายเลขสมาชิก</th>
                                                    <th>สมัครเป็นสมาชิกเมื่อ</th>
                                                    <th>ออกจากสมาชิกเมื่อ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($member_histories as $history)
                                                    <tr>
                                                        <td>{{ $history->memberCode }}</td>
                                                        <td>{{ Diamond::parse($history->start_date)->thai_format('j M Y') }}</td>
                                                        <td>{{ is_null($history->leave_date) ? '-' : Diamond::parse($history->leave_date)->thai_format('j M Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

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
                                <span class="pull-right display-number">
                                    {{ Diamond::parse($user->user_statistics->max('create_at'))->thai_format('j M Y') }}
                                </span>
                            </li>
                        </ul>
                        <a href="{{ action('Website\ProfileController@getPassword') }}" class="btn btn-primary btn-block btn-flat"><b>เปลี่ยนรหัสผ่าน</b></a>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

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
                    @include('website.profile.timeline')
                </div>
            </div>          
            <!-- /.box-body-->
        <div>
        <!-- /.box -->

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
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('[data-tooltip="true"]').tooltip();
        });

        function loadmore(index) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/loadmore',
                type: 'post',
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