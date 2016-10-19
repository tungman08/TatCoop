@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการผู้ดูแลระบบ
        <small>เพิ่ม ลบ แก้ไข บัญชีของผู้ดูแลระบบ สอ.สรทท.</small>
    </h1>

    @include('admin.administrator.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการผู้ดูแลระบบ', 'link' => '/admin/administrator'],
        ['item' => 'คืนค่า', 'link' => ''],
    ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="well">
            <h4>คืนค่าข้อมูลผู้ใช้งานระบบ</h4>
            <p>คืนค่าให้กับบัญชีผู้ใช้งานระบบที่ถูกลบไปแล้ว</p>
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
                <h3 class="box-title">บัญชีผู้ใช้งานระบบ</h3>
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
                                <th style="width: 15%;">ชื่อผู้ใช้</th>
                                <th style="width: 15%;">วันที่ลบ</th>
                                <th style="width: 27%;">การเข้าถึงข้อมูล</th>
                                <th style="width: 8%;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $index => $user)
                            <tr style="cursor: pointer;">
                                <td class="display-number">{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-user fa-fw"></i> {{ $user->email }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ Diamond::parse($user->deleted_date)->thai_format('j F Y H:i น.') }}</td>
                                <td>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        {{ Form::button('<i class="fa fa-rotate-left"></i>', [
                                            'class'=>'btn btn-default btn-xs btn-flat', 
                                            'onclick'=>'javascript:var result = confirm(\'คุณต้องการคืนค่าให้ ' . $user->email . ' ?\'); if (result) { document.location = \'' . url('/admin/administrator/' . $user->id . '/undelete') . '\'; }']) }}
                                        {{ Form::button('<i class="fa fa-trash"></i>', [
                                            'class'=>'btn btn-default btn-xs btn-flat', 
                                            'onclick'=>'javascript:var result = confirm(\'คุณต้องการลบ ' . $user->email . ' ออกจากระบบ ?\'); if (result) { document.location = \'' . url('/admin/administrator/' . $user->id . '/forcedelete') . '\'; }']) }}
                                    </div>
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
        });

        $('#dataTables-users').dataTable({
            "iDisplayLength": 25
        });
    </script>
@endsection