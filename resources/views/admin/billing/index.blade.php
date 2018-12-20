@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการใบรับเงิน
            <small>รายละเอียดข้อมูลใบเสร็จรับเงินของสมาชิก</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการใบรับเงิน', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>จัดการใบรับเงินค่าหุ้น/ค่างวด</h4>
            <p>ให้ผู้ดูแลระบบสามารถ แก้ไข ชื่อผู้จัดการและเหรัญญิกได้</p>
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

		<!-- Tab Panel -->
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#shareholding" data-toggle="tab"><strong><i class="fa fa-file-text-o"></i> ตัวอย่างใบรับเงินค่าหุ้น</a></strong></li>
				<li><a href="#loan" data-toggle="tab"><strong><i class="fa fa-file-text-o"></i> ตัวอย่างใบรับเงินค่างวด </a></strong></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="shareholding">
					@include('admin.billing.shareholding')
				</div>
				<!-- /.tab-pane -->

				<div class="tab-pane" id="loan">
					@include('admin.billing.loan')
				</div>
				<!-- /.tab-pane -->

			</div>
			<!-- /.tab-content -->

			<!-- Box row -->
			<div class="row">
				<div class="col-lg-12 text-center" style="padding: 20px 0px;">
					@if (empty($billing))
						<button class="btn btn-primary btn-flat" type="button" data-tooltip="true" title="เพิ่มชื่อผู้จัดการ/เหรัญญิก"
							onclick="javascript:window.location.href='{{ url('/admin/billing/create') }}';">
							<i class="fa fa-plus-circle"></i> เพิ่มชื่อผู้จัดการ/เหรัญญิก
						</button>
					@else
						<button class="btn btn-primary btn-flat" type="button" data-tooltip="true" title="แก้ไขชื่อผู้จัดการ/เหรัญญิก"
							onclick="javascript:window.location.href='{{ url('/admin/billing/' . $billing->id . '/edit') }}';">
							<i class="fa fa-edit"></i> แก้ไขชื่อผู้จัดการ/เหรัญญิก
						</button>
					@endif
				</div>
				<!-- /.col -->
			</div>
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
    @parent

	<style>
        .table-borderless > tbody > tr > td,
        .table-borderless > tbody > tr > th,
        .table-borderless > tfoot > tr > td,
        .table-borderless > tfoot > tr > th,
        .table-borderless > thead > tr > td,
        .table-borderless > thead > tr > th {
            border: none;
            padding: 6px 0px;
        }
    </style>
@endsection

@section('scripts')
    @parent
@endsection