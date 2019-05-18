@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการคณะกรรมการ
            <small>เพิ่ม ลบ แก้ไข บัญชีของคณะกรรมการ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการคณะกรรมการ', 'link' => action('Admin\BoardController@index')],
            ['item' => 'ข้อมูลคณะกรรมการ', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการข้อมูลคณะกรรมการ</h4>
            <p>ให้ผู้ดูแลระบบสามารถ เพิ่ม ลบ แก้ไข ตั้งรหัสผ่านใหม่ และกำหนดสิทธิ์การเข้าถึงข้อมูล ของคณะกรรมการทั้งหมด</p>
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
                <h3 class="box-title"><i class="fa fa-user-circle-o"></i> บัญชีคณะกรรมการ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-info">
                        <tr>
                            <th style="width:20%; border-top: 1px solid #ffffff;">ชื่อผู้สมาชิก:</th>
                            <td style="border-top: 1px solid #ffffff;">{{ $board->fullname }}</td>
                        </tr>
                        <tr>
                            <th>อีเมล:</th>
                            <td>{{ $board->email }}</td>
                        </tr>    
                        <tr>
                            <th>ลงทะเบียนเมื่อ:</th>
                            <td>{{ Diamond::parse($board->created_at)->thai_format('j M Y') }}</td>
                        </tr>      
                    </table>
                    <!-- /.table -->
                </div>  
                <!-- /.table-responsive --> 
                
                <button class="btn btn-primary btn-flat"
                    onclick="javascript:document.location.href = '{{ action('Admin\BoardController@edit', ['id' => $board->id]) }}';">
                    <i class="fa fa-edit"></i> แก้ไขข้อมูล
                </button>
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
                            @foreach($board->admin_histories->sortByDesc('created_at')->take(300) as $history)
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
            $('#dataTables-history').dataTable({
                "iDisplayLength": 10
            });
        });
    </script>
@endsection