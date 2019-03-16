@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จับรางวัล
            <small>สุ่มจับรางวัลให้กับสมาชิกสหกรณ์</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จับรางวัล', 'link' => '']
        ]])
    </section>


    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>จับรางวัล</h4>    
            <p>ให้เจ้าหน้าที่สามารถสุ่มจับรางวัลให้กับสมาชิกสหกรณ์ เนื่องในโอกาสพิเศษ</p>  
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
                <h3 class="box-title"><i class="fa fa-smile-o"></i> การจับรางวัล</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <a class="btn btn-primary btn-flat margin-b-md" href="{{ action('Admin\RewardController@getSlotmachine') }}" target="_blank">
                    <i class="fa fa-smile-o"></i> สุ่มจับรางวัล
                </a>

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 30%;">สร้างโดย</th>
                                <th style="width: 30%;">วันที่</th>
                                <th style="width: 30%;">จำนวนสมาชิกที่ได้รางวัล</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rewards as $index => $reward) 
                                <tr onclick="javascript: document.location.href  = '{{ action('Admin\RewardController@show', ['id' => $reward->id]) }}';"
                                    style="cursor: pointer;">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-user-secret fa-fw"></i> {{ $reward->admin->fullname }}</td>
                                    <td>{{ Diamond::parse($reward->created_at)->thai_format('j M Y') }}</td>
                                    <td>{{ number_format($reward->winners->count(), 0, '.', ',') }} คน</td>
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
                    { type: 'formatted-num', targets: 3 }
                ]
            });
        });
    </script>
@endsection