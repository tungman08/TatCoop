@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ทะเบียนหนี้ของสมาชิกสหกรณ์
            <small>แสดงข้อมูลทะเบียนหนี้ของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => action('Admin\LoanController@getMember')],
            ['item' => 'การกู้ยืม', 'link' => action('Admin\LoanController@index', ['member_id' => $member->id])],
            ['item' => 'ทะเบียนหนี้', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดทะเบียนหนี้</h4>
            <p>ข้อมูลทะเบียนหนี้ของสมาชิก เพื่อใช้พิจารณาในการอนุมัติเงินกู้</p>

            <select class="form-control" onchange="javascript: document.location.href='/service/loan/member/{{ $member->id }}/debt?year=' + $(this).val();">
                @if (Diamond::parse($member->start_date)->year < 2019)
                    @for ($year = Diamond::today()->year; $year >= 2019 ; $year--)
                        @if ($year == $selected_year)
                            <option value="{{ $year }}" selected>ปี {{ $year + 543 }}</option>
                        @else
                            <option value="{{ $year }}">ปี {{ $year + 543 }}</option>
                        @endif
                    @endfor
                @else
                    @for ($year = Diamond::today()->year; $year >= Diamond::parse($member->start_date)->year; $year--)
                        @if ($year == $selected_year)
                            <option value="{{ $year }}" selected>ปี {{ $year + 543 }}</option>
                        @else
                            <option value="{{ $year }}">ปี {{ $year + 543 }}</option>
                        @endif
                    @endfor
                @endif
            </select>

            <!-- this row will not appear when printing -->
            <div class="row no-print" style="margin-top: 30px;">
                <div class="col-xs-12">
                    <a href="javascript: document.location.href='{{ action('Admin\LoanController@getDebtPrint', ['member_id' => $member->id, 'year' => $selected_year]) }}';" target="_blank" class="btn btn-default btn-flat"><i class="fa fa-print"></i> พิมพ์</a>
                    <button type="button"
                        class="btn btn-primary btn-flat pull-right"
                        style="margin-right: 5px;"
                        onclick="javascript: document.location.href='{{ action('Admin\LoanController@getDebtPdf', ['member_id' => $member->id, 'year' => $selected_year]) }}';">
                        <i class="fa fa-download"></i> บันทึกเป็น PDF
                    </button>
                </div>
            </div>
        </div>

		<!-- Tab Panel -->
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#shareholding" data-toggle="tab"><strong><i class="fa fa-file-text-o"></i> ชำระค่าหุ้น</a></strong></li>

                @foreach ($loans as $index => $loan)
				    <li><a href="#loan{{ $loan->id }}" data-toggle="tab"><strong><i class="fa fa-file-text-o"></i> ชำระหนี้เงินกู้ ({{ $index + 1 }})</a></strong></li>
                @endforeach
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="shareholding">
                    @include('admin.loan.debtshareholding')
				</div>
				<!-- /.tab-pane -->

                @foreach ($loans as $loan)
                    <div class="tab-pane" id="loan{{ $loan->id }}">
                        @include('admin.loan.debtloan', ['loan' => $loan])
                    </div>
                    <!-- /.tab-pane -->
                @endforeach
			</div>
			<!-- /.tab-content -->
		</div>
		<!-- /.nav-tabs-custom -->
    </section>
    <!-- /.content -->
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection