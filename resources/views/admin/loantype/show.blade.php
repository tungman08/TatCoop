@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการประเภทเงินกู้
            <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการประเภทเงินกู้', 'link' => '/admin/loantype'],
            ['item' => 'ประเภทเงินกู้', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            {{ Form::open(['url' => '/admin/loantype/' . $loantype->id, 'method' => 'delete']) }}
                <h4>
                    การจัดการประเภทเงินกู้ของสหกรณ์

                    @if ($loantype->id > 2)
                        {{ Form::button('<i class="fa fa-times"></i>', [
                            'type'=>'submit',
                            'data-tooltip'=>"true",
                            'title'=>"ลบ",
                            'style'=>'margin-left: 2px;',
                            'class'=>'btn btn-danger btn-xs btn-flat pull-right', 
                            'onclick'=>"javascript:return confirm('คุณต้องการลบประเภทเงินกู้นี้ใช่หรือไม่?');"])
                        }}
                    @endif  
                </h4>
            {{ Form::close() }}

            @include('admin.loantype.info', ['loantype' => $loantype])

            <button class="btn btn-primary btn-flat" onclick="javascript:window.location = '/admin/loantype/{{ $loantype->id }}/edit';">
                <i class="fa fa-edit"></i> แก้ไขประเภทสัญญา
            </button>
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

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
                <h3 class="box-title"><i class="fa fa-credit-card"></i> สัญญาเงินกู้ที่ใช้ประเภทเงินกู้นี้ (กำลังผ่อนชำระจำนวน {{ number_format($loantype->loans->filter(function ($value, $key) { return !empty($value->code) && empty($value->completed_at); })->count()) }} สัญญา)</h3>
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default btn-flat btn-xs"
                        onclick="javascript:window.location.href='{{ url('/admin/loantype/' . $loantype->id . '/finished') }}';">
                        <i class="fa fa-check-circle-o"></i> สัญญาเงินกู้ที่ชำระหมดแล้ว
                    </button>
                </div>            
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-loans" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 8%;">#</th>
                                <th style="width: 10%;">เลขที่สัญญา</th>
                                <th style="width: 18%;">ชื่อผู้กู้</th>
                                <th style="width: 10%;">วันที่ทำสัญญา</th>
                                <th style="width: 12%;">วงเงินที่กู้</th>
                                <th style="width: 8%;">จำนวนงวด</th>
                                <th style="width: 12%;">ชำระแล้ว</th>
                                <th style="width: 12%;">คงเหลือ</th>
                                <th style="width: 10%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($loantype->loans->filter(function ($value, $key) { 
                                    return !empty($value->code) && is_null($value->completed_at); 
                                })->sortByDesc(function ($value, $key) {
                                    $code = explode('/', $value->code);
                                    return $code[1] . $code[0];
                                }) as $loan)
                                <tr onclick="javascript: document.location = '{{ url('/service/' . $loan->member->id . '/loan/' . $loan->id) }}';" style="cursor: pointer;">
                                    <td>{{ ++$count }}.</td>
                                    <td class="text-primary"><i class="fa fa-credit-card fa-fw"></i> {{ $loan->code }}</td>
                                    <td>{{ $loan->member->profile->fullName }}</td>
                                    <td>{{ Diamond::parse($loan->loaned_at)->thai_format('Y-m-d') }}</td>
                                    <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->payments->count(), 0, '.', ',') }}/{{ number_format($loan->period, 0, '.', ',') }}</td>
                                    <td>{{ number_format($loan->payments->sum('principle'), 2, '.', ',') }}</td>
                                    <td>{{ number_format(round($loan->outstanding - $loan->payments->sum('principle'), 2), 2, '.', ',') }}</td>
                                    <td>{!! (!is_null($loan->completed_at)) ? '<span class="label label-success">ปิดยอดแล้ว</span>' : '<span class="label label-danger">กำลังผ่อนชำระ</span>' !!}</td>
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

    $('#dataTables-loans').dataTable({
        "iDisplayLength": 10
    });
    </script>
@endsection