@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            บัญชีสมาชิกสหกรณ์
            <small>รายชื่อบัญชีของสมาชิกที่ใช้งานระบบบริการอิเล็กทรอนิกส์</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'บัญชีสมาชิกสหกรณ์', 'link' => '/admin/account'],
            ['item' => $user->member->profile->fullname, 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>บัญชีสมาชิกสหกรณ์ที่ใช้งานระบบบริการอิเล็กทรอนิกส์</h4>
            <p>แสดงรายชื่อบัญชีของสมาชิกที่ได้ลงทะเบียนเข้าใช้งานระบบบริการอิเล็กทรอนิกส์</p>
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
                <h3 class="box-title"><i class="fa fa-user-circle-o"></i> บัญชีสมาชิกสหกรณ์ที่ใช้งานระบบบริการอิเล็กทรอนิกส์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-info">
                        <tr>
                            <th style="width:20%; border-top: 1px solid #ffffff;">ชื่อผู้สมาชิก:</th>
                            <td style="border-top: 1px solid #ffffff;">{{ $user->member->profile->fullname }}</td>
                        </tr>
                        <tr>
                            <th>อีเมล:</th>
                            <td>{{ $user->email }}</td>
                        </tr>    
                        <tr>
                            <th>ลงทะเบียนเมื่อ:</th>
                            <td>{{ Diamond::parse($user->created_at)->thai_format('j M Y') }}</td>
                        </tr>  
                        <tr>
                            <th>สถานะ:</th>
                            <td>{!! ($user->confirmed == true) ? '<span class="label label-success">ปกติ</span>' : '<span class="label label-warning">ยังไม่ได้ยืนยันตัวตน</span>' !!}</td>
                        </tr>       
                    </table>
                    <!-- /.table -->
                </div>  
                <!-- /.table-responsive --> 

                <button class="btn btn-primary btn-flat margin-b-md" type="button"
                    onclick="javascript:document.location.href='{{ url('/admin/account/' . $user->member->id . '/edit') }}';">
                    <i class="fa fa-edit"></i> แก้ไขอีเมลบัญชีผู้ใช้
                </button>

                @if ($user->newaccount)
                    <button id="unlock" class="btn btn-primary btn-flat margin-b-md" type="button"
                        onclick="javascript: var result = confirm('เนื่องจากมีการเปลี่ยนแปลงบัญชีผู้ใช้ จึงจำเป็นที่ต้องล็อคปุ่มลืมรหัสผ่าน\nคุณต้องการปลดล็อคปุ่มลืมรหัสผ่านให้บัญชีผู้ใช้นี้ใช่หรือไม่?'); if (result) { unlock({{ $user->id }}); }">
                        <i class="fa fa-unlock-alt"></i> ปลดล็อคปุ่มลืมรหัสผ่าน
                    </button>
                @endif
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-envelope"></i> ประวัติการเปลี่ยนอีเมลบัญชีผู้ใช้</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-email" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 20%;">อีเมล</th>
                                <th style="width: 30%;">ยกเลิกเมื่อ</th>
                                <th style="width: 40%;">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->old_emails->sortByDesc('canceled_at') as $index => $email)
                            <tr>
                                <td>{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-envelope fa-fw"></i> {{ $email->email }}</td>
                                <td>{{ Diamond::parse($email->canceled_at)->thai_format('j M Y') }}</td>
                                <td>{{ $email->remark }}</td>
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

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-clock-o"></i> ประวัติการเข้าใช้งานระบบ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-history" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 20%;">วันที่</th>
                                <th style="width: 30%;">ประวัติการเข้าใช้งาน</th>
                                <th style="width: 40%;">คำอธิบาย</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($user->user_histories->sortByDesc('created_at') as $history)
                                <tr>
                                    <td>{{ ++$count }}.</td>
                                    <td class="text-primary">
                                        <i class="fa fa-clock-o fa-fw"></i> {{ Diamond::parse($history->created_at)->thai_format('j M Y') }} เวลา {{ Diamond::parse($history->created_at)->thai_format('H:i') }} น.
                                    </td>
                                    <td>{{ $history->history_type->name }}</td>
                                    <td>
                                        @if (!empty($history->description))
                                            <span>{{ $history->description }}</span>
                                        @else
                                            <span>-</span>
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

            $('#dataTables-email').dataTable({
                "iDisplayLength": 10
            });

            $('#dataTables-history').dataTable({
                "iDisplayLength": 10
            });
        });

        function unlock(id) {
            $.ajax({
                url: '/ajax/unlockresetpassword',
                type: "post",
                data: {
                    'id': id
                },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function() {
                    $('#unlock').remove();
                }
            });
        }
    </script>
@endsection