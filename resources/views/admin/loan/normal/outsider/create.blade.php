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
            ['item' => 'การกู้ยืม', 'link' => 'service/' . $member->id . '/loan'],
            ['item' => 'ทำสัญญาเงินกู้', 'link' => ''],
        ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การทำสัญญาการกู้ยืมของ {{ ($member->profile->name == '<ข้อมูลถูกลบ>') ? '<ข้อมูลถูกลบ>' :$member->profile->fullName }}</h4>

            @include('admin.loan.info', ['member' => $member])
        </div>

        <div class="box box-default collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-credit-card fa-fw"></i> รายละเอียดประเภทสินเชื่อเงินกู้</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-title -->

            <div class="box-body">
                @include('admin.loan.loantype', ['loantype' => $loantype])
            </div>          
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-file-text-o fa-fw"></i> ทำสัญญาเงินกู้ประเภทกู้สามัญ สำหรับบุคคลภายนอก</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/admin/member/' . $member->id . '/loan', 'method' => 'post', 'class' => 'form-horizontal']) }}
                {{ Form::hidden('member_id', $member->id, ['id' => 'member_id']) }}

                @include('admin.loan.normal.outsider.form', ['edit' => false])
            {{ Form::close() }}
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
    @parent

    <!-- Bootstrap DateTime Picker CSS -->
    {!! Html::style(elixir('css/bootstrap-datetimepicker.css')) !!}

    <!-- Wizard Step CSS -->
    {!! Html::style(elixir('css/stepwizard.css')) !!}
@endsection

@section('scripts')
    @parent

    <!-- Bootstrap DateTime Picker JavaScript -->
    {!! Html::script(elixir('js/moment.js')) !!}
    {!! Html::script(elixir('js/bootstrap-datetimepicker.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#datepicker').datetimepicker({
            locale: 'th',
            viewMode: 'days',
            format: 'YYYY-MM-DD'
        });

        var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn');

        allWells.hide();

        navListItems.click(function (e) {
            e.preventDefault();
            var $target = $($(this).attr('href')),
                $item = $(this);

            if (!$item.hasClass('disabled')) {
                navListItems.removeClass('btn-primary').addClass('btn-default');
                $item.addClass('btn-primary');
                allWells.hide();
                $target.show();
                $target.find('input:eq(0)').focus();
            }
        });

        allNextBtn.click(function(){
            var curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                curInputs = curStep.find("input[type='text'],input[type='url']"),
                isValid = true;

            $(".form-group").removeClass("has-error");
            for(var i=0; i<curInputs.length; i++){
                if (!curInputs[i].validity.valid){
                    isValid = false;
                    $(curInputs[i]).closest(".form-group").addClass("has-error");
                }
            }

            if (isValid) {
                var button = $(this).attr('id');

                switch (button) {
                    case 'step1':
                        step1(nextStepWizard);
                        break;
                    case 'step2':
                        nextStepWizard.removeAttr('disabled').trigger('click');
                        break;
                    case 'step3':
                        nextStepWizard.removeAttr('disabled').trigger('click');
                        break;
                }
            }
        });

        $('div.setup-panel div a.btn-primary').trigger('click');
    });

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    function step1(nextStepWizard) {
        var formData = new FormData();
            formData.append('member_id', $('#member_id').val());
            formData.append('payment_type', $('#payment_type').val());
            formData.append('outstanding', $('#outstanding').val());
            formData.append('period', $('#period').val());
            formData.append('salary', $('#salary').val());
            formData.append('net_salary', $('#net_salary').val());

        $.ajax({
            dataType: 'json',
            url: '/ajax/loannormalemployeestep1',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function(msg) {
                if (msg == true) {
                    nextStepWizard.removeAttr('disabled').trigger('click');
                }
                else {
                    $('#message_step1').html(msg);
                }
            }
        })
    }
    </script>
@endsection