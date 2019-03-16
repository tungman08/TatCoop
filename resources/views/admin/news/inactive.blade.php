@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการข่าวสารสำหรับสมาชิก
        <small>การจัดการข่าวสารสำหรับสมาชิกของ สอ.สรทท.</small>
    </h1>

    @include('admin.layouts.breadcrumb', ['breadcrumb' => [
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
                                <td>{{ Diamond::parse($inactive->created_at)->thai_format('Y-m-d') }}</td>
                                <td>{{ Diamond::parse($inactive->deleted_at)->thai_format('Y-m-d') }}</td>
                                <td>
                                    {{ Form::open(['url' => '/website/news/' . $inactive->id . '/restore', 'method' => 'post', 'class' => 'btn-group']) }}
									    {{ Form::button('<i class="fa fa-search"></i>', [
                                            'type' => 'button',
                                            'class'=>'btn btn-default btn-xs btn-flat',
											'data-tooltip'=>true,
											'title'=>'รายละเอียด',
                                            'onclick'=>"javascript:document.location.href='/website/news/inactive/" . $inactive->id . "';"])
                                        }}
                                        {{ Form::button('<i class="fa fa-rotate-left"></i>', [
                                            'type' => 'submit',
                                            'class'=>'btn btn-default btn-xs btn-flat',
											'class'=>'btn btn-default btn-xs btn-flat',
											'data-tooltip'=>true,
											'title'=>'กู้คืน',
                                            'onclick'=>"javascript:return confirm('คุณต้องการคืนค่าข่าวประชาสัมพันธ์นี้ใช่หรือไม่?');"])
                                        }}
										{{ Form::hidden('hidden1-' . $inactive->id, null, ['class' => 'btn']) }}
                                    {{ Form::close() }}

                                    {{ Form::open(['url' => '/website/news/' . $inactive->id . '/forcedelete', 'method' => 'post', 'class' => 'btn-group']) }}
										{{ Form::hidden('hidden-' . $inactive->id, null, ['class' => 'btn']) }}
                                        {{ Form::button('<i class="fa fa-trash"></i>', [
                                            'type' => 'submit',
                                            'class'=>'btn btn-default btn-xs btn-flat', 
											'data-tooltip'=>true,
											'title'=>'ลบข้อมูลแบบถาวร',
                                            'onclick'=>"javascript:return confirm('คุณต้องการลบข่าวประชาสัมพันธ์นี้ออกจากระบบใช่หรือไม่?');"])
                                        }}
                                    {{ Form::close() }}
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

	<style>
		form.btn-group + form.btn-group {
			margin-left: -5px;
		}
	</style>

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