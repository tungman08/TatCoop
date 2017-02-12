@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการสมาชิกสหกรณ์
            <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
        </h1>

        @if (is_null($member->leave_date))
            @include('admin.member.breadcrumb', ['breadcrumb' => [
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/admin/member'],
                ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => ''],
            ]])
        @else
            @include('admin.member.breadcrumb', ['breadcrumb' => [
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/admin/member'],
                ['item' => 'สมาชิกสหกรณ์ที่ลาออก', 'link' => '/admin/member/inactive'],
                ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => ''],
            ]])
        @endif
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well" style="padding-bottom: 0px;">
            <h4>รายละเอียดข้อมูลสมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th style="width:20%; border-top: none; border-bottom: 1px solid #e4e4e4;">หมายเลขสมาชิก:</th>
                        <td style="border-top: none; border-bottom: 1px solid #e4e4e4;">{{ $member->member_code }}</td>
                    </tr>
                    <tr>
                        <th style="border-top: none; border-bottom: 1px solid #e4e4e4;">ชื่อ:</th>
                        <td style="border-top: none; border-bottom: 1px solid #e4e4e4;">{{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullName }} </td>
                    </tr>
                    <tr>
                        <th style="border-top: none; border-bottom: 1px solid #e4e4e4;">จำนวนหุ้นต่อเดือน:</th>
                        <td style="border-top: none; border-bottom: 1px solid #e4e4e4;">{{ number_format($member->shareholding, 0,'.', ',') }} หุ้น ({{ number_format($member->shareholding * 10, 2,'.', ',') }} บาท)</td>
                    </tr>
                    <tr>
                        <th style="border-top: none; border-bottom: 1px solid #e4e4e4;">ทุนเรือนหุ้นสะสม:</th>
                        <td style="border-top: none; border-bottom: 1px solid #e4e4e4;">{{ number_format($member->shareholdings()->sum('amount'), 2,'.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>ยอดหนี้คงเหลือ:</th>
                        <td>- บาท</td>
                    </tr>                   
                </table>
            </div>

            @if (is_null($member->leave_date))
                <button class="btn btn-danger btn-flat"
                    style="margin-bottom: 20px; width: 120px;"
                    title="ลาออกจากสมาชิก"
                    onclick="javascript:var result = confirm('สมาชิกท่านนี้ต้องการลาออกจากการเป็นสมาชิกใช่หรือไม่ ?'); if (result) { window.location = '/admin/member/{{ $member->id }}/leave'; }">
                    <i class="fa fa-user-times"></i> ลาออก
                </button>
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

        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="{{ ($tab == 0) ? 'active' : '' }}"><a href="#result" data-toggle="tab"><strong><i class="fa fa-user fa-fw"></i> ข้อมูลสมาชิก</a></strong></li>
                <li class="{{ ($tab == 1) ? 'active' : '' }}"><a href="#shareholding" data-toggle="tab"><strong><i class="fa fa-money fa-fw"></i> ทุนเรือนหุ้น</a></strong></li>
                <li class="{{ ($tab == 2) ? 'active' : '' }}"><a href="#loan" data-toggle="tab"><strong><i class="fa fa-credit-card fa-fw"></i> การกู้ยืม</a></strong></li>
                <li class="{{ ($tab == 3) ? 'active' : '' }}"><a href="#dividend" data-toggle="tab"><strong><i class="fa fa-dollar fa-fw"></i> เงินปันผล</a></strong></li>
                <li class="{{ ($tab == 4) ? 'active' : '' }}"><a href="#guarantee" data-toggle="tab"><strong><i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน</a></strong></li>
                
            </ul>

            <div class="tab-content">
                <div class="tab-pane {{ ($tab == 0) ? 'active' : '' }}" id="result">
                    @include('admin.member.info.profile', ['member' => $member])
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane {{ ($tab == 1) ? 'active' : '' }}" id="shareholding">
                    @include('admin.member.info.shareholding', ['member' => $member])
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane {{ ($tab == 2) ? 'active' : '' }}" id="loan">
                    @include('admin.member.info.loan', ['member' => $member])
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane {{ ($tab == 3) ? 'active' : '' }}" id="dividend">
                    @include('admin.member.info.dividend', [
                        'member' => $member, 
                        'dividend_years' => $dividend_years, 
                        'dividends' => $dividends
                    ])
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane {{ ($tab == 4) ? 'active' : '' }}" id="guarantee">

                </div>
                <!-- /.tab-pane -->

            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
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
        "iDisplayLength": 25
    });

    $('#dataTables-loan').dataTable({
        "iDisplayLength": 25
    });

    $('#selectyear').change(function() {
        $.ajax({
            url: '/ajax/dividend',
            type: "get",
            data: {
                'id': {{ $member->id }},
                'year': $('#selectyear').val()
            },
            success: function(data) {
                $('#dividend_rate').html('(' + (data.dividend_rate == null ? '0' : data.dividend_rate ) + '%)');
                $('#dividend tbody>tr').remove();
                var index = 0;
                var total_amount = 0;
                var total_shareholding = 0;
                var total_dividend = 0;

                jQuery.each(data.dividends, function(i, val) {
                    $("#dividend tbody").append('<tr><td class="text-primary">' + 
                        ((index == 0) ? val.name : thai_date(moment(val.name, 'YYYY-MM-DD'))) + '</td><td>' + 
                        val.shareholding.format() + ' หุ้น</td><td>' + 
                        val.amount.format(2) + ' บาท</td><td>' + 
                        val.dividend.format(2) + ' บาท</td><td' + (data.dividend_rate == null ? ' class="text-danger"' : '') + '>' + 
                        val.remark + '</td></tr>');

                    index++;
                    total_amount += val.amount;
                    total_shareholding += val.shareholding;
                    total_dividend += val.dividend;
                });

                $("#dividend tbody").append('<tr><td class="text-primary"><strong>รวม</strong></td><td><strong>' + 
                    total_shareholding.format() + ' หุ้น</strong></td><td><strong>' + 
                    total_amount.format(2) + ' บาท</strong></td><td class="text-success"><strong>' + 
                    total_dividend.format(2) + ' บาท</strong></td><td' + (data.dividend_rate == null ? ' class="text-danger"' : '') + '>' + 
                    (data.dividend_rate == null ? 'ยังไม้ได้กำหนดอัตราเงินปันผล' : '') + '</td></tr>');
            }
        });
    });

    Number.prototype.format = function(n, x) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
        return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    };

    function thai_date(date) {
        var months = { 'January': 'มกราคม', 'February': 'กุมภาพันธ์', 'March': 'มีนาคม', 'April': 'เมษายน', 'May': 'พฤษภาคม', 'June': 'มิถุนายน', 'July': 'กรกฎาคม', 'August': 'สิงหาคม', 'September': 'กันยายน', 'October': 'ตุลาคม', 'November': 'พฤศจิกายน', 'December': 'ธันวาคม' };
        var month = months[date.format("MMMM")];
        var year = parseInt(date.format("YYYY"), 10) + 543;

        return month + " " + year.toString();
    }
    </script>
@endsection