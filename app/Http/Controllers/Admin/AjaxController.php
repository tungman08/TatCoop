<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Bing;
use DB;
use Diamond;
use MemberProperty;
use App\Classes\Icon;
use Statistic;
use App\VisitorStatistic;
use App\AdministratorStatistic;
use App\Employee;
use App\Member;
use App\Profile;
use App\District;
use App\Subdistrict;
use App\Province;
use App\Postcode;
use App\Dividend;
use App\Shareholding;
use Datatables;

class AjaxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins', ['except' => 'getBackground']);
    }

    public function getDistricts(Request $request) {
        $id = $request->input('id');

        return District::where('province_id', $id)->get();
    }

    public function getSubdistricts(Request $request) {
        $id = $request->input('id');

        return Subdistrict::where('district_id', $id)->get();
    }

    public function getPostcode(Request $request) {
        $id = $request->input('id');

        $subdistrict = Subdistrict::find($id);

        return $subdistrict->postcode->code;
    }

    public function getMembers() {
        return Datatables::of(Member::with('profile', 'profile.employee.employee_type')->active()->get())->make(true);
    }

    public function getStatus(Request $request) {
        $employee = Employee::where('code', $request->input('code'))->first();
        $message = (!is_null($employee)) ? (Member::where('profile_id', $employee->profile_id)->whereNull('leave_date')->count() == 1) ? 'ยังคงเป็นสมาชิกอยู่' : '200' : '100';
        $member = null;
        
        if ($message == '200') {
            $memberx = Member::where('profile_id', $employee->profile_id)->first();
            $profile = Profile::find($employee->profile_id);

            $member = [
                'profile'=>$profile, 
                'employee'=>$employee, 
                'districts'=>District::where('province_id', $memberx->profile->province_id)->get(), 
                'subdistricts'=>Subdistrict::where('district_id', $memberx->profile->district_id)->get(), 
                'postcode'=>Postcode::find($memberx->profile->postcode_id)->code
            ];
        }

        return compact('message', 'member');
    }

    public function getDividend(Request $request) {
        $member = Member::find($request->input('id'));
        $year = $request->input('year');
        $dividends = MemberProperty::getDividend($member->id);
        
        return compact('dividends', 'dividend_rate');
    }

    /**
     * Get Bing photo of the day.
     *
     * @param  Request
     * @return Response
     */
    public function getBackground(Request $request) {

        return response()->json(Bing::setArgs(['date'=>$request->input('date')])->getImage());
    }

    public function getChart(Request $request) {
        $date = Diamond::parse($request->input('date'));
        $web = $request->input('web');

        switch ($web) {
            default:
                $_visitors = VisitorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->groupBy(DB::raw('year(created_at)'), DB::raw('month(created_at)'), DB::raw('day(created_at)'))
                    ->select(DB::raw('day(created_at) as visit_date'), DB::raw('count(id) as amount'))
                    ->get();
                $_platforms = VisitorStatistic::join('platforms', 'visitor_statistics.platform_id', '=', 'platforms.id')
                    ->whereYear('visitor_statistics.created_at', '=', $date->year)
                    ->whereMonth('visitor_statistics.created_at', '=', $date->month)
                    ->groupBy('platforms.id')
                    ->select('platforms.name as tick', DB::raw('count(visitor_statistics.id) as amount'))
                    ->get();
                $_browsers = VisitorStatistic::join('browsers', 'visitor_statistics.browser_id', '=', 'browsers.id')
                    ->whereYear('visitor_statistics.created_at', '=', $date->year)
                    ->whereMonth('visitor_statistics.created_at', '=', $date->month)
                    ->groupBy('browsers.id')
                    ->select('browsers.name as tick', DB::raw('count(visitor_statistics.id) as amount'))
                    ->get();
                break;
            case 'webapp':
                $_visitors = AdministratorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->groupBy(DB::raw('year(created_at)'), DB::raw('month(created_at)'), DB::raw('day(created_at)'))
                    ->select(DB::raw('day(created_at) as visit_date'), DB::raw('count(id) as amount'))
                    ->get();
                $_platforms = AdministratorStatistic::join('platforms', 'administrator_statistics.platform_id', '=', 'platforms.id')
                    ->whereYear('administrator_statistics.created_at', '=', $date->year)
                    ->whereMonth('administrator_statistics.created_at', '=', $date->month)
                    ->groupBy('platforms.id')
                    ->select('platforms.name as tick', DB::raw('count(administrator_statistics.id) as amount'))
                    ->get();
                $_browsers = AdministratorStatistic::join('browsers', 'administrator_statistics.browser_id', '=', 'browsers.id')
                    ->whereYear('administrator_statistics.created_at', '=', $date->year)
                    ->whereMonth('administrator_statistics.created_at', '=', $date->month)
                    ->groupBy('browsers.id')
                    ->select('browsers.name as tick', DB::raw('count(administrator_statistics.id) as amount'))
                    ->get();
                break;
        }

        $visitors = Statistic::visitor_chart($_visitors, $date);
        $platforms = Statistic::bar_chart($_platforms);
        $browsers = Statistic::bar_chart($_browsers);

        return compact('visitors', 'platforms', 'browsers');
    }

    public function getDetail(Request $request) {
        $date = Diamond::parse($request->input('date'));
        $web = $request->input('web');

        $data = [];

        switch ($web) {
            case 'website':
                $visitors = VisitorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $index = 0;
                foreach ($visitors as $visitor) {
                    $row = [
                        ++$index, 
                        Icon::user($visitor->session), 
                        '<span class="display-number">' . Diamond::parse($visitor->created_at)->thai_format('j F Y H:i น.') . '</span>', 
                        $visitor->ip_address, 
                        Icon::platform($visitor->platform->name), 
                        Icon::browser($visitor->browser->name)
                    ];

                    $data[] = $row;
                }
                break;
            case 'webapp':
                $officers = AdministratorStatistic::whereYear('created_at', '=', $date->year)
                    ->whereMonth('created_at', '=', $date->month)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $index = 0;
                foreach ($officers as $officer) {
                    $row = [
                        ++$index, 
                        Icon::user($officer->administrator->name . ' (' . $officer->administrator->email . ')'), 
                        '<span class="display-number">' . Diamond::parse($officer->created_at)->thai_format('j F Y H:i น.') . '</span>', 
                        $officer->ip_address, 
                        Icon::platform($officer->platform->name), 
                        Icon::browser($officer->browser->name)
                    ];

                    $data[] = $row;
                }
                break;
        }

        return compact('data');
    }

    public function postPassword(Request $request) {
        //$alphabet = 'abcdefghijklmnpoqrstuvwxyzABCDEFGHIJKLMNPOQRSTUVWXYZ0123456789';

        $alphabet = '0123456789';
        $pass = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return response()->json(implode($pass)); //turn the array into a string
    }
}
