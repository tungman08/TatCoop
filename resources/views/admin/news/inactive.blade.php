@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการข่าวสารสำหรับสมาชิก
        <small>การจัดการข่าวสารสำหรับสมาชิกของ สอ.สรทท.</small>
    </h1>

    @include('admin.news.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการข่าวสารสำหรับสมาชิก', 'link' => '/website/news'],
        ['item' => 'ข่าวสารสำหรับสมาชิกที่ถูกลบ', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข่าวสารสำหรับสมาชิกที่ถูกลบ</h4>
            <p>ให้ผู้ดูแลระบบ ลบถาวรหรือคืนสภาพข่าวสารสำหรับสมาชิกที่ถูกลบ</p>
        </div>

        <!-- Box content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-newspaper-o"></i> ข่าวสารสำหรับสมาชิก</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-default btn-flat margin-b-md"
                    onclick="javascript:window.history.go(-1);">
                    <i class="fa fa-reply"></i> ถอยกลับ
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-inactive" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 65%;">หัวข้อสาระน่ารู้</th>
                                <th style="width: 10%;">สร้างเมื่อ</th>
                                <th style="width: 10%;">ลบเมื่อ</th>
                                <th style="width: 10%;"><i class="fa fa-gear"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inactives as $index => $inactive)
                            <tr>
                                <td>{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-newspaper-o fa-fw"></i> {{ $inactive->title }}</td>
                                <td>{{ Diamond::parse($inactive->created_at)->thai_format('j M Y') }}</td>
                                <td>{{ Diamond::parse($inactive->deleted_at)->thai_format('j M Y') }}</td>
                                <td>
                                    <a style="cursor: pointer;"
                                        onclick="javascript:result = confirm('คุณต้องการคืนสภาพรายการนี้ใช่ไหม ?'); if (result) { window.location.href='/website/news/{{ $inactive->id }}/restore'; }">คืนสภาพ</a> / 
                                    <a style="cursor: pointer;"
                                        onclick="javascript:result = confirm('คุณต้องการลบรายการนี้อย่างถาวรใช่ไหม ?'); if (result) { window.location.href='/website/news/{{ $inactive->id }}/delete'; }">ลบถาวร</a> 
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
        $('[data-tooltip="true"]').tooltip();
    });

    $('#dataTables-inactive').dataTable({
        "iDisplayLength": 25
    });
    </script>
@endsection