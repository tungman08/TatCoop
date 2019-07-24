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
            ['item' => 'สัญญากู้ยืม', 'link' => action('Admin\LoanController@show', ['member_id'=>$member->id, 'id'=>$loan->id])],
            ['item' => 'แก้ไข PMT', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>แก้ไขค่า PMT ของสัญญาเงินกู้ประเภท {{ $loan->loanType->name }} เลขที่  {{ $loan->code }} ของ {{ $loan->member->profile->fullname }}</h4>    
            <p>
                ให้เจ้าหน้าที่สหกรณ์สามารถแก้ไขค่า PMT ของสัญญาเงินกู้ ในกรณีที่ต้องใช้ค่า PMT ที่เป็นตรงกับความเป็นจริงที่ระบบคำนวณได้ ซึ่งจะมีผลในการนำไปตัดบัญชีเงินเดือนในแต่ละเดือน<br />
                - หากต้องการใช้ค่า PMT จากระบบให้ป้อนค่าเป็น 0<br />
                - หรือหากต้องการระบุค่า PMT เองให้ป้อนค่าตามที่ต้องการลงไป<br />
                ** หมายเหตุ: ค่า PMT ของสัญญาเป็นกู้นี้ ที่ควรจะเป็นคือ <strong>{{ number_format(LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period), 2, '.', ',') }}</strong> บาท/เดือน
            </p>  
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-edit"></i> แก้ไขค่า PMT</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {{ Form::model($loan, ['action' => ['Admin\LoanController@postPmt', $member->id, $loan->id], 'method' => 'post', 'class' => 'form-horizontal']) }}
                <div class="box-body">
                    <div class="form-group">
                            {{ Form::label('pmt', 'PMT', [
                                'class'=>'col-sm-2 control-label']) 
                            }}
    
                            <div class="col-sm-10">
                                {{ Form::text('pmt', null, [
                                    'class'=>'form-control', 
                                    'placeholder'=>'ตัวอย่าง: 100000', 
                                    'autocomplete'=>'off',
                                    'onkeypress' => 'javascript:return isNumberKey(event);'])
                                }}        
                            </div>
                        </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    {{ Form::button('<i class="fa fa-save"></i> บันทึก', [
                        'id'=>'save',
                        'type' => 'submit', 
                        'class'=>'btn btn-primary btn-flat'])
                    }}
                    {{ Form::button('<i class="fa fa-ban"></i> ยกเลิก', [
                        'class'=>'btn btn-default btn-flat', 
                        'onclick'=> 'javascript:history.go(-1);'])
                    }}
                </div>
                <!-- /.box-footer -->
            {{ Form::close() }}
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

    <script>
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 8 && charCode != 127 && charCode != 45 && charCode != 46 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }  
    </script>
@endsection