@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จับรางวัล
            <small>สุ่มจับรางวัลให้กับสมาชิกสหกรณ์</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จับรางวัล', 'link' => action('Admin\RewardController@index')],
            ['item' => 'สร้าง', 'link' => '']
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>จับรางวัล</h4>    
            <p>ให้เจ้าหน้าที่สามารถสุ่มจับรางวัลให้กับสมาชิกสหกรณ์ เนื่องในโอกาสพิเศษ</p>  
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
                <h3 class="box-title"><i class="fa fa-plus"></i> สร้างการจับรางวัล</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            {{ Form::open(['action' => 'Admin\RewardController@store', 'method' => 'post', 'class' => 'form-horizontal']) }}
                @include('admin.reward.form', ['edit' => false])
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
        $(document).ready(function() {
            $('#add_config').prop("disabled", true);
            $('#delete_config').prop("disabled", true);

            $('#add_config').click(function () {
                var childs = $('#configs tbody tr').length;

                var row = '<tr>';
                    row += '<td style="padding-left: 0px;">';
                    row += '<input name="rewardConfigs[' + childs + '][price]" placeholder="ตัวอย่าง: 1000" ';
                    row += 'class="form-control configs" type="text" onkeyup="javascript:check_configs();" ';
                    row += 'onkeypress="javascript:return isNumberKey(event);" autocomplete="off">';
                    row += '</td>';
                    row += '<td style="padding-left: 0px;">';
                    row += '<input name="rewardConfigs[' + childs + '][amount]" placeholder="ตัวอย่าง: 100" ';
                    row += 'class="form-control configs" type="text" onkeyup="javascript:check_configs();" ';
                    row += 'onkeypress="javascript:return isNumberKey(event);" autocomplete="off">';   
                    row += '</td>';
                    row += '<td style="padding-left: 0px; text-align:center; vertical-align: middle;">';
                    row += '<input type="checkbox" name="rewardConfigs[' + childs + '][register]" value="1">';  
                    row += '</td>';
                    row += '<td style="padding-left: 0px; text-align:center; vertical-align: middle;">';
                    row += '<input type="checkbox" name="rewardConfigs[' + childs + '][special]" value="1">';  
                    row += '</td>';                   
                    row += '</tr>';

                $('#configs tbody').append(row);
                $(this).prop("disabled", true);
                childs = $('#configs tbody tr').length;

                if (childs > 1) {
                    $('#delete_config').prop("disabled", false);
                }
            });

            $('#delete_config').click(function () {
                $('#configs tbody tr:last-child').remove();
                childs = $('#configs tbody tr').length;
                check_configs();

                if (childs < 2) {
                    $('#delete_config').prop("disabled", true);
                }
            });
        });

        function check_configs() {
            var empty = 0;

            $('.configs').each(function () {
                if (this.value == "") {
                    empty++;
                } 
            });

            $('#add_config').prop("disabled", (empty != 0));
        }

        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 8 && charCode != 127&& charCode != 45 && charCode != 46 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>   
@endsection