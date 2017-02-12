<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use DB;
use Diamond;
use MemberProperty;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
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
use App\Dividend;

class MemberController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins');
    }

    public function index() {
        return view('admin.member.index');
    }

    public function create() {
        $provinces = Province::all();
        $districts = $provinces->first()->districts;
        $subdistricts = $districts->first()->subdistricts;

        return view('admin.member.create', [
            'prefixs' => Prefix::all(),
            'provinces' => $provinces,
            'districts' => $districts,
            'subdistricts' => $subdistricts,
            'employee_types' => EmployeeType::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $rules = [
            'profile.employee.code' => 'required|digits:5', 
            'profile.name' => 'required',
            'profile.lastname' => 'required', 
            'profile.citizen_code' => 'required|digits:13', 
            'shareholding' => 'required|numeric', 
            'profile.birth_date' => 'date_format:Y-m-d', 
            'profile.address' => 'required'
        ];

        $attributeNames = [
            'profile.employee.code' => 'รหัสพนักงาน', 
            'profile.name' => 'ชื่อสมาชิก',
            'profile.lastname' => 'นามสกุล',
            'profile.citizen_code' => 'หมายเลขประจำตัวประชาชน',
            'shareholding' => 'จำนวนหุ้น', 
            'profile.birth_date' => 'วันเกิด',
            'profile.address' => 'ที่อยู่', 
        ];

        $employee = Employee::where('code', $request->input('profile')['employee']['code'])->first();
        $is_employee = false;
        $member = null;

        if ($employee != null) {
            $is_employee = true;
            $member = Member::where('profile_id', $employee->profile_id)->first();
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($member) {
            if ($member != null) {
                if ($member->leave_date == null) {
                    $validator->errors()->add('membered', 'ไม่สามารถสมัครสมาชิกได้ เนื่องจากยังเป็นสมาชิกอยู่');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $is_employee, $member) {
                $profile = (!$is_employee) ? new Profile() : Profile::find($member->profile_id);
                $profile->citizen_code = $request->input('profile')['citizen_code'];
                $profile->prefix_id = $request->input('profile')['prefix_id'];
                $profile->name = $request->input('profile')['name'];
                $profile->lastname = $request->input('profile')['lastname'];
                $profile->address = $request->input('profile')['address'];
                $profile->province_id = $request->input('profile')['province_id'];
                $profile->district_id = $request->input('profile')['district_id'];
                $profile->subdistrict_id = $request->input('profile')['subdistrict_id'];
                $profile->postcode_id = empty($request->input('profile')['postcode']['code']) ? 
                    Subdistrict::find($request->input('profile')['subdistrict_id'])->postcode_id : 
                    Postcode::where('code', $request->input('profile')['postcode']['code'])->first()->id;

                if (!empty($request->input('profile')['birth_date']))
                    $profile->birth_date = Diamond::parse($request->input('profile')['birth_date']);

                $profile->save();

                if (!$is_employee) {
                    $employee = new Employee();
                    $employee->profile_id = $profile->id;
                    $employee->code = $request->input('profile')['employee']['code'];
                    $employee->employee_type_id = $request->input('profile')['employee']['employee_type_id'];
                    $employee->save();
                }

                $member = new Member();
                $member->profile_id = $profile->id;
                $member->shareholding = ($request->input('profile')['employee']['employee_type_id'] < 3) ? $request->input('shareholding') : 0;
                $member->fee = ($is_employee) ? 200 : 100;
                $member->start_date = Diamond::now();
                $member->save();
            });

            return redirect()->route('admin.member.index')
                ->with('flash_message', 
                    'คุณ ' . $request->input('profile')['name'] . ' ' . $request->input('profile')['lastname'] . 
                    ' รหัสสมาชิก ' . str_pad(Member::all()->last()->id, 5, "0", STR_PAD_LEFT) . ' เป็นสมาชิกสหกรณ์เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($id) {
        $member = Member::find($id);
        $dividend_years = Shareholding::where('member_id', $member->id)
                ->where('remark', '<>', 'ยอดยกมา')
                ->select(DB::raw('year(pay_date) as pay_year'))
                ->groupBy(DB::raw('year(pay_date)'))
                ->get();

        return view('admin.member.show', [
            'member' => $member,
            'histories' => Member::where('profile_id', $member->profile_id)->get(),
            'dividend_years' => $dividend_years,
            'dividends' => MemberProperty::getDividend($member->id),
            'tab' => 0
        ]);
    }

    public function edit($id) {
        $member = Member::find($id);
        $provinces = Province::all();
        $districts = District::where('province_id', $member->profile->province_id)->get();
        $subdistricts = Subdistrict::where('district_id', $member->profile->district_id)->get();
        $employee_types = EmployeeType::all();

        return view('admin.member.edit', [
            'member' => $member,
            'prefixs' => Prefix::all(),
            'provinces' => $provinces,
            'districts' => $districts,
            'subdistricts' => $subdistricts,
            'employee_types' => $employee_types
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'profile.employee.code' => 'required|digits:5', 
            'profile.name' => 'required',
            'profile.lastname' => 'required', 
            'profile.citizen_code' => 'required|digits:13', 
            'shareholding' => 'required|numeric', 
            'profile.birth_date' => 'date_format:Y-m-d', 
            'profile.address' => 'required', 
        ];

        $attributeNames = [
            'profile.employee.code' => 'รหัสพนักงาน', 
            'profile.name' => 'ชื่อสมาชิก',
            'profile.lastname' => 'นามสกุล',
            'profile.citizen_code' => 'หมายเลขประจำตัวประชาชน',
            'shareholding' => 'จำนวนหุ้น', 
            'profile.birth_date' => 'วันเกิด',
            'profile.address' => 'ที่อยู่', 
        ];

        $employee = Employee::where('code', $request->input('profile')['employee']['code'])->first();
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
                $member->shareholding = ($request->input('profile')['employee']['employee_type_id'] < 3) ? $request->input('shareholding') : 0;
                $member->save();

                $profile = Profile::find($member->profile_id);
                $profile->prefix_id = $request->input('profile')['prefix_id'];
                $profile->name = $request->input('profile')['name'];
                $profile->lastname = $request->input('profile')['lastname'];
                $profile->address = $request->input('profile')['address'];
                $profile->province_id = $request->input('profile')['province_id'];
                $profile->district_id = $request->input('profile')['district_id'];
                $profile->subdistrict_id = $request->input('profile')['subdistrict_id'];
                $profile->postcode_id = Postcode::where('code', $request->input('profile')['postcode']['code'])->first()->id;
                $profile->birth_date = Diamond::parse($request->input('profile')['birth_date']);
                $profile->save();

                $employee = Employee::where('profile_id', $profile->id)->first();
                $employee->code = $request->input('profile')['employee']['code'];
                $employee->employee_type_id = $request->input('profile')['employee']['employee_type_id'];
                $employee->save();
            });

            return redirect()->route('admin.member.show', ['id' => $id])
                ->with('flash_message', 'ข้อมูลคุณ ' . $request->input('profile')['name'] . ' ' . $request->input('profile')['lastname'] . ' ถูกแก้ไขเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getInactive() {
        return view('admin.member.inactive');
    }

    public function getLeave($id) {
        $member = Member::find($id);

        DB::transaction(function() use ($member) {
            $member->leave_date = Diamond::now();
            $member->save();

            $total = Shareholding::where('member_id', $member->id)->sum('amount');

            if ($total > 0) {
                $share = new Shareholding();
                $share->member_id = $member->id;
                $share->pay_date = Diamond::today();
                $share->shareholding_type_id = 2;
                $share->amount = 0 - $total;
                $share->save();
            }
        });

        $profile = Profile::find($member->profile_id);

        return redirect()->route('admin.member.index')
            ->with('flash_message', 'คุณ ' . $profile->fulleName . ' รหัสสมาชิก ' . str_pad($member->id, 5, "0", STR_PAD_LEFT) . ' ได้ลาออกจากการเป็นเป็นสมาชิกสหกรณ์แล้ว')
            ->with('callout_class', 'callout-success');
    }

    public function getShowTab($id, $tab) {
        $member = Member::find($id);
        $dividend_years = Shareholding::where('member_id', $member->id)
                ->where('remark', '<>', 'ยอดยกมา')
                ->select(DB::raw('year(pay_date) as pay_year'))
                ->groupBy(DB::raw('year(pay_date)'))
                ->get();

        return view('admin.member.show', [
            'member' => $member,
            'histories' => Member::where('profile_id', $member->profile_id)->get(),
            'dividend_years' => $dividend_years,
            'dividends' => MemberProperty::getDividend($member->id),
            'tab' => $tab
        ]);
    }

    public function getShareholding() {
        $members = Member::active()->where('shareholding', '>', 0)->get();
        $endOfMonth = Diamond::today()->endOfMonth();

        DB::transaction(function() use ($members, $endOfMonth) {
            foreach($members as $member) {
                if ($member->shareholdings()->whereYear('pay_date', '=', $endOfMonth->year)->whereMonth('pay_date', '=', $endOfMonth->month)->count() == 0) {
                    $share = new Shareholding();
                    $share->pay_date = $endOfMonth;
                    $share->shareholding_type_id = 1;
                    $share->amount = $member->shareholding * 10 ;
                    $share->remark = 'ป้อนข้อมูลอัตโนมัติ';
                    $member->shareholdings()->save($share);
                }
                else {
                    $share = $member->shareholdings()->whereYear('pay_date', '=', $endOfMonth->year)->whereMonth('pay_date', '=', $endOfMonth->month)->first();
                    $share->pay_date = $endOfMonth;
                    $share->amount = $member->shareholding * 10;
                    $share->save();
                }
            }
        });

        return redirect()->route('admin.member.index')
            ->with('flash_message', 'ทำรายการชำระค่าหุ้นอัตโนมัติประจำเดือน' . $endOfMonth->thai_format('F Y') . ' เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
