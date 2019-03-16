@extends('admin.layouts.layout')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            จัดการเงื่อนไขการค้ำประกัน
            <small>แก้ไข รายละเอียดเงื่อนไขการค้ำประกันเงินกู้ของ สอ.สรทท.</small>
        </h1>

        @include('admin.layouts.breadcrumb', ['breadcrumb' => [
            ['item' => 'จัดการเงื่อนไขการค้ำประกัน', 'link' => ''],
        ]])
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="well">
            <h4>การจัดรายละเอียดเงื่อนไขการค้ำประกันของสหกรณ์</h4>
            <p>ให้ผู้ดูแลระบบสามารถ แก้ไข รายละเอียดเงื่อนไขการค้ำประกันเงินกู้ของสหกรณ์ เพื่อใช้ในการตรวจสอบความสามารถในการค้ำประกันของผู้ค้ำ</p>
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

        <div class="row">
            @foreach ($bailsmans as $bailsman)
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-male"></i> เงื่อนไขประเภท{{ $bailsman->employeeType->name }}</h3>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">
                            <strong>กรณีค้ำประกันตนเอง</strong>
                            <ul class="list-info" style="margin-top: 5px;">
                                @if ($bailsman->self_type == 'salary')
                                    <li style="padding: 5px;">
                                        ใช้เงินเดือนค้ำประกันได้ไม่เกิน <strong>{{ number_format($bailsman->self_rate, 0, '.', ',') }}</strong> เท่าของเงินเดือน
                                        และไม่เกิน <strong>{{ number_format($bailsman->self_maxguaruntee, 2, '.', ',') }}</strong> บาท
                                    </li>
                                    <li style="padding: 5px;">เงินเดือนสุทธิหลังหักค่างวดแล้ว ต้องเหลือมากกว่า <strong>{{ number_format($bailsman->self_netsalary, 2, '.', ',') }}</strong> บาท</li>
                                @else
                                    <li style="padding: 5px;">
                                        ใช้หุ้นค้ำประกันได้ไม่เกิน <strong>{{ number_format($bailsman->self_rate * 100, 0, '.', ',') }}%</strong> ของทุนเรือนหุ้นคงเหลือในการใช้ค้ำประกันในสัญญาเงินกู้อื่นๆ
                                        และไม่เกิน <strong>{{ number_format($bailsman->self_maxguaruntee, 2, '.', ',') }}</strong> บาท
                                    </li>
                                @endif
                            </ul>
                            <!-- /.list-info -->

                            <strong>กรณีค้ำประกันผู้อื่น</strong>
                            <ul class="list-info" style="margin-top: 5px;">
                                @if ($bailsman->other_type == 'salary')
                                    <li style="padding: 5px;">
                                        ใช้เงินเดือนค้ำประกันได้ไม่เกิน <strong>{{ number_format($bailsman->other_rate, 0, '.', ',') }}</strong> เท่าของเงินเดือน
                                        และไม่เกิน <strong>{{ number_format($bailsman->other_maxguaruntee, 2, '.', ',') }}</strong> บาท
                                    </li>
                                    <li style="padding: 5px;">เงินเดือนสุทธิหลังหักค่างวดแล้ว ต้องเหลือมากกว่า <strong>{{ number_format($bailsman->other_netsalary, 2, '.', ',') }}</strong> บาท</li>
                                @else
                                    <li style="padding: 5px;">
                                        ใช้หุ้นค้ำประกันได้ไม่เกิน <strong>{{ number_format($bailsman->other_rate * 100, 0, '.', ',') }}%</strong> ของทุนเรือนหุ้นคงเหลือในการใช้ค้ำประกันในสัญญาเงินกู้อื่นๆ
                                        และไม่เกิน <strong>{{ number_format($bailsman->other_maxguaruntee, 2, '.', ',') }}</strong> บาท
                                    </li>
                                @endif
                            </ul>
                            <!-- /.list-info -->
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <a class="btn btn-primary btn-flat" href="{{ action('Admin\BailsmanController@edit', ['id' => $bailsman->id]) }}">
                                <i class="fa fa-pencil"></i> แก้ไข
                            </a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            @endforeach
        </div>
        <!-- /.row -->
        
    </section>
    <!-- /.content -->
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection