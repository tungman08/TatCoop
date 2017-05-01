<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use History;
use Bing;
use LoanCalculator;
use MemberProperty;
use App\Theme;
use App\District;
use App\Subdistrict;
use App\Member;
use App\Dividend;
use App\LoanType;

class AjaxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users', ['except' => ['getBackground', 'postLoan']]);
    }

    /**
     * Get Bing photo of the day.
     *
     * @param  Request
     * @return Response
     */
    public function getBackground(Request $request) {
        $date = $request->input('date');

        return Bing::photo($date);
    }

    public function postSkin(Request $request) {
        $skin = $request->input('skin');
                $skins = Theme::all();

                $user = Auth::user();
                $user->theme_id = Theme::where('code', $skin)->first()->id;
                $user->push();

        return $skins;
    }

    public function postLoadmore(Request $request) {
        $index = intval($request->input('index'));
        $count = History::countUserHistory(Auth::guard()->id());
        $histories = History::user(Auth::guard()->id(), $index);

        return compact('index', 'count', 'histories');
    }

    public function postDistricts(Request $request) {
        $id = $request->input('id');

        return District::where('province_id', $id)->orderBy('name')->get();
    }

    public function postSubdistricts(Request $request) {
        $id = $request->input('id');

        return Subdistrict::where('district_id', $id)->orderBy('name')->get();
    }

    public function postPostcode(Request $request) {
        $id = $request->input('id');

        $subdistrict = Subdistrict::find($id);

        return $subdistrict->postcode->code;
    }

    public function postDividend(Request $request) {
        $member = Member::find($request->input('id'));
        $year = $request->input('year');
        $dividends = MemberProperty::getDividend($member->id, $year);
        $rate = Dividend::where('rate_year', $year)->first();
        $dividend_rate = (!is_null($rate)) ? $rate->rate : 0;
        
        return compact('member', 'dividends', 'dividend_rate');
    }

    public function postLoan(Request $request) {
        $loan_type_id = $request->input('loan_type');
        $outstanding = $request->input('outstanding');
        $period = $request->input('period');
        $loanType = LoanType::find($loan_type_id);

        $general = collect(LoanCalculator::payment($loanType->rate, 1, $outstanding, $period));
        $stable = collect(LoanCalculator::payment($loanType->rate, 2, $outstanding, $period));

        $info = (object)[
            'rate' => $loanType->rate,
            'general' => (object) [ 'total_pay' => $general->sum('pay'), 'total_interest' => $general->sum('interest') ],
            'stable' => (object) [ 'total_pay' => $stable->sum('pay') + $stable->sum('addon'), 'total_interest' => $stable->sum('interest') ]
        ];

        return compact('info', 'general', 'stable');
    }
}
