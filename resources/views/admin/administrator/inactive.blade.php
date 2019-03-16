@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเจ้าหน้าที่สหกรณ์
            <small>เพิ่ม ลบ แก้ไข บัญชีของเจ้าหน้าที่สหกรณ์ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเจ้าหน้าที่สหกรณ์', 'link' => '/admin/administrator'],
            ['item' => 'ถูกลบ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="well">
            <h4>แสดงข้อมูลเจ้าหน้าที่สหกรณ์ที่ถูกลบ</h4>
            <p>เจ้าหน้าที่สหกรณ์สามารถคืนค่าให้กับบัญชีผู้ใช้งานระบบที่ถูกลบไปแล้ว</p>
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
                <h3 class="box-title">บัญชีเจ้าหน้าที่สหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-default btn-flat" style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.history.go(-1);">
                    <i class="fa fa-reply"></i> ถอยกลับ
                </button>

                <div class="table-responsive">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 30%;">บัญชีผู้ใช้</th>
                                <th style="width: 20%;">ชื่อผู้ใช้</th>
                                <th  style="width: 15%;">สร้างเมื่อ</th>
                                <th style="width: 15%;">ลบเมื่อ</th>
                                <th style="width: 15%;"><i class="fa fa-gear"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $index => $user)
                            <tr style="cursor: pointer;">
                                <td class="display-number">{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-user fa-fw"></i> {{ $user->email }}</td>
                                <td>{{ $user->fullname }}</td>
                                <td>{{ Diamond::parse($user->created_at)->thai_format('j F Y H:i น.') }}</td>
                                <td>{{ Diamond::parse($user->deleted_date)->thai_format('j F Y H:i น.') }}</td>
                                <td>
                                    <div class="btn-group">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-flat btn-xs"
                                            onclick="javascript: restore({{ $user->id }});">
                                            <i class="fa fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-flat btn-xs"
                                            onclick="javascript: forcedelete({{ $user->id }});" disabled>
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ Form::open(['action' => ['Admin\AdminController@postRestore', 0], 'id' => 'restore_form', 'method' => 'post', 'role' => 'form', 'onsubmit' => "return confirm('คุณต้องการคืนค่าเจ้าหน้าที่ใช่หรือไม่??');"]) }}
                    {{ Form::close() }}

                    {{ Form::open(['action' => ['Admin\AdminController@postForceDelete', 0], 'id' => 'forcedelete_form', 'method' => 'post', 'role' => 'form', 'onsubmit' => "return confirm('คุณต้องการลบเจ้าหน้าที่นี้ใช่ไหม?');"]) }}
                    {{ Form::close() }}
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
            $('[data-tooltip="true"]').tooltip();
            
            $('#dataTables-users').dataTable({
                "iDisplayLength": 25
            });
        });

        function restore(id) {
            let url = $("#restore_form").attr("action");
            let temp = url.substr(0, url.lastIndexOf("/"));
            let action = temp.substr(0, temp.lastIndexOf("/") + 1) + id + "/restore";

            $("#restore_form").attr("action", action);
            $('#restore_form').submit();
        }

        function forcedelete(id) {
            let url = $("#forcedelete_form").attr("action");
            let temp = url.substr(0, url.lastIndexOf("/"));
            let action = temp.substr(0, temp.lastIndexOf("/") + 1) + id + "/forcedelete";

            $("#forcedelete_form").attr("action", action);
            $('#forcedelete_form').submit();
        }
    </script>
@endsection