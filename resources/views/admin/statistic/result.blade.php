<div class="visitor_result">
    <table class="table">
        <tr>
            <th><i class="fa fa-user fa-fw"></i> วันนี้</td>
            <td class="number display-number">{{ number_format($statistics->today, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <th><i class="fa fa-user fa-fw"></i> เมื่อวานนี้</td>
            <td class="number display-number">{{ number_format($statistics->yesterday, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <th><i class="fa fa-user fa-fw"></i> สัปดาห์นี้</td>
            <td class="number display-number">{{ number_format($statistics->thisWeek, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <th><i class="fa fa-user fa-fw"></i> สัปดาห์ที่แล้ว</td>
            <td class="number display-number">{{ number_format($statistics->lastWeek, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <th><i class="fa fa-user fa-fw"></i> เดือนนี้</td>
            <td class="number display-number">{{ number_format($statistics->thisMonth, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <th><i class="fa fa-user fa-fw"></i> เดือนที่แล้ว</td>
            <td class="number display-number">{{ number_format($statistics->lastMonth, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <th><i class="fa fa-user fa-fw"></i> รวมทั้งหมด</td>
            <td class="number display-number">{{ number_format($statistics->total, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <th><i class="fa fa-user fa-fw"></i> เริ่มนับตั้งแต่</td>
            <td class="number display-number">{{ $statistics->start }}</td>
        </tr>
    </table>
                
</div>