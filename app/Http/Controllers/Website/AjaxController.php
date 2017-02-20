<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use History;
use Bing;
use MemberProperty;
use App\Theme;
use App\District;
use App\Subdistrict;
use App\Member;
use App\Dividend;

class AjaxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users', ['except' => 'getBackground']);
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

    public function postSkin(Request $request) {
        $skin = $request->input('skin');
                $skins = Theme::all();

                $user = Auth::user();
                $user->theme_id = Theme::where('code', $skin)->first()->id;
                $user->push();

        return $skins;
    }

    public function getLoadmore(Request $request) {
        $index = intval($request->input('index'));
        $count = History::countUserHistory(Auth::guard()->id());
        $histories = History::user(Auth::guard()->id(), $index);

        return compact('index', 'count', 'histories');
    }

    public function getDistricts(Request $request) {
        $id = $request->input('id');

        return District::where('province_id', $id)->orderBy('name')->get();
    }

    public function getSubdistricts(Request $request) {
        $id = $request->input('id');

        return Subdistrict::where('district_id', $id)->orderBy('name')->get();
    }

    public function getPostcode(Request $request) {
        $id = $request->input('id');

        $subdistrict = Subdistrict::find($id);

        return $subdistrict->postcode->code;
    }

    public function getDividend(Request $request) {
        $member = Member::find($request->input('id'));
        $year = $request->input('year');
        $dividends = MemberProperty::getDividend($member->id, $year);
        $rate = Dividend::where('rate_year', $year)->first();
        $dividend_rate = (!is_null($rate)) ? $rate->rate : 0;
        
        return compact('member', 'dividends', 'dividend_rate');
    }
}
