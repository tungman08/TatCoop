@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการผู้ดูแลระบบ
        <small>เพิ่ม ลบ แก้ไข บัญชีของผู้ดูแลระบบ สอ.สรทท.</small>
    </h1>

    @include('admin.administrator.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการผู้ดูแลระบบ', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข้อมูลผู้ดูแลระบบ</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ตั้งรหัสผ่านใหม่ และกำหนดสิทธิ์การเข้าถึงข้อมูล ของผู้ดูแลระบบทั้งหมด</p>
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
                <h3 class="box-title"><i class="fa fa-user-circle-o"></i> บัญชีผู้ใช้งานระบบ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat" style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.location.href='{{ url('/admin/administrator/create') }}';">
                    <i class="fa fa-user-plus"></i> เพื่อบัญชีผู้ดูแลระบบ
                </button>
                <button class="btn btn-default btn-flat pull-right" style="margin-top: 15px; margin-bottom: 15px;"
                    onclick="javascript:window.location.href='{{ url('/admin/administrator/restore') }}';">
                    <i class="fa fa-trash"></i> ผู้ดูแลระบบที่ถูกลบ
                </button>

                <div class="table-responsive">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 30%;">บัญชีผู้ใช้</th>
                                <th style="width: 30%;">ชื่อผู้ใช้</th>
                                <th style="width: 35%;">สร้างเมื่อ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $index => $adminx)
                            <tr onclick="javascript: document.location = '{{ url('/admin/administrator/' . $adminx->id . '/edit') }}';"
                                style="cursor: pointer;">
                                <td>{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-user-secret fa-fw"></i> {{ $adminx->email }}</td>
                                <td>{{ $adminx->name }}</td>
                                <td>{{ Diamond::parse($adminx->created_at)->thai_format('j M Y') }}</td>
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
        $('[data-tooltip="true"]').tooltip();
    });

    $('#dataTables-users').dataTable({
        "iDisplayLength": 25
    });
    </script>
@endsection