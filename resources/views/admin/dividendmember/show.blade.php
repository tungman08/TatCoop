@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงินปันผลประจำปีของสมาชิกสหกรณ์ฯ
            <small>คำนวณเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงินปันผลประจำปีของสมาชิก', 'link' => url('/admin/dividendmember/year')],
            ['item' => 'ปี ' . strval($dividend->rate_year + 543), 'link' => url('/admin/dividendmember/' . $dividend->id )],
            ['item' => $member->fullname, 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การคำนวณเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ เงินปันปี ของ {{ $member->fullname }}</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">อัตราเงินปันผล</th>
                        <td>{{ $dividend->shareholding_rate }}%</td>
                    </tr>
                    <tr>
                        <th>เงินปันผลรวม:</th>
                        <td>{{ number_format($member->dividends->sum('shareholding_dividend'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>อัตราเงินเฉลี่ยคืน</th>
                        <td>{{ $dividend->loan_rate }}%</td>
                    </tr>
                    <tr>
                        <th>เงินเฉลี่ยคืนรวม</td>
                        <td>{{ number_format($member->dividends->sum('interest_dividend'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>รวมทั้งสิ้น</td>
                        <td>{{ number_format($member->dividends->sum('shareholding_dividend') + $member->dividends->sum('interest_dividend'), 2, '.', ',') }} บาท</td>
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

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-baht"></i> เงินปันผลประจำปี {{ $dividend->rate_year + 543 }} ของ {{ $member->fullname }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th class="text-right" style="width: 18%;">จำนวนเงินค่าหุ้น</th>
                                <th class="text-right" style="width: 18%;">จำนวนเงินปันผล</th>
                                <th class="text-right" style="width: 18%;">จำนวนดอกเบี้ยเงินกู้</th>
                                <th class="text-right" style="width: 18%;">จำนวนเงินเฉลี่ยคืน</th>
                                <th class="text-right" style="width: 18%;">รวมทั้งสิ้น</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($member->dividends as $m_dividend)
                                <tr onclick="javascript: document.location = '{{ url('/admin/dividendmember/' . $dividend->id . '/' . $member->id . '/' . $m_dividend->id . '/edit') }}';" style="cursor: pointer;">
                                    <td class="text-primary">{{ $m_dividend->dividend_name }}</td>
                                    <td class="text-right">{{ number_format($m_dividend->shareholding, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($m_dividend->shareholding_dividend, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($m_dividend->interest, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($m_dividend->interest_dividend, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ number_format($m_dividend->shareholding_dividend + $dividend->interest_dividend, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-primary"><strong>รวม</strong></td>
                                <td class="text-right"><strong>{{ number_format($member->dividends->sum('shareholding'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($member->dividends->sum('shareholding_dividend'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($member->dividends->sum('interest'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($member->dividends->sum('interest_dividend'), 2, '.', ',') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($member->dividends->sum('shareholding_dividend') + $member->dividends->sum('interest_dividend'), 2, '.', ',') }}</strong></td>
                            </tr> 
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
    {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
    {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
    {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

    <script>
    $(document).ready(function () {
        $('[data-tooltip="true"]').tooltip();

        $('#dataTables').dataTable({
            "iDisplayLength": 25,
            "bLengthChange": false,
            "bSort": false,
            "bFilter": false
        });
    });
    </script>
@endsection