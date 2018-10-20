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
            <h4>รายละเอียดข้อมูลการกู้ยืม</h4>

            <div class="table-responsive">
                @php
                    $loansCount = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->count();
                    $outstanding = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum('outstanding');
                    $principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum(function ($value) { return $value->payments->sum('principle'); });
                @endphp

                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }}</td>
                    </tr>
                    <tr>
                        <th>วงเงินที่กู้ที่กำลังผ่อนชำระทั้งหมด:</th>
                        <td>{{ ($loansCount > 0) ? number_format($outstanding, 2, '.', ',') . ' บาท (สัญญาเงินกู้ ' . number_format($loansCount, 0, '.', ',') . ' สัญญา)' : '-'}}</td>
                    </tr>  
                    <tr>
                        <th>เงินต้นคงเหลือทั้งหมด:</th>
                        <td>{{ ($outstanding - $principle > 0) ?number_format($outstanding - $principle, 2, '.', ',') . ' บาท' : '-' }}</td>
                    </tr>
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
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

        <div class="row margin-b-md">
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-primary btn-lg" onclick="javascript:window.location.href='{{ url('/service/member/' . $member->id) }}';">
                    <i class="fa fa-user fa-fw"></i> ข้อมูลสมาชิก
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-success btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/shareholding') }}';">
                    <i class="fa fa-money fa-fw"></i> ทุนเรือนหุ้น
                </button>
            </div>            
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-danger btn-lg disabled">
                    <i class="fa fa-credit-card fa-fw"></i> การกู้ยืม
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-warning btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/guaruntee') }}';">
                    <i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-purple btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/dividend') }}';">
                    <i class="fa fa-dollar fa-fw"></i> เงินปันผล
                </button>
            </div>
        </div>
        <!-- /.row -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card"></i> รายละเอียดสัญญาการกู้ยืม</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button id="create_loan" class="btn btn-primary btn-flat" style="margin-bottom: 15px;">
                    <i class="fa fa-plus-circle fa-fw"></i> ทำสัญญาเงินกู้ใหม่
                </button>
                <button id="cal_loan" class="btn btn-default btn-flat margin-b-md pull-right" type="button" data-tooltip="true" title="คำนวณความสามารถในการกู้สามัญ">
                    <i class="fa fa-calculator"></i> ความสามารถในกู้สามัญ
                </button>
                @if ($member->profile->employee->employee_type_id == 1)
                    <button id="cal_surety" class="btn btn-default btn-flat margin-b-md pull-right margin-r-sm" type="button" data-tooltip="true" title="คำนวณความสามารถในการค้ำประกันผู้อื่น">
                        <i class="fa fa-calculator"></i> ความสามารถในการค้ำผู้อื่น
                    </button>
                @endif

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-loans" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 12%;">เลขที่สัญญา</th>
                                <th style="width: 12%;">ประเภทเงินกู้</th>
                                <th style="width: 10%;">วันที่กู้</th>
                                <th style="width: 12%;">วงเงินที่กู้</th>
                                <th style="width: 12%;">จำนวนงวด</th>
                                <th style="width: 12%;">ชำระแล้ว</th>
                                <th style="width: 12%;">คงเหลือ</th>
                                <th style="width: 8%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($loans->sortByDesc('loaned_at') as $loan) 
                                <tr onclick="javascript: document.location = '{{ url('service/' . $member->id . '/loan/' . $loan->id) }}';"
                                    style="cursor: pointer;">
                                    <td>{{ ++$count }}</td>
                                    <td class="text-primary"><i class="fa fa-file-text-o fa-fw"></i> {{ $loan->code }}</td>
                                    <td><span class="label label-primary">{{ $loan->loanType->name }}</span></td>
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
                    <!-- /.table -->
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

    <!-- Special Loan Modal -->
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

    <!-- Salary Modal -->
    <div id="calsuretyModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">คำนวณความสามารถในการค้ำประกันผู้อื่น</h4>
                </div>
                <div class="modal-body">
                    <label for="calsuretysalary">เงินเดือน</label>
                    <input type="text" id="calsuretysalary" class="form-control margin-b-sm" />

                    <label for="calsuretynetsalary">เงินเดือนสุทธิ</label>
                    <input type="text" id="calsuretynetsalary" class="form-control margin-b-lg" />

                    <div class="text-center">
                        <button id="calsurety" class="btn btn-primary btn-flat margin-b-lg">
                            <i class="fa fa-file-o"></i> คำนวณ
                        </button>
                    </div>
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
        });
        
        $('#cal_loan').click(function () {
            $.ajax({
                url: '/ajax/checkloanavailable',
                type: "post",
                data: {
                    'id': {{ $member->id }}
                },
                success: function(message) {
                    alert(message);
                } 
            });
        });

        $('#cal_surety').click(function () {
            $.ajax({
                url: '/ajax/countsurety',
                type: "post",
                data: {
                    'id': {{ $member->id }}
                },
                success: function(loans) {
                    if (loans.length < 2) {
                        $('#calsuretyModal').modal('show');
                    }
                    else {
                        alert('ผู้ค้ำประกันได้ใช้สิทธิ์การค้ำ 2 สัญญาเท่านั้น');
                    }
                }
            });
        });

        $('#calsurety').click(function () {
            $.ajax({
                url: '/ajax/checksuretyavailable',
                type: "post",
                data: {
                    'id': {{ $member->id }},
                    'salary': $('#calsuretysalary').val(),
                    'netsalary': $('#calsuretynetsalary').val()
                },
                success: function(message) {
                    alert(message);
                },
                complete: function(){
                    $('#calsuretyModal').modal('hide');
                }  
            });
        });
    });   
    </script>
@endsection