<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use History;
use DB;
use Diamond;
use MemberProperty;
use PDF;
use Validator;
use App\Member;
use App\Prefix;
use App\Province;
use App\District;
use App\Subdistrict;
use App\Postcode;
use App\Profile;
use App\Employee;
use App\EmployeeType;
use App\Shareholding;

class MemberController extends Controller
{
    /**
     * Only user authorize to access this section.
     *
     * @var string
     */
    protected $guard = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users');
    }

   /**
    * Responds to requests to GET /member
    */
    public function getIndex() {
        $member = Member::find(Auth::user()->member_id);

        return view('website.member.index', [
            'member' => $member,
            'histories' => Member::where('profile_id', $member->profile_id)->get(),
        ]);
    }

    /**
     * Responds to requests to GET /member/edit
     */
     public function getEdit() {
        $member = Member::find(Auth::user()->member_id);
        $provinces = Province::orderBy('name')->get();
        $districts = District::where('province_id', $member->profile->province_id)->orderBy('name')->get();
        $subdistricts = Subdistrict::where('district_id', $member->profile->district_id)->orderBy('name')->get();

         return view('website.member.edit', [
            'member' => $member,
            'prefixs' => Prefix::all(),
            'provinces' => $provinces,
            'districts' => $districts,
            'subdistricts' => $subdistricts,
         ]);
     }

     public function putUpdate(Request $request, $id) {
        $rules = [
            'profile.birth_date' => 'required|date_format:Y-m-d', 
            'profile.address' => 'required', 
        ];

        $attributeNames = [
            'profile.birth_date' => 'วันเกิด',
            'profile.address' => 'ที่อยู่', 
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
                $member = Member::find($id);
                $profile = Profile::find($member->profile_id);
                $profile->address = $request->input('profile')['address'];
                $profile->province_id = $request->input('profile')['province_id'];
                $profile->district_id = $request->input('profile')['district_id'];
                $profile->subdistrict_id = $request->input('profile')['subdistrict_id'];
                $profile->postcode_id = Postcode::where('code', $request->input('profile')['postcode']['code'])->first()->id;
                $profile->birth_date = Diamond::parse($request->input('profile')['birth_date']);
                $profile->save();

                History::addUserHistory(Auth::guard()->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลส่วนตัว');
            });

            return redirect()->route('website.member.index', ['id' => $id])
                ->with('flash_message', 'แก้ไขข้อมูลเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
     }

     public function getShareholding() {
        $member = Member::find(Auth::user()->member_id);
        $shareholdings = Shareholding::where('member_id', $member->id)
            ->select(
                DB::raw('str_to_date(concat(\'1/\', month(pay_date), \'/\', year(pay_date)), \'%d/%m/%Y\') as name'),
                DB::raw('(sum(if(member_id = 1190 and shareholding_type_id = 1, amount, 0))) as amount'),
                DB::raw('(sum(if(member_id = 1190 and shareholding_type_id = 2, amount, 0))) as amount_cash'))
            ->groupBy(DB::raw('year(pay_date)'), DB::raw('month(pay_date)'))
            ->get();

        return view('website.member.shareholding', [
            'member' => $member,
            'shareholdings' => $shareholdings
        ]);
     }

     public function getLoan() {
         $member = Member::find(Auth::user()->member_id);

         return view('website.member.loan', [
            'member' => $member
        ]);
     }

     public function getDividend() {
        $member = Member::find(Auth::user()->member_id);
        $dividend_years = [];
        $start_year = (Diamond::parse($member->start_date)->year > 2016) ? Diamond::parse($member->start_date)->year : 2016;

        for ($i = $start_year; $i <= Diamond::today()->year; $i++) {
            $dividend_years[] = (object)['pay_year' => $i];
        }

         return view('website.member.dividend', [
            'member' => $member,
            'dividend_years' => $dividend_years,
            'dividends' => MemberProperty::getDividend($member->id, Diamond::today()->year),
        ]);
     }

     public function getGuaruntee() {
        $member = Member::find(Auth::user()->member_id);

        return view('website.member.guaruntee', [
            'member' => $member
        ]);
     }

     public function getBilling($date) {
        $billdate = Diamond::parse($date);
        $member = Member::find(Auth::user()->member_id);
        $shareholdings = Shareholding::where('member_id', $member->id)
            ->whereYear('pay_date', '=', $billdate->year)
            ->whereMonth('pay_date', '=', $billdate->month)
            ->get();
        $total_shareholding = Shareholding::where('member_id', $member->id)
            ->where('pay_date', '<=', $billdate)
            ->sum('amount');

        return view('website.member.billing', [
            'member' => $member,
            'shareholdings' => $shareholdings,
            'total_shareholding' => $total_shareholding,
            'billno' => $billdate->thai_format('Y') . str_pad($shareholdings->max('id'), 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
     }

     public function getPrintBilling($date) {
        $billdate = Diamond::parse($date);
        $member = Member::find(Auth::user()->member_id);
        $shareholdings = Shareholding::where('member_id', $member->id)
            ->whereYear('pay_date', '=', $billdate->year)
            ->whereMonth('pay_date', '=', $billdate->month)
            ->get();
        $total_shareholding = Shareholding::where('member_id', $member->id)
            ->where('pay_date', '<=', $billdate)
            ->sum('amount');

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return view('website.member.print', [
            'member' => $member,
            'shareholdings' => $shareholdings,
            'total_shareholding' => $total_shareholding,
            'billno' => $billdate->thai_format('Y') . str_pad($shareholdings->max('id'), 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
     }

     public function getPdfBilling($date) {
        $billdate = Diamond::parse($date);
        $member = Member::find(Auth::user()->member_id);
        $shareholdings = Shareholding::where('member_id', $member->id)
            ->whereYear('pay_date', '=', $billdate->year)
            ->whereMonth('pay_date', '=', $billdate->month)
            ->get();
        $total_shareholding = Shareholding::where('member_id', $member->id)
            ->where('pay_date', '<=', $billdate)
            ->sum('amount');

        $data = [
            'member' => $member,
            'shareholdings' => $shareholdings,
            'total_shareholding' => $total_shareholding,
            'billno' => $billdate->thai_format('Y') . str_pad($shareholdings->max('id'), 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ];

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return PDF::loadView('website.member.pdf', $data)->download('ใบเสร็จรับเงินค่าหุ้นเดือน-' . $billdate->thai_format('M-Y') . '.pdf');
     }
}
