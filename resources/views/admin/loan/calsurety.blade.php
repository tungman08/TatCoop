@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการการกู้ยืมของสมาชิกสหกรณ์ฯ
            <small>เพิ่ม ลบ แก้ไข การกู้ยืมของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการการกู้ยืม', 'link' => action('Admin\LoanController@getMember')],
            ['item' => 'การกู้ยืม', 'link' => action('Admin\LoanController@index', ['member_id'=>$member->id])],
            ['item' => 'คำนวณความสามารถในการค้ำประกัน', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>คำนวณความสามารถในการค้ำประกัน</h4>
            <p>ให้ผู้ดูแลระบบใช้สำหรับคำนวณความสามารถในการค้ำประกันของผู้ค้ำประกัน ก่อนทำการสร้างสัญญากู้สามัญ</p>
            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width: 25%; padding-left: 0px;">เงื่อนไขสำหรับผู้ค้ำประกัน:</th>
                        <td style="width: 75%;">
                            <ul class="list-info">
                                <li>ผู้ค้ำประกัน สามารถค้ำได้ไม่เกินคราวละ 2 สัญญา</li>
                                <li>เงินเดือนสุทธิของผู้ค้ำต้องมากกว่า 3,000 บาท</li>
                                <li>ผลรวมของ 40 เท่าของเงินเดือน ลบด้วยจำนวนเงินที่กำลังค้ำประกัน ณ ปัจจุบัน ของผู้ค้ำทุกคนต้องมากกว่าวงเงินที่ต้องการค้ำ</li>
                            </ul>
                        </td>
                    </tr>           
                </table>
            </div>
        </div>

        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-calculator"></i> การคำนวณความสามารถในการค้ำประกันของ {{ $member->profile->fullname }}</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                {{ Form::hidden('member_id', $member->id, [
                    'id' => 'member_id']) 
                }}

                <div class="form-group">
                    {{ Form::label('salary', 'เงินเดือนปัจจุบันของผู้ค้ำประกัน', [
                        'class'=>'control-label']) 
                    }}
                    {{ Form::text('salary', null, [
                        'id' => 'salary',
                        'required' => true,
                        'min' => 1,
                        'placeholder' => 'ตัวอย่าง: 50000',
                        'autocomplete'=>'off',
                        'onkeypress' => 'javascript:return isNumberKey(event);',
                        'class'=>'form-control'])
                    }}
                </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
                {{ Form::button('<i class="fa fa-calculator"></i> คำนวณ', [
                    'type' => 'button', 
                    'id' => 'calculate',
                    'class'=>'btn btn-primary btn-flat'])
                }}
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->
    </section>

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

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#calculate').click(function () {
            var member_id = $('#member_id').val();
            var salary = $('#salary').val();

            if (salary != '') {
                var formData = new FormData();
                formData.append('member_id', parseInt(member_id));
                formData.append('salary', parseFloat(salary));

                $.ajax({
                    dataType: 'json',
                    url: '/ajax/calculateavailable',
                    type: 'post',
                    cache: false,
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $(".ajax-loading").css("display", "block");
                    },
                    complete: function(){
                        $(".ajax-loading").css("display", "none");
                    },  
                    error: function(xhr, ajaxOption, thrownError) {
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    },
                    success: function(data) {
                        alert(data);
                    }
                });
            }
        });
    });   

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode != 8 && charCode != 127 && charCode != 45 && charCode != 46 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }    
    </script>
@endsection