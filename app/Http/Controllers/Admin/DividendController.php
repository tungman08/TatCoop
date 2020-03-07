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
use Diamond;
use Excel;
use History;
use Validator;
use MemberProperty;

class DividendController extends Controller
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

    public function index() {
        return view('admin.dividend.index', [
            'dividends' => Dividend::orderBy('rate_year', 'desc')->get()
        ]);
    }

    public function create() {
        return view('admin.dividend.create');
    }

    public function store(Request $request) {
        $rules = [
            'rate_year' => 'required|digits:4|unique:dividends,rate_year', 
            'shareholding_rate' => 'required|numeric|between:0,100',
            'loan_rate' => 'required|numeric|between:0,100',
            'release_date' => 'required|date_format:Y-m-d',
        ];

        $attributeNames = [
            'rate_year' => 'ปี ค.ศ.', 
            'shareholding_rate' => 'อัตราเงินปันผล',
            'loan_rate' => 'อัตราเงินเฉลี่ยคืน',
            'release_date' => 'วันที่เผยแพร่'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request) {
                $dividend = new Dividend();
                $dividend->rate_year = $request->input('rate_year');
                $dividend->shareholding_rate = $request->input('shareholding_rate');
                $dividend->loan_rate = $request->input('loan_rate');
                $dividend->release_date = Diamond::parse($request->input('release_date'));
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ป้อนอัตราเงินปันผล ประจำปี ' . $dividend->rate_year);
            });

            return redirect()->action('Admin\DividendController@index')
                ->with('flash_message', 'ข้อมูลอัตราเงินปันผลถูกป้อนเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function edit($id) {
        return view('admin.dividend.edit', [
            'dividend'=>Dividend::find($id)]
        );
    }

    public function update(Request $request, $id) {
        $rules = [
            'shareholding_rate' => 'required|numeric|between:0,100',
            'loan_rate' => 'required|numeric|between:0,100',
            'release_date' => 'required|date_format:Y-m-d'
        ];

        $attributeNames = [
            'shareholding_rate' => 'อัตราเงินปันผล',
            'loan_rate' => 'อัตราเงินเฉลี่ยคืน',
            'release_date' => 'วันที่เผยแพร่'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $id) {
                $dividend = Dividend::find($id);
                $dividend->shareholding_rate = $request->input('shareholding_rate');
                $dividend->loan_rate = $request->input('loan_rate');
                $dividend->release_date = Diamond::parse($request->input('release_date'));
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขอัตราเงินปันผล ประจำปี ' . $dividend->rate_year);
            });

            return redirect()->action('Admin\DividendController@index')
                ->with('flash_message', 'แก้ไขข้อมูลอัตราเงินปันผลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function destroy($id) {
        $validator = Validator::make([], []);

        $validator->after(function($validator) use ($id) {
            if (Dividend::find($id)->members->count() > 0) {
                $validator->errors()->add('used', 'ไม่สามารถลบได้เนื่องจากข้อมูลมีการใช้งานอยู่');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($id) {
                $dividend = Dividend::find($id);
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'ลบข้อมูล', 'ลบอัตราเงินปันผล ประจำปี ' . $dividend->rate_year);
    
                $dividend->delete();
            });
    
            return redirect()->action('Admin\DividendController@index')
                ->with('flash_message', 'ลบข้อมูลอัตราเงินปันผลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getMember() {
        $dividend_years = Dividend::all();
        $year = DB::select(DB::raw('select max(a.rate_year) as rate_year ' .
            'from (' .
            'select d.rate_year ' .
            'from dividends d ' .
            'inner join dividend_member dm on d.id = dm.dividend_id ' .
            'group by d.rate_year ' .
            'having count(dm.id) > 0 ' .
            ') a;'));

        return view('admin.dividend.member', [
            'dividend_years' => collect($dividend_years),
            'year' => $year[0]->rate_year
        ]);
    }

    public function getMemberDividend($member_id, Request $request) {
        $dividend_years = Dividend::all();
        $year = is_null($request->input('year')) ? 
            DB::select(DB::raw('select max(a.rate_year) as rate_year ' .
                'from (' .
                'select d.rate_year ' .
                'from dividends d ' .
                'inner join dividend_member dm on d.id = dm.dividend_id ' .
                'group by d.rate_year ' .
                'having count(dm.id) > 0 ' .
                ') a;'))[0]->rate_year : 
            $request->input('year');
        $dividend = Dividend::where('rate_year', $year)->first();
        $member = Member::find($member_id);

        $dividends = Dividendmember::where('dividend_id', $dividend->id)
            ->where('member_id', $member->id)
            ->orderBy('dividend_date')
            ->get();

        return view('admin.dividend.show', [
            'dividend_years' => collect($dividend_years),
            'member' => $member,
            'dividend' => $dividend,
            'dividends' => $dividends
        ]);
    }

    public function getMemberEdit($member_id, $dividend_id) {
        $member = Member::find($member_id);
        $dividend = Dividendmember::find($dividend_id);
        $year = Dividend::find($dividend->dividend_id)->rate_year;

        return view('admin.dividend.memberedit', [
            'year' => $year,
            'member' => $member,
            'dividend' => $dividend
        ]);
    }

    public function postMemberUpdate($member_id, $dividend_id, Request $request) {
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
            DB::transaction(function() use ($member_id, $dividend_id, $request) {
                $member = Member::find($member_id);
                $dividend = Dividendmember::find($dividend_id);
                $year = Dividend::find($dividend->dividend_id)->rate_year;     

                $dividend->shareholding = $request->input('shareholding');
                $dividend->shareholding_dividend = $request->input('shareholding_dividend');
                $dividend->interest = $request->input('interest');
                $dividend->interest_dividend = $request->input('interest_dividend');
                $dividend->save();
    
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลเงินปันผล ประจำปี ' . $year . ' ของ ' . $member->profile->fullname);
            });

            $dividend = Dividendmember::find($dividend_id);
            $year = Dividend::find($dividend->dividend_id)->rate_year;     

            return redirect()->action('Admin\DividendController@getMemberDividend', ['member_id'=>$member_id, 'year'=>$year])
                ->with('flash_message', 'แก้ไขข้อมูลเงินปันผลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
}
