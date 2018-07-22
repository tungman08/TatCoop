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
            <p>แสดงการชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullName }}</p>
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
                                <th style="width: 18%;">ใบรับเงินค่าหุ้น</th>
                            </tr>
                        </thead>
                        <tbody>
                            @eval($count = 0)
                            @foreach($shareholdings->sortByDesc('name') as $share)
                                @php($date = Diamond::parse($share->name))
                                <tr>
                                    <td>{{ ++$count }}</td>
                                    <td class="text-primary"><i class="fa fa-money fa-fw"></i> {{ $date->thai_format('F Y') }}</td>
                                    <td>{{ number_format($share->amount, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->amount_cash, 2, '.', ',') }} บาท</td>
                                    <td>{{ number_format($share->amount + $share->amount_cash, 2, '.', ',') }} บาท</td>
                                    <td>
                                        @if (Diamond::parse($share->name)->gte(Diamond::create(2016, 1, 1, 0, 0, 0)))
                                            <a href="/member/{{ $member->id }}/shareholding/billing/{{ $date->endOfMonth()->format('Y-m-d') }}"><i class="fa fa-file-o"></i> ใบรับเงินค่าหุ้น</a>
                                        @endif
                                    </td>
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
    });

    $('#dataTables-shareholding').dataTable({
        "iDisplayLength": 10
    });
    </script>
@endsection