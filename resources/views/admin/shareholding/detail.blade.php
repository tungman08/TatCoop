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
            ['item' => Diamond::parse($shareholding->pay_date)->thai_format('M Y'), 'link' => action('Admin\ShareholdingController@getShow', ['member_id'=>$member->id, 'paydate'=>Diamond::parse($shareholding->pay_date)->format('Y-n-1')])],
            ['item' => 'รายละเอียด', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ข้อมูลทุนเรือนหุ้น</h4>
            <p>รายละเอียดข้อมูลชำระค่าหุ้นต่างๆ ของ {{ $member->profile->fullname }} วันที่ {{ Diamond::parse($shareholding->pay_date)->thai_format('j F Y') }}</p>
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

        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-money"></i> รายละเอียดการชำระค่าหุ้น วันที่ {{ Diamond::parse($shareholding->pay_date)->thai_format('j F Y') }}</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <button class="btn btn-primary btn-flat margin-b-sm" 
                            onclick="javascript:document.location.href='{{ action('Admin\ShareholdingController@getBilling', ['member_id'=>$member->id, 'paydate'=>Diamond::parse($shareholding->pay_date)->format('Y-n-1'), 'id'=>$shareholding->id]) }}';">
                            <i class="fa fa-file-text-o"></i> ใบเสร็จรับเงินค่าหุ้น
                        </button>

                        <button class="btn btn-primary btn-flat margin-b-sm pull-right"
                            {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                            onclick="javascript:document.location.href  = '{{ action('Admin\ShareholdingController@edit', ['member_id'=>$member->id, 'id'=>$shareholding->id]) }}';">
                            <i class="fa fa-pencil"></i> แก้ไข
                        </button>

                        <div class="table-responsive" style=" margin-top: 10px;">
                            <table class="table" width="100%">
                                <tbody>
                                    <tr>
                                        <th style="width:30%; border-top: none;">วันที่ชำระ</th>
                                        <td style="border-top: none;">{{ Diamond::parse($shareholding->pay_date)->thai_format('j M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>ประเภท</th>
                                        <td><span class="label label-primary">{{ $shareholding->shareholding_type->name }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>จำนวน</th>
                                        <td>{{ number_format($shareholding->amount, 2, '.', ',') }} บาท</td>
                                    </tr>
                                    <tr>
                                        <th>ทุนเรือนหุ้นสะสม ณ ขณะนั้น</th>
                                        <td>{{ number_format($total_shareholding + $shareholding->amount, 2, '.', ',') }} บาท</td>
                                    </tr>
                                    <tr>
                                        <th>หมายเหตุ</th>
                                        <td>{{ !empty($shareholding->remark) ? $shareholding->remark : '-' }}</td>
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
                        <input type="hidden" id="shareholding_id" value="{{ $shareholding->id }}" />
                        <input type="file" id="attachment" class="file-upload" onchange="javascript:attachment(this);" />
                        <button class="btn btn-primary btn-flat margin-b-sm" 
                            {{ (($is_super || $is_admin) ? '' : 'disabled') }}
                            onclick="javascript:$('#attachment').click();">
                            <i class="fa fa-plus-circle"></i> เพิ่มเอกสารแนบ
                        </button>

                        <div class="table-responsive" style=" margin-top: 10px;">
                            <table id="attachments" class="table" width="100%">
                                <tbody>
                                    <tr>
                                        <th style="width:80%; border-top: none;">เอกสารแนบ</td>
                                        <th style="width:20%; border-top: none;"><i class="fa fa-gear"></i></td>
                                    </tr>
                                    @forelse ($shareholding->attachments as $attachment)
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
    
	function attachment(file) {
		if (file.files && file.files[0]) {
			let formData = new FormData();
                formData.append('shareholding_id', $('#shareholding_id').val());
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
            url: '/service/shareholding/uploadfile',
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
            url: '/service/shareholding/deletefile',
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