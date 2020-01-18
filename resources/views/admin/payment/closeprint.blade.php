<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>:: สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด ::</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    @section('styles')
        <!-- Bootstrap Core CSS -->
        {{ Html::style(elixir('css/bootstrap.css')) }}

        <!-- Theme style -->
        {{ Html::style(elixir('css/admin-lte.css')) }}

        <style>
            .table-borderless > tbody > tr > td,
            .table-borderless > tbody > tr > th,
            .table-borderless > tfoot > tr > td,
            .table-borderless > tfoot > tr > th,
            .table-borderless > thead > tr > td,
            .table-borderless > thead > tr > th {
                border: none;
                padding: 0px 0px 8px 0px;
            }
        </style>
    @show

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body onload="window.print();">
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice">

            <div class="well">
                <i class="fa fa-money"></i> <strong>เงินที่ต้องนำมาปิดยอด</strong>
                <hr />
                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                    <label for="cal">ช่วงเวลาคำนวณดอกเบี้ย</label>
                    <input type="text" id="cal" name="cal" value="{{ $cal }}" readonly="readonly"
                        placeholder="กรุณากดปุมคำนวณ..."
                        class="form-control" />  
                </div>
                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                    <label for="principle">จำนวนเงินต้น</label>
                    <input type="text" id="principle" name="principle" value="{{ $principle }}" readonly="readonly"
                        placeholder="กรุณากดปุมคำนวณ..."
                        class="form-control" />  
                </div>
                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                    <label for="interest">จำนวนดอกเบี้ย</label>
                    <input type="text" id="interest" name="interest" value="{{ $interest }}" readonly="readonly"
                        placeholder="กรุณากดปุมคำนวณ..."
                        class="form-control" />       
                </div>
                <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                    <label for="total">รวม</label>
                    <input type="text" id="total" name="total" value="{{ $total }}" readonly="readonly"
                        placeholder="กรุณากดปุมคำนวณ..."
                        class="form-control" />       
                </div>
            </div>

        </section>
    </div>
    <!-- ./wrapper -->
</body>
</html>