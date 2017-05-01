<div class="container">
    <div class="row">
        <div class="col-md-6 title">
            สหกรณ์ออมทรัพย์สหภาพแรงงานรัฐวิสาหกิจการท่องเที่ยวแห่งประเทศไทย จำกัด
        </div>
        <div class="col-md-6 counter">
            <ul class="list-inline counter-buttons">จำนวนผู้เข้าชม:&nbsp;
                <li class="dropup">
                    <a href="#counterModal" data-toggle="modal" data-tooltip="true" data-placement="top" title="สถิติจำนวนผู้เข้าชม">
                        <ul class="list-inline quicklinks">
                            {!! Statistic::counter($statistics->total) !!}
                        </ul>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Counter Modal -->
<div id="counterModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">สถิติจำนวนผู้เข้าชม</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> วันนี้</td>
                        <td class="number">{{ number_format($statistics->today, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> เมื่อวานนี้</td>
                        <td class="number">{{ number_format($statistics->yesterday, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> สัปดาห์นี้</td>
                        <td class="number">{{ number_format($statistics->thisWeek, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> สัปดาห์ที่แล้ว</td>
                        <td class="number">{{ number_format($statistics->lastWeek, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> เดือนนี้</td>
                        <td class="number">{{ number_format($statistics->thisMonth, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> เดือนที่แล้ว</td>
                        <td class="number">{{ number_format($statistics->lastMonth, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> รวมทั้งหมด</td>
                        <td class="number">{{ number_format($statistics->total, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="title"><i class="fa fa-user fa-fw"></i> เริ่มนับตั้งแต่</td>
                        <td class="number">{{ $statistics->start }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="modal-hr" /></td>
                    </tr>
                    <tr>
                        <td class="title">IP Address</td>
                        <td class="number">{{ $statistics->ip_address }}</td>
                    </tr>
                    <tr>
                        <td class="title">Web browser</td>
                        <td class="number">{{ $statistics->browser }}</td>
                    </tr>
                    <tr>
                        <td class="title">Platform</td>
                        <td class="number">{{ $statistics->platform }}</td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</div>
