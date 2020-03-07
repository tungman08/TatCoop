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
            ['item' => Diamond::parse($reward->created_at)->thai_format('j M Y'), 'link' => '']
        ]])
    </section>


    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจับรางวัล วันที่ {{ Diamond::parse($reward->created_at)->thai_format('j M Y') }}</h4>   

            <ul class="list-info">
                @foreach($reward->rewardConfigs as $config)
                    <li>
                        รางวัล {{ number_format($config->price, 2, '.', ',') }} บาท
                        จำนวน {{ number_format($config->amount, 0, '.', ',') }} รางวัล

                        @if ($config->register)
                            <span class="label label-info">ลงทะเบียน</span>
                        @endif

                        @if ($config->special)
                            <span class="label label-info">รางวัลพิเศษ</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <button type="button" class="btn btn-primary btn-flat"
                {{ (($is_super || $is_admin) && $reward->rewardStatus->id < 3 ? '' : 'disabled') }}
                onclick="javascript:document.location.href = '{{ action('Admin\RewardController@edit', ['id'=>$reward->id]) }}';">
                <i class="fa fa-pencil"></i> แก้ไขรางวัล
            </button>

            <a class="btn btn-success btn-flat pull-right margin-l-sm"
                {{ (($is_super || $is_admin) && $reward->rewardStatus->id == 3 && $reward->admin_id == $admin->id ? '' : 'disabled') }}
                href="{{ (($is_super || $is_admin) && $reward->rewardStatus->id == 3 && $reward->admin_id == $admin->id) ? action('Admin\RewardController@getSlotmachine', ['id'=>$reward->id]) : 'javascript:void(0);' }}"
                {{ (($is_super || $is_admin) && $reward->rewardStatus->id == 3 && $reward->admin_id == $admin->id ? 'target="_blank"' : '') }}>
                <i class="fa fa-play"></i> จับรางวัล
            </a>

            <button type="button" class="btn btn-primary btn-flat pull-right"
                {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                onclick="javascript:document.location.href = '{{ action('Admin\RewardController@getRegister', ['id'=>$reward->id]) }}';">
                <i class="fa fa-user-plus"></i> ลงทะเบียน
            </button>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-smile-o"></i> รายชื่อผู้ได้รับรางวัล</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                {{ Form::open(['action' => ['Admin\RewardController@destroy', $reward->id], 'method' => 'delete', 'role' => 'form', 'onsubmit' => "return confirm('คุณต้องการลบข้อมูลนี้ใช่ไหม?');"]) }}
                    {{ Form::button('<i class="fa fa-trash"></i> ลบข้อมูล', [
                        'id'=>'delete',
                        'type' => 'submit', 
                        'disabled' => $reward->reward_status_id == 4,
                        'class'=>'btn btn-danger btn-flat margin-b-md'])
                    }}
                {{ Form::close() }}

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 15%;">รหัสสมาชิก</th>
                                <th style="width: 25%;">ชื่อผู้โชคดี</th>
                                <th style="width: 15%;">รางวัล</th>
                                <th style="width: 15%;">สถานะ</th>
                                <th style="width: 20%;">เวลา</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($index = 0)
                            @foreach($reward->rewardConfigs as $config) 
                                @foreach($config->rewardWinners->sortBy('created_at') as $winner) 
                                    <tr>
                                        <td>{{ ++$index }}.</td>
                                        <td>{{ $winner->member->memberCode }}</td>
                                        <td class="text-primary"><i class="fa fa-smile-o fa-fw"></i> {{ $winner->member->profile->name . ' ' . $winner->member->profile->lastname }}</td>
                                        <td>{{ number_format($config->price, 2, '.', ',') }} บาท{{ $config->special ? ' (พิเศษ)' : '' }}</td>
                                        <td>{!! ($winner->status) ? '<span class="label label-primary">รับรางวัล</span>' : '<span class="label label-danger">สละสิทธิ์</span>' !!}</td>
                                        <td>{{ Diamond::parse($winner->created_at)->thai_format('j M Y เวลา H:i') }}</td>
                                    </tr>
                                @endforeach
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

    <style>
        .list-info li {
            padding-top: 0;
            padding-bottom: 4px;
        }
    </style>
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