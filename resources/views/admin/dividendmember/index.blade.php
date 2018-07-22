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
            ['item' => 'ปี ' . strval($dividend->rate_year + 543), 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การคำนวณเงินปันผลประจำปีให้กับสมาชิกสหกรณ์ฯ เงินปันปี {{ $dividend->rate_year + 543 }}</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">อัตราเงินปันผล</th>
                        <td>{{ $dividend->shareholding_rate }}%</td>
                    </tr>
                    <tr>
                        <th>เงินปันผลรวม:</th>
                        <td>{{ number_format($dividend->shareholding_dividend, 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>อัตราเงินเฉลี่ยคืน</th>
                        <td>{{ $dividend->loan_rate }}%</td>
                    </tr>
                    <tr>
                        <th>เงินเฉลี่ยคืนรวม</td>
                        <td>{{ number_format($dividend->interest_dividend, 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>รวมทั้งสิ้น</td>
                        <td>{{ number_format($dividend->shareholding_dividend + $dividend->interest_dividend, 2, '.', ',') }} บาท</td>
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
                <h3 class="box-title"><i class="fa fa-baht"></i> เงินปันผลประจำปี {{ $dividend->rate_year + 543 }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 25%;">ชื่อสมาชิก</th>
                                <th style="width: 20%;">ประเภทสมาชิก</th>
                                <th style="width: 15%;">จำนวนเงินปันผล</th>
                                <th style="width: 15%;">จำนวนเงินเฉลี่ยคืน</th>
                                <th style="width: 15%;">รวมเงินทั้งหมด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr onclick="javascript: document.location = '{{ url('/admin/dividendmember/' . $dividend->id . '/' . $member->id) }}';" style="cursor: pointer;">
                                    <td>{{ $member->code }}</td>
                                    <td class="text-primary"><i class="fa fa-user fa-fw"></i> {{ $member->fullname }}</td>
                                    <td>{{ $member->typename }}</td>
                                    <td>{{ number_format($member->shareholding, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($member->interest, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($member->shareholding + $member->interest, 2, '.', ',') }} บาท</td>
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
            "iDisplayLength": 25
        });
    });
    </script>
@endsection