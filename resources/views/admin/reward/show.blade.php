@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จับรางวัล
            <small>สุ่มจับรางวัลให้กับสมาชิกสหกรณ์</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จับรางวัล', 'link' => action('Admin\RewardController@index')],
            ['item' => 'รายชื่อผู้ได้รับรางวัล', 'link' => '']
        ]])
    </section>


    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>จับรางวัล</h4>    
            <p>ให้เจ้าหน้าที่สามารถสุ่มจับรางวัลให้กับสมาชิกสหกรณ์ เนื่องในโอกาสพิเศษ</p>  
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-smile-o"></i> การจับรางวัล วันที่ {{ Diamond::parse($reward->created_at)->thai_format('j M Y') }} โดย {{ $reward->admin->fullname }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                {{ Form::open(['action' => ['Admin\RewardController@destroy', $reward->id], 'method' => 'delete', 'role' => 'form', 'onsubmit' => "return confirm('คุณต้องการลบข้อมูลนี้ใช่ไหม?');"]) }}
                    {{ Form::button('<i class="fa fa-trash"></i> ลบข้อมูล', [
                        'id'=>'delete',
                        'type' => 'submit', 
                        'class'=>'btn btn-danger btn-flat margin-b-md'])
                    }}
                {{ Form::close() }}

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 20%;">รหัสสมาชิก</th>
                                <th style="width: 30%;">ชื่อผู้โชคดี</th>
                                <th style="width: 20%;">สถานะ</th>
                                <th style="width: 20%;">เวลา</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reward->winners->sortBy('created_at') as $index => $winner) 
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ $winner->member->memberCode }}</td>
                                    <td class="text-primary"><i class="fa fa-smile-o fa-fw"></i> {{ $winner->member->profile->name . ' ' . $winner->member->profile->lastname }}</td>
                                    <td>{!! ($winner->status) ? '<span class="label label-primary">รับรางวัล</span>' : '<span class="label label-danger">สละสิทธิ์</span>' !!}</td>
                                    <td>{{ Diamond::parse($winner->created_at)->thai_format('j M Y เวลา H:i') }}</td>
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
                "iDisplayLength": 25
            });
        });
    </script>
@endsection