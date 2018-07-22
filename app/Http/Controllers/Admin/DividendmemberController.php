<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Dividend;
use App\Dividendmember;
use App\Member;
use Auth;
use DB;
use History;
use stdClass;
use Validator;

class DividendmemberController extends Controller
{
    /**
     * Only administartor authorize to access this section.
     *
     * @var string
     */
     protected $guard = 'admins';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins');
    }

    public function getYear() {
        $dividends = DB::table('dividend_member')
            ->join('dividends', 'dividend_member.dividend_id', '=', 'dividends.id')
            ->groupBy('dividends.id', 'dividends.rate_year')
            ->select('dividends.id',
                'dividends.rate_year', 
                'dividends.shareholding_rate', 
                DB::raw('sum(dividend_member.shareholding_dividend) as shareholding_dividend'), 
                'dividends.loan_rate', 
                DB::raw('sum(dividend_member.interest_dividend) as interest_dividend'))
            ->get();

        return view('admin.dividendmember.year', [
            'dividends' => $dividends
        ]);
    }

    public function getIndex($devidend_id) {
        $dividend = DB::table('dividend_member')
            ->join('dividends', 'dividend_member.dividend_id', '=', 'dividends.id')
            ->groupBy('dividends.id', 'dividends.rate_year')
            ->where('dividends.id', $devidend_id)
            ->select('dividends.id',
                'dividends.rate_year', 
                'dividends.shareholding_rate', 
                DB::raw('sum(dividend_member.shareholding_dividend) as shareholding_dividend'), 
                'dividends.loan_rate', 
                DB::raw('sum(dividend_member.interest_dividend) as interest_dividend'))
            ->first();
        $members = Member::active()->get();
        $dividends = collect([]);

        foreach ($members as $member) {
            $m_dividend = Dividendmember::where('dividend_id', $dividend->id)
                ->where('member_id', $member->id)
                ->get();

            $shareholding_dividend = $m_dividend->sum('shareholding_dividend');
            $interest_dividend = $m_dividend->sum('interest_dividend');

            $item = new stdClass();
            $item->id = $member->id;
            $item->code = $member->memberCode;
            $item->fullname = $member->profile->fullName;
            $item->typename = $member->profile->employee->employee_type->name;
            $item->shareholding = $shareholding_dividend;
            $item->interest = $interest_dividend;
            $dividends->push($item);
        }

        return view('admin.dividendmember.index', [
            'dividend' => $dividend,
            'members' => $dividends
        ]);
    }

    public function getShow($dividend_id, $member_id) {
        $detail = Member::find($member_id);
        $dividend = Dividend::find($dividend_id);
        $dividends = Dividendmember::where('dividend_id', $dividend_id)
            ->where('member_id', $member_id)
            ->get();

        $member = new stdClass();
        $member->id = $detail->id;
        $member->fullname = $detail->profile->fullName;
        $member->dividends = $dividends;

        return view('admin.dividendmember.show', [
            'dividend' => $dividend,
            'member' => $member
        ]);
    }

    public function getEdit($dividend_id, $member_id, $m_dividend_id) {
        $detail = Member::find($member_id);
        $dividend = Dividend::find($dividend_id);
        $dividends = Dividendmember::where('dividend_id', $dividend_id)
            ->where('member_id', $member_id)
            ->get();
        $m_dividend = Dividendmember::find($m_dividend_id);

        $member = new stdClass();
        $member->id = $detail->id;
        $member->fullname = $detail->profile->fullName;
        $member->dividends = $dividends;

        return view('admin.dividendmember.edit', [
            'dividend' => $dividend,
            'member' => $member,
            'm_dividend' => $m_dividend
        ]);
    }

    public function postUpdate(Request $request, $dividend_id, $member_id, $m_dividend_id) {
        $rules = [
            'shareholding' => 'required|numeric',
            'shareholding_dividend' => 'required|numeric',
            'interest' => 'required|numeric',
            'interest_dividend' => 'required|numeric'
        ];

        $attributeNames = [
            'shareholding' => 'เงินค่าหุ้น',
            'shareholding_dividend' => 'เงินปันผล',
            'interest' => 'ดอกเบี้ยเงินกู้',
            'interest_dividend' => 'เงินเฉลี่ยคืน'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $dividend_id, $member_id, $m_dividend_id) {
                $member = Member::find($member_id);
                $dividend = Dividend::find($dividend_id);

                $m_dividend = Dividendmember::find($m_dividend_id);
                $m_dividend->shareholding = $request->input('shareholding');
                $m_dividend->shareholding_dividend = $request->input('shareholding_dividend');
                $m_dividend->interest = $request->input('interest');
                $m_dividend->interest_dividend = $request->input('interest_dividend');
                $m_dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลเงินปันผล ประจำปี ' . $dividend->rate_year . ' ของ ' . $member->profile->fullName);
            });

            return redirect()->action('Admin\DividendmemberController@getShow', [$dividend_id, $member_id])
                ->with('flash_message', 'แก้ไขข้อมูลเงินปันผลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
}
