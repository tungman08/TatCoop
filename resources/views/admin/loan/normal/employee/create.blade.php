@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        จัดการสมาชิกสหกรณ์
        <small>เพิ่ม ลบ แก้ไข บัญชีสมาชิก สอ.สรทท.</small>
    </h1>

    @include('admin.member.breadcrumb', ['breadcrumb' => [
        ['item' => 'จัดการสมาชิกสหกรณ์', 'link' => '/admin/member'],
        ['item' => 'ข้อมูลสมาชิกสหกรณ์', 'link' => "/admin/member/{$member->id}"],
        ['item' => 'การกู้ยืม', 'link' => ''],
    ]])

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well" style="padding-bottom: 0px;">
            <h4>รายละเอียดข้อมูลสมาชิกสหกรณ์</h4>

            @include('admin.loan.info', ['member' => $member])
        </div>

        <div class="well" style="padding-bottom: 0px;">
            <h4>รายละเอียดประเภทสินเชื่อ</h4>

            @include('admin.loan.loantype', ['loantype' => $loantype])
        </div>

        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" aria-hidden="true" data-dismiss="alert" data-toggle="tooltip" title="Close">×</button>
                <h4><i class="icon fa fa-ban"></i>ข้อผิดพลาด!</h4>
                {{ Html::ul($errors->all()) }}
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-file-o"></i> ทำสัญญาเงินกู้ประเภทกู้สามัญ สำหรับพนักงาน/ลูกจ้าง ททท.</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['url' => '/admin/member/' . $member->id . '/loan', 'method' => 'post', 'class' => 'form-horizontal']) }}
                {{ Form::hidden('member_id', $member->id, ['id' => 'member_id']) }}

                @include('admin.loan.normal.employee.form', ['edit' => false])
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

    <!-- InputMask JavaScript -->
    {{ Html::script(elixir('js/jquery.inputmask.js')) }}

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

        $("[data-mask]").inputmask();
        $('form').submit(function() {
            $("[data-mask]").inputmask('remove');
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
        $('#button_add_surety').click(function() {
            getSurety(parseInt($('#add_surety').val(), 10));
        });
    });

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    function getSurety(member_id) {
        var formData = new FormData();
            formData.append('member_id', member_id);

        $.ajax({
            dataType: 'json',
            url: '/ajax/getsurety',
            type: 'post',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function(member) {

            }
        })
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