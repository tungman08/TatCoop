@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการทุนเรือนหุ้นของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข ทุนเรือนหุ้นของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการทุนเรือนหุ้น', 'link' => '/service/shareholding/member'],
            ['item' => 'ทุนเรือนหุ้น', 'link' => '/service/' . $member->id . '/shareholding'],
            ['item' => Diamond::parse($shareholding_date)->thai_format('M Y'), 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>

			<div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }}</td>
                    </tr>
                    <tr>
                        <th>ค่าหุ้นเดือน:</th>
                        <td>{{ Diamond::parse($shareholding_date)->thai_format('F Y') }}</td>
                    </tr>
					<tr>
                        <th>จำนวนหุ้นที่ชำระ:</th>
                        <td>{{ number_format($shareholdings->sum('amount'), 2, '.', ',') }} บาท</td>
                    </tr>  
                    <tr>
                        <th>ทุนเรือนหุ้นสะสม ณ {{ Diamond::parse($shareholding_date)->thai_format('M Y') }}:</th>
                        <td>{{ number_format($total_shareholding + $shareholdings->sum('amount'), 2, '.', ',') }} บาท</td>
                    </tr>        
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
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
                <h3 class="box-title"><i class="fa fa-money"></i> รายละเอียดการชำระค่าหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style="margin-top: 15px;">
                    <table id="dataTables-shareholding" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 20%;">วันที่ชำระ</th>
                                <th style="width: 20%;">ประเภท</th>
                                <th style="width: 23%;">จำนวน</th>
                                <th style="width: 23%;">ทุนเรือนหุ้นสะสม</th>
                                <th style="width: 4%;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($count = 0)
                            @foreach($shareholdings->sortByDesc('id')->sortByDesc('paydate') as $share)
                                <tr onclick="javascript: document.location = '{{ action('Admin\ShareholdingController@getDetail', ['member_id'=>$member->id, 'paydate'=>Diamond::parse($share->paydate)->format('Y-n-1'), 'id'=>$share->id]) }}';" style="cursor: pointer;">
                                    <td>{{ ++$count }}.</td>
                                    <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ Diamond::parse($share->paydate)->thai_format('Y-n-d') }}</td>
                                    <td><span class="label label-primary">{{ $share->shareholding_type_name }}</td>
                                    <td>{{ number_format($share->amount, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->total_shareholding + $share->amount, 2, '.', ',') }} บาท</td>
                                    <td>{!! $share->attachment !!}</td>
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
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('[data-tooltip="true"]').tooltip();

        $('#dataTables-shareholding').dataTable({
            "iDisplayLength": 10
        });
    });
    </script>
@endsection