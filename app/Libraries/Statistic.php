<?php

namespace App\Libraries;

use App\AdministratorStatistic;
use App\UserStatistic;
use App\VisitorStatistic;
use App\Browser;
use App\Platform;
use stdClass;
use DB;

class Statistic
{
    public static function administartor($admin_id) {
        $info = Client::info();

        $admin = new AdministratorStatistic();
        $admin->administartor_id = $admin_id;
        $admin->ip_address = $info->ip_address;
        $admin->platform_id = self::platform($info->platform)->id;
        $admin->browser_id = self::browser($info->browser)->id;
        $admin->created_at = Diamond::now();
        $admin->save();
    }

    public static function user($user_id) {
        $info = Client::info();

        $user = new UserStatistic();
        $user->user_id = $user_id;
        $user->ip_address = $info->ip_address;
        $user->platform_id = self::platform($info->platform)->id;
        $user->browser_id = self::browser($info->browser)->id;
        $user->created_at = Diamond::now();
        $user->save();
    }

    public static function visitor() {
        $info = Client::info();

        if (VisitorStatistic::where('session', $info->session)->count() == 0) {
            $visitor = new VisitorStatistic();
            $visitor->session = $info->session;
            $visitor->ip_address = $info->ip_address;
            $visitor->platform_id = self::platform($info->platform)->id;
            $visitor->browser_id = self::browser($info->browser)->id;
            $visitor->created_at = Diamond::now();
            $visitor->save();
        }
    }

    public static function visitor_statistic() {
        $info = Client::info();
        
        $statistics = new stdClass();
        $statistics->total = VisitorStatistic::count();
        $statistics->today = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), Diamond::today()->toDateString())->count();
        $statistics->yesterday = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), Diamond::yesterday()->toDateString())->count();
        $statistics->thisWeek = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%v\')'), Diamond::today()->format('Y-W'))->count();
        $statistics->lastWeek = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%v\')'), Diamond::today()->subWeek()->format('Y-W'))->count();
        $statistics->thisMonth = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m\')'), Diamond::today()->format('Y-m'))->count();
        $statistics->lastMonth = VisitorStatistic::where(DB::raw('date_format(created_at, \'%Y-%m\')'), Diamond::today()->subMonth()->format('Y-m'))->count();
        $statistics->start = Diamond::parse(VisitorStatistic::min('created_at'))->thai_format('j M Y');

        $statistics->ip_address = $info->ip_address;
        $statistics->platform = $info->platform;
        $statistics->browser = $info->browser;

        return $statistics;
    }

    public static function counter($number) {
        $counter = str_split(str_pad(strval($number), 9, '0', STR_PAD_LEFT));
        $result = '';

        foreach ($counter as $c) {
            $result .= '<li><img class="' . self::toClass($c) . '" src="' . asset('images/blank.png') . '" /></li>';
        }

        return $result;
    }

    protected static function toClass($chr) {
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

    protected static function platform($str) {
        $platform = Platform::where('name', $str)->first();

        if (is_null($platform)) {
            $platform = new Platform(['name' => $str]);
            $platform->save();
        }

        return $platform;
    }

    protected static function browser($str) {
        $browser = Browser::where('name', $str)->first();

        if (is_null($browser)) {
            $browser = new Browser(['name' => $str]);
            $browser->save();
        }

        return $browser;
    }
}
