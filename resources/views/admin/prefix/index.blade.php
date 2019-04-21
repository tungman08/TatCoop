@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            คำนำหน้านาม
            <small>จัดการคำนำหน้านามที่ใช้ในระบบ</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'คำนำหน้านาม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>คำนำหน้านาม</h4>
            <p>ให้ผู้ดูแลระบบสามารถเพิ่ม ลบ แก้ไข คำนำหน้านามที่ใช้ในระบบได้</p>
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
                <h3 class="box-title"><i class="fa fa-th-large"></i> คำนำหน้านาม</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat margin-b-md" type="button"
                    onclick="javascript:document.location.href='{{ action('Admin\PrefixController@create') }}';">
                    <i class="fa fa-plus-circle"></i> เพิ่มคำนำหน้านาม
                </button>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 45%;">คำนำหน้านาม</th>
                                <th style="width: 45%;">ใช้อยู่</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prefixs as $index => $prefix)
                                <tr onclick="javascript: document.location.href  = '{{ action('Admin\PrefixController@edit', ['id' => $prefix->id]) }}';" style="cursor: pointer;">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary">{{ $prefix->name }}</td>
                                    <td>{{ number_format($prefix->profiles->count(), 0, '.', ',') }} คน</td>
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
        $('#dataTables').dataTable({
            "iDisplayLength": 25,
            "columnDefs": [
                { type: 'formatted-num', targets: 2 }
            ]
        }); 
    });  

    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "formatted-num-pre": function ( a ) {
            a = (a === "-" || a === "") ? 0 : a.replace(/[^\d\-\.]/g, "");
            return parseFloat( a );
        },

        "formatted-num-asc": function ( a, b ) {
            return a - b;
        },

        "formatted-num-desc": function ( a, b ) {
            return b - a;
        }
    });  
    </script>  
@endsection