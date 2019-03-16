@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => '/service/loan/member'],
            ['item' => 'การกู้ยืม', 'link' => '/service/' . $member->id . '/loan'],
            ['item' => 'สัญญากู้ยืม', 'link' => '/service/' . $member->id . '/loan/' . $loan->id],
            ['item' => 'รายการผ่อนชำระ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดสัญญากู้ยืมเลขที่ {{ $loan->code }}</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">ชื่อผู้สมาชิก:</th>
                        <td>{{ $member->profile->fullname }}</td>
                    </tr>
                    <tr>
                        <th>ประเภทเงินกู้:</th>
                        <td>{{ $loan->loanType->name }}</td>
                    </tr>  
                    <tr>
                        <th>วงเงินที่กู้:</th>
                        <td>{{ number_format($loan->outstanding, 2, '.', ',') }} บาท</td>
                    </tr>  
                    <tr>
                        <th>จำนวนงวดผ่อนชำระ:</th>
                        <td>{{ number_format($loan->period, 0, '.', ',') }} งวด (ชำระงวดละ {{ number_format(LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period), 2, '.', ',') }} บาท)</td>
                    </tr> 
                    <tr>
                        <th>เงินต้นคงเหลือ:</th>
                        <td>{{ number_format($loan->outstanding - $loan->payments->sum('principle'), 2, '.', ',') }} บาท</td>
                    </tr>
                    <tr>
                        <th>ดอกเบี้ยสะสม:</th>
                        <td>{{ number_format($loan->payments->sum('interest'), 2, '.', ',') }} บาท</td>
                    </tr>
                    
                    @if ($loan->loan_type_id == 1)
                        <tr>
                            <th>ผู้ค้ำประกัน:</th>
                            <td>
                                <ul class="list-info">
                                    @foreach($loan->sureties as $item)
                                        <li>{{ $item->profile->fullname }} (ค้ำประกันจำนวน {{ number_format($item->pivot->amount, 2, '.', ',')  }}  บาท)</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endif
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

        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-credit-card"></i> รายละเอียดผ่อนชำระ</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <button class="btn btn-primary btn-flat margin-b-sm" onclick="javascript:document.location.href='{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/billing/' . $payment->id . '/' . Diamond::parse($payment->pay_date)->format('Y-n-j')) }}';">
                            <i class="fa fa-file-text-o"></i> ใบเสร็จรับเงินการชำระเงินกู้
                        </button>

                        <button class="btn btn-primary btn-flat margin-b-sm pull-right"
                            {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                            onclick="javascript:document.location.href  = '{{ url('/service/' . $member->id . '/loan/' . $loan->id . '/payment/' . $payment->id . '/edit') }}';">
                            <i class="fa fa-pencil"></i> แก้ไข
                        </button>

                        <div class="table-responsive" style=" margin-top: 10px;">
                            <table class="table" width="100%">
                                <tbody>
                                    <tr>
                                        <th style="width:20%; border-top: none;">งวดที่</th>
                                        <td style="border-top: none;">{{ $payment->period }}</td>
                                    </tr>
                                    <tr>
                                        <th>วันที่ชำระ</th>
                                        <td>{{ Diamond::parse($payment->pay_date)->thai_format('j M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>เงินต้น</th>
                                        <td>{{ number_format($payment->principle, 2, '.', ',') }} บาท</td>
                                    </tr>
                                    <tr>
                                        <th>ดอกเบี้ย</th>
                                        <td>{{ number_format($payment->interest, 2, '.', ',') }} บาท</td>
                                    </tr>
                                    <tr>
                                        <th>รวม</th>
                                        <td>{{ number_format($payment->principle + $payment->interest, 2, '.', ',') }} บาท</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- /.table -->
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-paperclip"></i> เอกสารแนบ</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <input type="hidden" id="payment_id" value="{{ $payment->id }}" />
                        <input type="file" id="attachment" class="file-upload" onchange="javascript:attachment(this);" />
                        <button class="btn btn-primary btn-flat margin-b-sm" onclick="javascript:$('#attachment').click();"
                            {{ (($is_super || $is_admin) ? '' : 'disabled') }}>
                            <i class="fa fa-plus-circle"></i> เพิ่มเอกสารแนบ
                        </button>

                        <div class="table-responsive" style=" margin-top: 10px;">
                            <table id="attachments" class="table" width="100%">
                                <tbody>
                                    <tr>
                                        <th style="width:80%; border-top: none;">เอกสารแนบ</td>
                                        <th style="width:20%; border-top: none;"><i class="fa fa-gear"></i></td>
                                    </tr>
                                    @forelse ($payment->attachments as $attachment)
                                        <tr id="item-{{ $attachment->id }}">
                                            <td><a href="{{ FileManager::get('attachments', $attachment->file) }}" target="_blank"><i class="fa fa-paperclip"></i> {{ $attachment->display }}</a></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default btn-flat btn-xs" data-tooltip="true" title="ลบ"
                                                        onclick="javascript:var result = confirm('คุณต้องการลบเอกสารนี้ใช่ไหม'); if (result) deletefile({{ $attachment->id }});">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                <!-- /.btn-group -->
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="item-empty">
                                            <td colspan="2" class="text-center">=== ไม่มีเอกสารแนบ ===</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <!-- /.table -->
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->            
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->

    <!-- Ajax Loading Status -->
    <div class="ajax-loading">
        <i class="fa fa-spinner fa-3x fa-spin"></i>
    </div>
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('[data-tooltip="true"]').tooltip(); 
    });   
    
	function attachment(file) {
		if (file.files && file.files[0]) {
			let formData = new FormData();
                formData.append('payment_id', $('#payment_id').val());
                formData.append('file', file.files[0]);

			uploadfile(formData);
		}
		else {
			alert('กรุณาเลือกเอกสารที่ต้องการ');
		}
	}

	function uploadfile(formData) {
        $.ajax({
            dataType: 'json',
            url: '/service/loan/payment/uploadfile',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
				$(".ajax-loading").css("display", "none");
				alert('เกิดข้อผิดพลาดในการอัฟโหลด');

                console.log(xhr.responseText);
                console.log(thrownError);
            },
            beforeSend: function() {
				$(".ajax-loading").css("display", "block");      
            },
            success: function(obj) {
				$(".ajax-loading").css("display", "none");
				$('#item-empty').remove();

				let item = '<tr id="item-' + obj.id + '">';
				item += '<td><a href="' + obj.href + '" target="_blank"><i class="fa fa-paperclip"></i> ' + obj.display + '</a></td>';
				item += '<td>';
				item += '<div class="btn-group">';
				item += '<button type="button" class="btn btn-default btn-flat btn-xs" data-tooltip="true" title="ลบ" ';
				item += 'onclick="javascript:var result = confirm(\'คุณต้องการลบเอกสารนี้ใช่ไหม\'); if (result) deletefile(' + obj.id + ');">';
				item += '<i class="fa fa-trash"></i>';
				item += '</button>';
				item += '</div>';
				item += '</td>';
				item += '</tr>';

				$('table#attachments tbody').append(item);
            }
		});
	}

	function deletefile(id) {
        $.ajax({
            dataType: 'json',
            url: '/service/loan/payment/deletefile',
            type: "post",
            data: {
                'id': id
            },
			beforeSend: function() {
				$(".ajax-loading").css("display", "block");      
            },
            success: function (data) {
				$(".ajax-loading").css("display", "none");
				$('#item-' + data.id).remove();

				if (data.count == 0) {
					let item = '<tr id="item-empty">';
                    item += '<td colspan="2" class="text-center">=== ไม่มีเอกสารแนบ ===</td>';
                    item += '</tr>';

					$('table#attachments tbody').append(item);
				}
            }
        });
	}
    </script>  
@endsection