@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ข้อมูลเงินปันผล/เฉลี่ยคืนของสมาชิกสหกรณ์ฯ
            <small>แสดงรายละเอียดข้อมูลเงินปันผล/เฉลี่ยคืนของสมาชิก สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'ข้อมูลเงินปันผล/เฉลี่ยคืน', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>รายละเอียดข้อมูลเงินปันผล/เฉลี่ยคืนของสมาชิกสหกรณ์</h4>

            <div class="table-responsive">
                <table class="table table-info">
                    <tr>
                        <th style="width:20%;">เงินปันผล/เฉลี่ยคืนปี:</th>
                        <td><span id="rate_year"></span></td>
                    </tr>
                    <tr>
                        <th>จ่ายเมื่อ:</th>
                        <td><span id="release_date"></span></td>
                    </tr>
                    <tr>
                        <th>เงินปันผลทั้งหมด (อัตรา <span id="shareholding_rate"></span>%):</th>
                        <td><span id="shareholding_dividend"></span> บาท</td>
                    </tr> 
                    <tr>
                        <th>เงินเฉลี่ยคืนทั้งหมด (อัตรา <span id="interest_rate"></span>%):</th>
                        <td><span id="interest_dividend"></span> บาท</td>
                    </tr>  
                    <tr>
                        <th>รวมเป็นเงินทั้งหมด:</th>
                        <td><span id="total"></span> บาท</td>
                    </tr>  
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

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-heart-o"></i> รายละเอียดข้อมูลเงินปันผล/เฉลี่ยคืนของสมาชิกสหกรณ์</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <select class="form-control" id="selectyear" autocomplete="off">
                                @foreach($dividend_years as $dividend_year)
                                    @if ($dividend_year->rate_year == $year)
                                        <option value="{{ $dividend_year->rate_year }}" selected>ปี {{ $dividend_year->rate_year + 543 }}</option>
                                    @else 
                                        <option value="{{ $dividend_year->rate_year }}">ปี {{ $dividend_year->rate_year + 543 }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <!-- /.col -->  

                        <input type="hidden" id="is_super" value="{{ $is_super }}"/>
                        <input type="hidden" id="is_admin" value="{{ $is_admin }}"/>
                        <div class="col-md-2 col-md-offset-8">
                            <button id="calculate" class="btn btn-primary btn-flat pull-right" disabled>
                                <i class="fa fa-calculator"></i> คำนวณเงินใหม่
                            </button>
                        </div>          
                        <!-- /.col -->   
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.form-group -->

                <div class="table-responsive" style=" margin-top: 10px;">
                    <table id="dataTables-users" class="table table-hover dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">รหัสสมาชิก</th>
                                <th style="width: 25%;">ชื่อสมาชิก</th>
                                <th style="width: 20%;">ประเภทสมาชิก</th>
                                <th style="width: 15%;">จำนวนเงินปันผล</th>
                                <th style="width: 15%;">จำนวนเงินเฉลี่ยคืน</th>
                                <th style="width: 15%;">รวมเงินทั้งหมด</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
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
    {!! Html::script(elixir('js/formatted-numbers.js')) !!}

    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('[data-tooltip="true"]').tooltip();
        $(".ajax-loading").css("display", "none");

        datatable($('#selectyear').val());
        summery($('#selectyear').val());

        $('#dataTables-users tbody').on('click', 'tr', function() {
            document.location.href  = '/service/dividend/member/' + parseInt($(this).children("td").first().html()).toString() + '/?year=' + $('#selectyear').val();            
        }); 

        $('#selectyear').change(function() {
            datatable($(this).val());
            summery($(this).val());
        });

        $('#calculate').click(function() {
            let result = confirm('คุณต้องการคำนวณจำนวนเงินทั้งหมดใหม่ใช่ไหม?');

            if (result) {
                refresh($('#selectyear').val());
            }             
        });
    });   

    function datatable(selectyear) {
        $('#dataTables-users').dataTable().fnDestroy();
        $('#dataTables-users').dataTable({
            //"processing": true,
            //"serverSide": true,
            "ajax": {
                "url": "/ajax/dividendlist",
                "type": "post",
                "data": {
                    "year": selectyear
                },
                beforeSend: function () {
                    $(".ajax-loading").css("display", "block");
                },
                complete: function (){
                    $(".ajax-loading").css("display", "none");
                }       
            },
            "iDisplayLength": 25,
            "createdRow": function(row, data, index) {
                $(this).css('cursor', 'pointer');
            },
            "columnDefs": [
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 },
                { type: 'formatted-num', targets: 5 }
            ],
            "columns": [
                { "data": "code" },
                { "data": "fullname" },
                { "data": "typename" },
                { "data": "shareholding" },
                { "data": "interest" },
                { "data": "total" }
            ]
        });   
    }

    function refresh(selectyear) {
        $('#dataTables-users').dataTable().fnDestroy();
        $('#dataTables-users').dataTable({
            "ajax": {
                "url": "/ajax/refreshdividend",
                "type": "post",
                "data": {
                    "year": selectyear
                },
                beforeSend: function () {
                    $(".ajax-loading").css("display", "block");
                },
                complete: function () {
                    summery(selectyear);

                    $(".ajax-loading").css("display", "none");
                }       
            },
            "iDisplayLength": 25,
            "createdRow": function(row, data, index) {
                $(this).css('cursor', 'pointer');
            },
            "columnDefs": [
                { type: 'formatted-num', targets: 3 },
                { type: 'formatted-num', targets: 4 },
                { type: 'formatted-num', targets: 5 }
            ],
            "columns": [
                { "data": "code" },
                { "data": "fullname" },
                { "data": "typename" },
                { "data": "shareholding" },
                { "data": "interest" },
                { "data": "total" }
            ]
        });  
    }

    function summery(selectyear) {
        $.ajax({
            dataType: 'json',
            url: '/ajax/dividendsummary',
            type: 'post',
            data: {
                year: selectyear
            },
            error: function(xhr, ajaxOption, thrownError) {
                console.log(xhr.responseText);
                console.log(thrownError);
            },
            success: function(data) {
                $('#rate_year').html(data.dividend.rate_year);
                $('#release_date').html(moment(data.dividend.release_date).add(543, 'year').lang('th').format('D MMM YYYY'));
                $('#shareholding_rate').html($.number(data.dividend.shareholding_rate, 2));
                $('#interest_rate').html($.number(data.dividend.interest_rate, 2)); 
                $('#shareholding_dividend').html($.number(data.dividend.shareholding_dividend, 2)); 
                $('#interest_dividend').html($.number(data.dividend.interest_dividend, 2)); 
                $('#total').html($.number(data.dividend.total, 2)); 

                if ($('#is_super').val() || $('#is_admin').val()) {
                    if (moment() > moment(data.dividend.release_date)) {
                        $('#calculate').attr("disabled", true);
                    }      
                    else {
                        $('#calculate').attr("disabled", false);
                    }
                }
            }
        });
    }

    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "formatted-num-pre": function ( a ) {
            a = (a === "-" || a === "") ? 0 : a.replace(/[^\d\-\.]/g, "");
            return parseFloat( a );
        },

        "formatted-num-asc": function ( a, b ) {
            return a - b;
        },

        "formatted-num-desc": function ( a, b ) {
            return b - a;
        }
    });
    </script>
@endsection