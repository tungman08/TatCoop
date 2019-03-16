<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv='content-language' content='th' /> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=2.0">
    <meta name='distribution' content='global' /> 
    <meta name="description" content="เว็บไซต์สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย" />
    <meta name="keywords" content="tatcoop,สหกรณ์ออมทรัพย์,การท่องเที่ยวแห่งประเทศไทย,สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย,สอ.สรทท.,เงินกู้,เงินฝาก,เงินปันผล" />
    <meta name="robots" content="index,nofollow,noarchive,noimageindex" />
    <meta name='revisit-after' content='7 days' />
    <meta name="author" content="Tungm@n" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>:: สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด ::</title>

    @section('styles')
        <!-- Bootstrap Core CSS -->
        {{ Html::style(elixir('css/bootstrap.css')) }}
        {{ Html::style(elixir('css/miscellaneous.css')) }}

        <!-- Font-Awesome Fonts -->
        {{ Html::style(elixir('css/font-awesome.css')) }}

        <!-- Theme style -->
        {{ Html::style(elixir('css/admin-lte.css')) }}

        <!-- jQuery-Comfirm CSS -->
        {{ Html::style(elixir('css/jquery-confirm.css')) }}

        <!-- Bootstrap DataTable CSS -->
        {!! Html::style(elixir('css/dataTables.bootstrap.css')) !!}

        <!-- jQuery-SlotMachine CSS -->
        {{ Html::style(elixir('css/jquery.slotmachine.css')) }}
    @show
</head>
<body> 
    <div class="wrapper">
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-smile-o"></i> รายชื่อผู้โชคดี</h3>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <div class="table-responsive" style=" margin-top: 10px;">
                        <table id="dataTables" class="table table-hover dataTable" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">รหัส</th>
                                    <th style="width: 60%;">ชื่อสมาชิก</th>
                                    <th style="width: 20%;">สถานะ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </aside>
        <!--/.main-sidebar -->
    </div>
    <!-- ./wrapper -->

    @section('scripts')
        <!-- jQuery -->
        {{ Html::script(elixir('js/jquery.js')) }}

        <!-- Bootstrap Core JavaScript -->
        {{ Html::script(elixir('js/bootstrap.js')) }}

        <!-- jQuery-Comfirm -->
        {{ Html::script(elixir('js/jquery-confirm.js')) }}

        <!-- Bootstrap DataTable JavaScript -->
        {!! Html::script(elixir('js/jquery.dataTables.js')) !!}
        {!! Html::script(elixir('js/dataTables.responsive.js')) !!}
        {!! Html::script(elixir('js/dataTables.bootstrap.js')) !!}

        <!-- jQuery-SlotMachine -->
        {{ Html::script(elixir('js/jquery.slotmachine.js')) }}
    @show
</body>
</html>