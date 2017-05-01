@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => '/service/loan/member'],
            ['item' => 'การกู้ยืม', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลการกู้ยืม</h4>
            <p>แสดงการสัญญาการกู้ยืมต่าง ๆ ของ {{ $member->profile->fullName }}</p>
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
                <h3 class="box-title"><i class="fa fa-credit-card"></i> รายละเอียดสัญญาการกู้ยืม</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="btn-group">
                    <button id="create_loan" class="btn btn-primary btn-flat" style="margin-bottom: 15px;">
                        <i class="fa fa-plus-circle fa-fw"></i> ทำสัญญาเงินกู้ใหม่
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="dataTables-loans" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 15%;">เลขที่สัญญา</th>
                                <th style="width: 15%;">ประเภทเงินกู้</th>
                                <th style="width: 10%;">วันที่กู้</th>
                                <th style="width: 10%;">วงเงินที่กู้</th>
                                <th style="width: 15%;">จำนวนงวดที่ผ่อนชำระ</th>
                                <th style="width: 15%;">จำนวนเงินที่ชำระแล้ว</th>
                                <th style="width: 10%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($loans as $loan) 
                            <tr onclick="javascript: document.location = '{{ url('service/' . $member->id . '/loan/' . $loan->id) }}';"
                                style="cursor: pointer;">
                                <td>{{ ++$count }}</td>
                                <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> {{ $loan->code }}</td>
                                <td>{{ $loan->loanType->name }}</td>
                                <td>{{ Diamond::parse($loan->loaned_at)->thai_format('j M Y') }}</td>
                                <td>{{ number_format($loan->outstanding, 2, '.', ',') }}</td>
                                <td>{{ number_format($loan->period, 0, '.', ',') }}</td>
                                <td>{{ number_format($loan->payments->count(), 0, '.', ',') }}</td>
                                <td class="{{ (!is_null($loan->completed_at)) ? 'text-success' : 'text-danger' }}">{{ (!is_null($loan->completed_at)) ? 'ปิดยอดแล้ว' : 'กำลังผ่อนชำระ' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- /.table-responsive -->
                </div>
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

    <!-- Special Load Modal -->
    <div id="loanModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ประเภทสินเชื่อ</h4>
                </div>
                <div class="modal-body text-center">
                    <select id="loantype" class="form-control">
                        @foreach($loantypes as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary btn-flat margin-t-lg margin-b-lg"
                        onclick="javascript:window.location.href='/service/{{ $member->id }}/loan/' + $('#loantype').val() + '/create';">
                        <i class="fa fa-file-o"></i> ทำสัญญา
                    </button>
                </div>
            </div>
        </div>
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
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#dataTables-loans').dataTable({
            "iDisplayLength": 10
        });

        $('[data-tooltip="true"]').tooltip();
        $(".ajax-loading").css("display", "none");

        $('#create_loan').click(function () {
            $.ajax({
                url: '/ajax/clearloan',
                type: "post",
                data: {
                    'id': {{ $member->id }}
                },
                beforeSend: function() {
                    $(".ajax-loading").css("display", "block");
                },
                success: function() {
                    $(".ajax-loading").css("display", "none");
                    $('#loantype option:eq(0)').prop('selected', true); 
                    $('#loanModal').modal('show');
                }
            });
        }) 
    });   
    </script>
@endsection