<?php

namespace App\Classes;

use App\AdministratorStatistic;
use App\UserStatistic;
use App\VisitorStatistic;
use App\Browser;
use App\Platform;
use stdClass;
use Client;
use DB;

class Statistic
{
    private $info;

    public function __construct() {
        $this->info = Client::info();
    }

    public function administartor($admin_id) {
        $admin = new AdministratorStatistic();
        $admin->administrator_id = $admin_id;
        $admin->ip_address = $this->info->ip_address;
        $admin->platform_id = $this->platform($this->info->platform)->id;
        $admin->browser_id = $this->browser($this->info->browser)->id;
        $admin->save();
    }

    public function user($user_id) {
        $user = new UserStatistic();
        $user->user_id = $user_id;
        $user->ip_address = $this->info->ip_address;
        $user->platform_id = $this->platform($this->info->platform)->id;
        $user->browser_id = $this->browser($this->info->browser)->id;
        $user->save();
    }

    public function visitor() {
        if (VisitorStatistic::where('session', $this->info->session)->count() == 0) {
            $visitor = new VisitorStatistic();
            $visitor->session = $this->info->session;
            $visitor->ip_address = $this->info->ip_address;
            $visitor->platform_id = $this->platform($this->info->platform)->id;
            $visitor->browser_id = $this->browser($this->info->browser)->id;
            $visitor->save();
        }
    }

    public function visitor_statistic() {
        $statistics = new stdClass();
        $statistics->total = VisitorStatistic::count();
        $statistics->today = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), Diamond::today()->toDateString())->count();
        $statistics->yesterday = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), Diamond::yesterday()->toDateString())->count();
        $statistics->thisWeek = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%v\')'), Diamond::today()->format('Y-W'))->count();
        $statistics->lastWeek = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%v\')'), Diamond::today()->subWeek()->format('Y-W'))->count();
        $statistics->thisMonth = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m\')'), Diamond::today()->format('Y-m'))->count();
        $statistics->lastMonth = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m\')'), Diamond::today()->subMonth()->format('Y-m'))->count();
        $statistics->start = Diamond::parse(VisitorStatistic::min('created_at'))->thai_format('j M Y');

        $statistics->ip_address = $this->info->ip_address;
        $statistics->platform = $this->info->platform;
        $statistics->browser = $this->info->browser;

        return $statistics;
    }

    public static function administrator_statistic() {
        $statistics = new stdClass();
        $statistics->total = AdministratorStatistic::count();
        $statistics->today = AdministratorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), Diamond::today()->toDateString())->count();
        $statistics->yesterday = AdministratorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), Diamond::yesterday()->toDateString())->count();
        $statistics->thisWeek = AdministratorStatistic::where(DB::raw('date_format(created_at, \'%Y-%v\')'), Diamond::today()->format('Y-W'))->count();
        $statistics->lastWeek = AdministratorStatistic::where(DB::raw('date_format(created_at, \'%Y-%v\')'), Diamond::today()->subWeek()->format('Y-W'))->count();
        $statistics->thisMonth = AdministratorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m\')'), Diamond::today()->format('Y-m'))->count();
        $statistics->lastMonth = AdministratorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m\')'), Diamond::today()->subMonth()->format('Y-m'))->count();
        $statistics->start = Diamond::parse(AdministratorStatistic::min('created_at'))->thai_format('j M Y');

        return $statistics;
    }

    public function counter($number) {
        $counter = str_split(str_pad(strval($number), 9, '0', STR_PAD_LEFT));
        $result = '';

        foreach ($counter as $c) {
            $result .= '<li><img class="' . self::toClass($c) . '" src="' . asset('images/blank.png') . '" /></li>';
        }

        return $result;
    }

    protected function toClass($chr) {
        switch($chr) {
            default: return 'zero'; break;
            case '1': return 'one'; break;
            case '2': return 'two'; break;
            case '3': return 'three'; break;
            case '4': return 'four'; break;
            case '5': return 'five'; break;
            case '6': return 'six'; break;
            case '7': return 'seven'; break;
            case '8': return 'eight'; break;
            case '9': return 'nine'; break;
        }
    }

    protected function platform($str) {
        $platform = Platform::where('name', $str)->first();

        if (is_null($platform)) {
            $platform = new Platform(['name' => $str]);
            $platform->save();
        }

        return $platform;
    }

    protected function browser($str) {
        $browser = Browser::where('name', $str)->first();

        if (is_null($browser)) {
            $browser = new Browser(['name' => $str]);
            $browser->save();
        }

        return $browser;
    }

    public static function visitor_chart($data, $date) {
        $days = cal_days_in_month(CAL_GREGORIAN, $date->month, $date->year);
        $visits = [];
        $result = [];

        foreach($data as $row) {
            $visits[strval($row->visit_date)] = $row->amount;
        }

        for ($i = 1; $i <= $days; $i++) {
            $result[] = [$i, (array_key_exists(strval($i), $visits)) ? $visits[strval($i)] : 0];
        }

        return $result;
    }

    public static function bar_chart($reries) {
        $amounts = [];
        $ticks = [];
        $i = 1;

        foreach ($reries as $rerie) {
            $amounts[] = [$i, $rerie->amount];
            $ticks[] = [$i, $rerie->tick];

            $i++;
        }

        return [$amounts, $ticks];
    }
}
