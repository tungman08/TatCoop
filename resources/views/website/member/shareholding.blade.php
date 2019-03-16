@extends('website.member.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        ข้อมูลทุนเรือนหุ้น
        <small>รายละเอียดข้อมูลทุนเรือนหุ้นของสมาชิก</small>
    </h1>
    @include('website.member.layouts.breadcrumb', ['breadcrumb' => [
        ['item' => 'ทุนเรือนหุ้น', 'link' => ''],
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
                        <th style="width:20%;">จำนวนหุ้นรายเดือน:</th>
                        <td>{{ ($member->shareholding > 0) ? number_format($member->shareholding, 0, '.', ',') . ' หุ้น': '-' }}</td>
                    </tr>
                    <tr>
                        <th>ค่าหุ้นรายเดือน:</th>
                        <td>{{ ($member->shareholding > 0) ? number_format($member->shareholding * 10, 2, '.', ',') . ' บาท': '-' }}</td>
                    </tr>
                    <tr>
                        <th>ทุนเรือนหุ้นสะสม:</th>
                        <td>{{ number_format($member->shareHoldings->sum('amount'), 2, '.', ',') }} บาท</td>
                    </tr>        
                </table>
                <!-- /.table -->
            </div>  
            <!-- /.table-responsive --> 
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">รายละเอียดข้อมูลทุนเรือนหุ้น</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style="margin-top: 15px;">
                    <table id="dataTables-shareholding" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 18%;">เดือน</th>
                                <th style="width: 18%;">ค่าหุ้นปกติ</th>
                                <th style="width: 18%;">ค่าหุ้นเงินสด</th>
                                <th style="width: 18%;">รวมเป็นเงิน</th>
                                <th style="width: 18%;">ทุนเรือนทุ้นสะสม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shareholdings as $index => $share)
                                @php($date = Diamond::parse($share->name))
                                <tr onclick="javascript: document.location.href  = '{{ action('Website\MemberController@getShowShareholding', ['id'=>$member->id, 'month'=>$date->format('Y-n-1')]) }}';" style="cursor: pointer;">
                                    <td>{{ $index + 1 }}.</td>
                                    <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ $date->thai_format('F Y') }}</td>
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
                { type: 'formatted-num', targets: 0 },
                { type: 'formatted-num', targets: 2 },
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 },
                { type: 'formatted-num', targets: 5 }
            ]
        });
    });

    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "formatted-num-pre": function ( a ) {
            a = (a === "-" || a === "") ? 0 : a.replace(/[^\d\-\.]/g, "");
            return parseFloat( a );
        },

        "formatted-num-asc": function ( a, b ) {
            return a - b;
        },

        "formatted-num-desc": function ( a, b ) {
            return b - a;
        }
    });
    </script>
@endsection