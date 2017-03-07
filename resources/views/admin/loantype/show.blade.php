@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการประเภทเงินกู้
            <small>เพิ่ม ลบ แก้ไข ประเภทเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.member.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการประเภทเงินกู้', 'link' => '/admin/loantype'],
            ['item' => 'ประเภทเงินกู้', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดการประเภทเงินกู้ของสหกรณ์</h4>

            @include('admin.loantype.info', ['loantype' => $loantype])

            <button class="btn btn-primary btn-flat"
                title="แก้ไข"
                onclick="javascript:window.location = '/admin/loantype/{{ $loantype->id }}/edit';">
                <i class="fa fa-edit"></i> แก้ไข
            </button> 

            @if ($loantype->id > 2)
                <button class="btn btn-danger btn-flat"
                    style="width: 75px;"
                    title="ลบ"
                    onclick="javascript:result = confirm('คุณต้องการลบประเภทเงินกู้นี้ใช่ไหม ?'); if (result) { $('#delete_item').click(); }">
                    <i class="fa fa-trash"></i> ลบ
                </button>
                
                {{ Form::open(['url' => '/admin/loantype/' . $loantype->id]) }}
                    {{ Form::hidden('_method', 'delete') }}
                    {{ Form::submit('Delete', ['id' => 'delete_item', 'style' => 'display: none;']) }}
                {{ Form::close() }} 
            @endif  
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
                <h3 class="box-title"><i class="fa fa-credit-card"></i> สัญญาเงินกู้ที่ใช้ประเภทเงินกู้นี้</h3>
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default btn-flat btn-xs"
                        onclick="javascript:window.location.href='{{ url('/admin/loantype/' . $loantype->id . '/finish') }}';">
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
                                <th style="width: 10%;">#</th>
                                <th style="width: 14%;">เลขที่สัญญา</th>
                                <th style="width: 20%;">ชื่อผู้กู้</th>
                                <th style="width: 14%;">วันที่ทำสัญญา</th>
                                <th style="width: 14%;">วงเงินที่กู้</th>
                                <th style="width: 14%;">จำนวนงวดที่ผ่อนชำระ</th>
                                <th style="width: 14%;">ชำระเงินต้นแล้ว</th>
                            </tr>
                        </thead>
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