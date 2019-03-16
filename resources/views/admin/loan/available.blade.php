@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ตรวจสอบความสามารถในการกู้สามัญ
            <small>ตรวจสอบความสามารถในการกู้สามัญของสมาชิก</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ตรวจสอบความสามารถในการกู้สามัญ', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>ตรวจสอบความสามารถในการกู้สามัญ</h4>
            <p>ให้ผู้ดูแลระบบสามารถตรวจสอบความสามารถในการกู้สามัญของสมาชิก</p>
        </div>
    
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-user"></i> สมาชิกสหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <label for="membercode">รหัสสมาชิกของผู้กู้</label>
                    <div class="input-group">
                        <input type="text" id="membercode" placeholder="รหัสสมาชิก 5 หลัก"
                            data-inputmask="'mask': '99999','placeholder': '0','autoUnmask': true,'removeMaskOnSubmit': true"
                            data-mask
                            autocomplete="off" class="form-control" />
                        <span class="input-group-btn">
                            <button id="btn_check" class="btn btn-default btn-flat">
                                <i class="fa fa-search"></i> ตรวจสอบ
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> ผลการตรวจสอบ</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-info">
                        <tr>
                            <th style="width:25%; border-top: 1px solid #ffffff;">รหัสสมาชิก:</th>
                            <td style="border-top: 1px solid #ffffff;"><span id="member_code"></span></td>
                        </tr>
                        <tr>
                            <th>ชื่อ-นามสกุล:</th>
                            <td><span id="member_name"></span></td>
                        </tr>
                        <tr>
                            <th>ประเภท:</th>
                            <td><span id="member_type"></span></td>
                        </tr>
                        <tr>
                            <th>สัญญาเงินกู้:</th>
                            <td><span id="loans"></span></td>
                        </tr> 
                        <tr>
                            <th>ทุนเรือนหุ้นสะสม:</th>
                            <td><span id="shareholding"></span></td>
                        </tr> 
                        <tr>
                            <th>กู้เพิ่มได้อีก:</th>
                            <td><span id="amount"></span></td>
                        </tr>    
                        <tr>
                            <th>สรุป:</th>
                            <td><span id="result"></span></td>
                        </tr>   
                    </table>
                    <!-- /.table -->
                </div>  
                <!-- /.table-responsive --> 
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->

    <!-- Salary Modal -->
    <div id="memberModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ความสามารถในการกู้สามัญ</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="member_id" />

                    <label for="member_salary">เงินเดือน</label>
                    <input type="text" id="member_salary" class="form-control margin-b-sm" autocomplete="off" />

                    <label for="member_netsalary">เงินเดือนสุทธิ</label>
                    <input type="text" id="member_netsalary" class="form-control margin-b-sm" autocomplete="off" />

                    <div class="text-center">
                        <button id="btn_member" class="btn btn-primary btn-flat margin-b-lg">
                            <i class="fa fa-file-o"></i> ตรวจสอบ
                        </button>
                    </div>
                </div>
            </div>
        </div>
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

        $("[data-mask]").inputmask();

        $('#btn_check').click(function () {
            let id = parseInt($('#membercode').val());

            if (id > 0) {
                check_member(id);
            }
        });

        $('#btn_member').click(function () {
            check_employee($('#member_id').val(), $('#member_salary').val(), $('#member_netsalary').val());
        });
    });

    function check_member(id) {
        $.ajax({
            url: '/ajax/checkmember',
            type: "post",
            data: {
                'id': id
            },
            success: function(result) {
                if (result.exist) {
                    if (!result.is_employee) {
                        check_outsider(result.id);
                    }
                    else {
                        $('#member_id').val(result.id);
                        $('#member_salary').val('');
                        $('#member_netsalary').val('');
                        $('#memberModal').modal('show');
                    }
                }
                else {
                    alert('ไม่พบสมาชิก กรุณาป้อนรหัสสมาชิกอีกครั้ง');
                }
            }
        });
    }
    
    function check_employee(id, salary, netsalary) {
        $.ajax({
            url: '/ajax/employeeloan',
            type: "post",
            data: {
                'id': id,
                'salary': salary,
                'netsalary': netsalary
            },
            success: function(result) {
                $('#member_code').html(result.member_code);
                $('#member_name').html(result.member_name);
                $('#member_type').html('<span class=\"label label-primary\">' + result.member_type + '</span>');
                $('#loans').html($.number(result.loans, 0) + ' สัญญา' + ((result.loans > 0) ? ' <a href=\"/service/' + parseInt(result.member_code) + '/loan\">[ดูรายละเอียดการกู้]</a>' : ''));
                $('#shareholding').html($.number(result.shareholding, 2) + ' บาท');
                $('#amount').html($.number(result.amount, 2) + ' บาท');
                $('#result').html(result.message);
            },
            complete: function(){
                $('#memberModal').modal('hide');
            }  
        });
    }

    function check_outsider(id) {
        $.ajax({
            url: '/ajax/outsiderloan',
            type: "post",
            data: {
                'id': id
            },
            success: function(result) {
                $('#member_code').html(result.member_code);
                $('#member_name').html(result.member_name);
                $('#member_type').html('<span class=\"label label-primary\">' + result.member_type + '</span>');
                $('#loans').html($.number(result.loans, 0) + ' สัญญา' + ((result.loans > 0) ? ' <a href=\"/service/' + parseInt(result.member_code) + '/loan\">[ดูรายละเอียดการกู้]</a>' : ''));
                $('#shareholding').html($.number(result.shareholding, 2) + ' บาท');
                $('#amount').html($.number(result.amount, 2) + ' บาท');
                $('#result').html(result.message);
            } 
        });
    }
    </script>
@endsection