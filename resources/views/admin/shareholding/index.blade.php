@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการทุนเรือนหุ้นของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข ทุนเรือนหุ้นของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการทุนเรือนหุ้น', 'link' => action('Admin\ShareholdingController@getMember')],
            ['item' => 'ทุนเรือนหุ้น', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลทุนเรือนหุ้น</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullname }}</td>
                    </tr>
                    @if ($member->profile->employee->employee_type_id == 1)
                        <tr>
                            <th>จำนวนหุ้นรายเดือน:</th>
                            <td>
                                {{ number_format($member->shareholding, 0, '.', ',') }} หุ้น

                                <button class="btn btn-xs btn-primary btn-flat margin-l-xl"
                                    {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                                    title="ปรับปรุงหุ้นรายเดือน"
                                    onclick="javascript:document.location.href = '{{ action('Admin\ShareholdingController@getAdjust', ['member_id' => $member->id]) }}';">
                                    ปรับปรุงหุ้นรายเดือน
                                </button>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>จำนวนทุนเรือนหุ้นสะสม:</th>
                        <td>{{ number_format($member->shareHoldings->sum('amount'), 2, '.', ',') }} บาท</td>
                    </tr>        
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

        <div class="row margin-b-md">
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-primary btn-lg" onclick="javascript:document.location.href='{{ action('Admin\MemberController@show', ['id'=>$member->id]) }}';">
                    <i class="fa fa-user fa-fw"></i> ข้อมูลสมาชิก
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-success btn-lg disabled">
                    <i class="fa fa-money fa-fw"></i> ทุนเรือนหุ้น
                </button>
            </div>            
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-danger btn-lg" onclick="javascript:document.location.href='{{ action('Admin\LoanController@index', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-credit-card fa-fw"></i> การกู้ยืม
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-warning btn-lg" onclick="javascript:document.location.href='{{ action('Admin\GuarunteeController@index', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน
                </button>
            </div>
            <div class="col-md-5ths">
                <button type="button" class="btn btn-block btn-purple btn-lg" onclick="javascript:document.location.href='{{ action('Admin\DividendController@getMemberDividend', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-dollar fa-fw"></i> เงินปันผล
                </button>
            </div>
        </div>
        <!-- /.row -->

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
                <h3 class="box-title">รายละเอียดข้อมูลทุนเรือนหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <button class="btn btn-primary btn-flat" style="margin-bottom: 15px;"
                    {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                    onclick="javascript:document.location.href = '{{ action('Admin\ShareholdingController@create', ['member_id'=>$member->id]) }}';">
                    <i class="fa fa-plus-circle"></i> ชำระเงินค่าหุ้น
                </button>

                <div class="table-responsive" style="margin-top: 15px;">
                    <table id="dataTables-shareholding" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 18%;">เดือน</th>
                                <th style="width: 18%;">ค่าหุ้นปกติ</th>
                                <th style="width: 18%;">ค่าหุ้นเงินสด</th>
                                <th style="width: 18%;">รวมเป็นเงิน</th>
								<th style="width: 18%;">ทุนเรือนหุ้นสะสม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shareholdings as $index => $share)
                                <tr onclick="javascript: document.location.href  = '{{ action('Admin\ShareholdingController@getMonth', ['member_id'=>$member->id, 'pay_date'=>$share->paydate]) }}';" style="cursor: pointer;">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ Diamond::parse($share->paydate)->thai_format('F Y') }}</td>
                                    <td>{{ number_format($share->amount, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->amount_cash, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->amount + $share->amount_cash, 2, '.', ',') }} บาท</td>
									<td>{{ number_format($share->total_shareholding + $share->amount + $share->amount_cash, 2, '.', ',') }} บาท</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!-- /.table-responsive -->
                    </table>
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
@endsection

@section('styles')
    <!-- Bootstrap DataTable CSS -->
    {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

    @parent
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DataTable JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();

        $('#dataTables-shareholding').dataTable({
            "iDisplayLength": 10,
            "columnDefs": [
                { type: 'formatted-num', targets: 2 },
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 },
                { type: 'formatted-num', targets: 5 }
            ]
        });
    });
    </script>
@endsection