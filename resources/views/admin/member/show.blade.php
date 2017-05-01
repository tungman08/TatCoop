@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
        </h1>

        @if (is_null($member->leave_date))
            @include('admin.layouts.breadcrumb', ['breadcrumb' => [
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/service/member'],
                ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => ''],
            ]])
        @else
            @include('admin.layouts.breadcrumb', ['breadcrumb' => [
                ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/service/member'],
                ['item' => 'สมาชิกสหกรณ์ที่ลาออก', 'link' => '/service/member/inactive'],
                ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => ''],
            ]])
        @endif
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลสมาชิกสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ แก้ไข ข้อมูลของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }}</p>
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
                <h3 class="box-title"><i class="fa fa-user"></i> ข้อมูลของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' : $member->profile->fullName }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                @include('admin.member.profile', ['member' => $member])
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="row">
            <div class="col-md-3">
                <button type="button" class="btn btn-block btn-primary btn-lg" onclick="javascript:window.location.href='{{ url('/service/' . $member->id . '/shareholding') }}';">
                    <i class="fa fa-money fa-fw"></i> ทุนเรือนหุ้น
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-block btn-danger btn-lg" onclick="javascript:window.location.href='{{ url('/service/loan/' . $member->id) }}';">
                    <i class="fa fa-credit-card fa-fw"></i> การกู้ยืม
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-block btn-success btn-lg" onclick="javascript:window.location.href='{{ url('/service/dividend/' . $member->id) }}';">
                    <i class="fa fa-dollar fa-fw"></i> เงินปันผล
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-block btn-warning btn-lg" onclick="javascript:window.location.href='{{ url('/service/loansurety/' . $member->id) }}';">
                    <i class="fa fa-share-alt fa-fw"></i> การค้ำประกัน
                </button>
            </div>
        </div>
    </section>
    <!-- /.content -->
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
    });
    </script>
@endsection