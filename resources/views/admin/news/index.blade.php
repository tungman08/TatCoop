@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการข่าวสารสำหรับสมาชิก
            <small>การจัดการข่าวสารสำหรับสมาชิกของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการข่าวสารสำหรับสมาชิก', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข่าวสารสำหรับสมาชิก</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ข่าวสารสำหรับสมาชิก</p>
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

        <!-- Box content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-newspaper-o"></i> ข่าวสารสำหรับสมาชิก</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat margin-b-md" type="button" data-tooltip="true" title="เพิ่มข่าวสารสำหรับสมาชิก"
                    onclick="javascript:document.location.href='{{ action('Admin\NewsController@create') }}';">
                    <i class="fa fa-plus-circle"></i> เพิ่มข่าวสารสำหรับสมาชิก
                </button>
                <button class="btn btn-default btn-flat margin-b-md pull-right" type="button" data-tooltip="true" title="ข่าวสารสำหรับสมาชิกที่ถูกลบ"
                    onclick="javascript:document.location.href='{{ action('Admin\NewsController@getInactive') }}';">
                    <i class="fa fa-trash"></i> แสดงข่าวสารสำหรับสมาชิกที่ถูกลบ
                </button>  

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-news" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 75%;">หัวข้อข่าวสารสำหรับสมาชิก</th>
                                <th style="width: 20%;">สร้างเมื่อ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newses as $index => $news)
                            <tr onclick="javascript: document.location.href  = '{{ action('Admin\NewsController@show', ['id'=>$news->id]) }}';"
                                style="cursor: pointer;">
                                <td>{{ $index + 1 }}.</td>
                                <td class="text-primary"><i class="fa fa-newspaper-o fa-fw"></i> {{ $news->title }}</td>
                                <td>{{ Diamond::parse($news->created_at)->thai_format('Y-m-d') }}</td>
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

        $('#dataTables-news').dataTable({
            "iDisplayLength": 25
        });       
    });   
    </script>
@endsection