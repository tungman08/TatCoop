<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;
use History;
use DB;
use Diamond;
use LoanCalculator;
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
use App\User;
use App\LoanType;
use App\Loan;
use App\Payment;
use Routine;
use stdClass;
use PDF;

class MemberController extends Controller
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
        return view('admin.member.index', [
            'members' => Member::active()->count(),
            'last_member' => Member::find(Member::active()->max('id'))
        ]);
    }

    public function create() {
        $provinces = Province::orderBy('name')->get();
        $districts = District::where('province_id', $provinces->first()->id)->orderBy('name')->get();
        $subdistricts = Subdistrict::where('district_id', $districts->first()->id)->orderBy('name')->get();

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
            'start_date' => 'required|date_format:Y-m-d',
            // 'profile.employee.code' => 'required|digits:5', 
            'profile.name' => 'required',
            'profile.lastname' => 'required', 
            'profile.citizen_code' => 'required|digits:13', 
            // 'shareholding' => 'required|numeric', 
            'profile.birth_date' => 'date_format:Y-m-d', 
            'profile.address' => 'required'
        ];

        $attributeNames = [
            'start_date' => 'วันที่สมัครเป็นสมาชิก',
            //'profile.employee.code' => 'รหัสพนักงาน', 
            'profile.name' => 'ชื่อสมาชิก',
            'profile.lastname' => 'นามสกุล',
            'profile.citizen_code' => 'หมายเลขประจำตัวประชาชน',
            // 'shareholding' => 'จำนวนหุ้น', 
            'profile.birth_date' => 'วันเกิด',
            'profile.address' => 'ที่อยู่', 
        ];

        $employee_code = $request->input('profile')['employee']['code'];
        $employee = ($employee_code != '00000') ? Employee::where('code', $employee_code)->first() : null;
        $is_employee = false;
        $member = null;

        if ($employee != null) {
            $is_employee = true;
            $member = Member::where('profile_id', $employee->profile_id)->first();
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $member) {
            if ($member != null) {
                if ($member->leave_date == null) {
                    $validator->errors()->add('membered', 'ไม่สามารถสมัครสมาชิกได้ เนื่องจากยังเป็นสมาชิกอยู่');
                }
            }

            if (Diamond::parse($request->input('start_date'))->greaterThan(Diamond::today())) {
                $validator->errors()->add('calendar', 'กรุณาเลือกวันที่สมัครจากปฏิทิน ในรูปแบบ ค.ศ.');
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
                    $employee->code = ($request->input('profile')['employee']['code'] != '00000') ? $request->input('profile')['employee']['code'] : '<ข้อมูลถูกลบ>';
                    $employee->employee_type_id = $request->input('profile')['employee']['employee_type_id'];
                    $employee->save();
                }

                $member = new Member();
                $member->profile_id = $profile->id;
                //$member->shareholding = ($request->input('profile')['employee']['employee_type_id'] == 1) ? $request->input('shareholding') : 0;
                $member->shareholding = 0;
                $member->fee = ($is_employee) ? 200 : 100;
                $member->start_date = Diamond::parse($request->input('start_date'));
                $member->save();

                Routine::shareholding(Diamond::today(), $member->id);
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'สร้างข้อมูลสมาชิกใหม่', 'คุณ' . $profile->name . ' ' . $profile->lastname . ' สมัครเป็นสมาชิกสหกรณ์');
            });

            return redirect()->action('Admin\MemberController@index')
                ->with('flash_message', 
                    'คุณ ' . $request->input('profile')['name'] . ' ' . $request->input('profile')['lastname'] . 
                    ' รหัสสมาชิก ' . str_pad(Member::all()->last()->id, 5, "0", STR_PAD_LEFT) . ' เป็นสมาชิกสหกรณ์เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function show($id) {
        $member = Member::find($id);
        $account = User::where('member_id', $member->id)->first();
        $startYear = Diamond::parse($member->start_date)->year > 2019 ? Diamond::parse($member->start_date)->year : 2019;

        return view('admin.member.show', [
            'member' => $member,
            'histories' => Member::where('profile_id', $member->profile_id)->get(),
            'account' => !is_null($account) ? $account->email : '<span class="text-danger">ยังไม่ได้ลงทะเบียนใช้งาน</span>',
            'startYear' => $startYear
        ]);
    }

    public function edit($id) {
        $member = Member::find($id);
        $provinces = Province::orderBy('name')->get();
        $districts = District::where('province_id', $member->profile->province_id)->orderBy('name')->get();
        $subdistricts = Subdistrict::where('district_id', $member->profile->district_id)->orderBy('name')->get();
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
            'start_date' => 'required|date_format:Y-m-d',
            // 'profile.employee.code' => 'required|digits:5', 
            'profile.name' => 'required',
            'profile.lastname' => 'required', 
            'profile.citizen_code' => 'required|digits:13', 
            // 'shareholding' => 'required|numeric', 
            'profile.birth_date' => 'date_format:Y-m-d', 
            'profile.address' => 'required', 
        ];

        $attributeNames = [
            'start_date' => 'วันที่สมัครเป็นสมาชิก',
            // 'profile.employee.code' => 'รหัสพนักงาน', 
            'profile.name' => 'ชื่อสมาชิก',
            'profile.lastname' => 'นามสกุล',
            'profile.citizen_code' => 'หมายเลขประจำตัวประชาชน',
            // 'shareholding' => 'จำนวนหุ้น', 
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
                // $member->shareholding = ($request->input('profile')['employee']['employee_type_id'] == 1) ? $request->input('shareholding') : 0;
                $member->start_date = Diamond::parse($request->input('start_date'));
                $member->save();

                $profile = Profile::find($member->profile_id);
                $profile->citizen_code = $request->input('profile')['citizen_code'];
                $profile->prefix_id = $request->input('profile')['prefix_id'];
                $profile->name = $request->input('profile')['name'];
                $profile->lastname = $request->input('profile')['lastname'];
                $profile->address = $request->input('profile')['address'];
                $profile->province_id = $request->input('profile')['province_id'];
                $profile->district_id = $request->input('profile')['district_id'];
                $profile->subdistrict_id = $request->input('profile')['subdistrict_id'];
                $profile->postcode_id = Postcode::where('code', $request->input('profile')['postcode']['code'])->first()->id;
                $profile->birth_date = !empty($request->input('profile')['birth_date']) ? Diamond::parse($request->input('profile')['birth_date']) : null;

                $profile->save();

                $employee = Employee::where('profile_id', $profile->id)->first();
                $employee->code = (!isset($request->input('profile')['employee']['code']) || trim($request->input('profile')['employee']['code']) === '') ? '<ข้อมูลถูกลบ>' : $request->input('profile')['employee']['code'];
                $employee->employee_type_id = $request->input('profile')['employee']['employee_type_id'];
                $employee->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลของสมาชิกสหกรณ์ ชื่อ คุณ' . $profile->name . ' ' . $profile->lastname);
            });

            return redirect()->action('Admin\MemberController@show', ['id' => $id])
                ->with('flash_message', 'ข้อมูลคุณ ' . $request->input('profile')['name'] . ' ' . $request->input('profile')['lastname'] . ' ถูกแก้ไขเรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }

    public function getInactive() {
        return view('admin.member.inactive');
    }

    public function getLeave($id, $date) {
        $member = Member::find($id);
        $leave_date = Diamond::parse($date);

        if (is_null($member->leave_date)) {
            if ($member->sureties->filter(function ($value, $key) use ($member) { return is_null($value->completed_at) && $value->member_id != $member->id; })->count() > 0) {
                return redirect()->back()
                    ->with('flash_message', 'ไม่สามารถลาออกได้ เนื่องจากยังเป็นผู้ค้ำประกันการกู้ยืมของสมาชิกอื่นอยู่ กรุณาทำการเปลี่ยนผู้ค้ำประกันก่อน โดยสามารถจัดการได้ที่หัวข้อ "การค้ำประกัน"')
                    ->with('callout_class', 'callout-danger');
            }
            else {
                $payments = MemberProperty::getTotalPayment($member, $leave_date);

                return view('admin.member.leave', [
                    'member' => $member,
                    'leave_date' => $leave_date,
                    'shareholdings' => $member->shareHoldings->sum('amount'),
                    'loans' => $member->loans->filter(function ($value, $key) { return is_null($value->completed_at); }),
                    'payments' => ($payments->outstanding - $payments->principle) + $payments->interest
                ]);
            }
        }
        else {
            return redirect()->action('Admin\MemberController@show', [
                'member' => $member->id
            ]);
        }
    }

    public function postLeave($id, Request $request) {
        $member = Member::find($id);

        $rules = [
            'member_code' => 'required|exists:members,id', 
            'leave_date' => 'required|date_format:Y-m-d'
        ];

        $attributeNames = [
            'member_code' => 'รหัสสมาชิก', 
            'leave_date' => 'วันที่ลาออก'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($member, $request) {
            $member_id = $request->input("member_code");

            if (!empty($member_id)) {
                if ($member->id != intval($member_id)) {
                    if ($member->leave_date == null) {
                        $validator->errors()->add('membered', 'รหัสสมาชิกที่ป้อนไม่ตรงกับรหัสสมาชิกของผู้ต้องการลาออก');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $leave_date = Diamond::parse($request->input("leave_date"));

            DB::transaction(function() use ($member, $leave_date) {
                //ปิดยอดหุ้น
                $total = Shareholding::where('member_id', $member->id)->sum('amount');

                if ($total > 0) {
                    $share = new Shareholding();
                    $share->member_id = $member->id;
                    $share->pay_date = $leave_date;
                    $share->shareholding_type_id = 2;
                    $share->amount = 0 - $total;
                    $share->save();
                }

                //ปิดยอดเงินกู้
                $loans = $member->loans->filter(function ($value, $key) { return is_null($value->completed_at); });

                foreach ($loans as $loan) {
                    $payment = new Payment();
                    $payment->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                    $payment->pay_date = $leave_date;
                    $payment->principle = $loan->outstanding - $loan->payments->sum('principle');
                    $payment->interest = LoanCalculator::loan_interest($loan, $leave_date);
        
                    $loan->payments()->save($payment);
                    $loan->completed_at = $leave_date;
                    $loan->shareholding = 0;
                    $loan->save();
                }

                //บันทึกการลาออก
                $member->leave_date = $leave_date;
                $member->save();

                //ลบบัญชี
                $member->user()->forceDelete();

                $user = User::where('member_id', $member->id)->first();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'บันทึกการลาออกของสมาชิก', 'คุณ' . $member->profile->fullname . ' ได้ลาออกจากการเป็นเป็นสมาชิกสหกรณ์');

                if (!is_null($user)) {
                    History::addAdminHistory($user->id, 'ลาออก');
                }
            });

            return redirect()->action('Admin\MemberController@index')
                ->with('flash_message', 'คุณ ' . $member->profile->fullname . ' รหัสสมาชิก ' . str_pad($member->id, 5, "0", STR_PAD_LEFT) . ' ได้ลาออกจากการเป็นเป็นสมาชิกสหกรณ์แล้ว')
                ->with('callout_class', 'callout-success');      
        }
    }

    public function getCashflow($id, $year) {
        $member = Member::find($id);
        $loantypes = LoanType::whereYear('expire_date', '>=', Diamond::today()->year)->get();
        $shareholding = Shareholding::where('member_id', $id)->whereYear('pay_date', '<=', $year)->sum('amount');

        $debts = [];
        foreach ($loantypes as $loantype) {
            $loans = Loan::where('member_id', $id)
                ->where('loan_type_id', $loantype->id)
                ->whereNull('completed_at')
                ->whereYear('loaned_at', '<', $year)
                ->get();

            $outstanding = ($loans->count() > 0) ? $loans->sum('outstanding') : 0;
            $payments = 0;
            foreach ($loans as $loan) {
                $payments += Payment::where('loan_id', $loan->id)
                    ->whereYear('pay_date', '<=', $year)
                    ->sum('principle');
            }

            $debt = new stdClass();
            $debt->name = $loantype->name;
            $debt->balance = $outstanding - $payments;
            $debts[] = $debt;
        }

        return view('admin.member.cashflow', [
            'member' => $member,
            'year' => $year,
            'debts' => $debts,
            'shareholding' => $shareholding
        ]);
    }

    public function getPrintCashflow($id, $year) {
        $member = Member::find($id);
        $loantypes = LoanType::whereYear('expire_date', '>=', Diamond::today()->year)->get();
        $shareholding = Shareholding::where('member_id', $id)->whereYear('pay_date', '<=', $year)->sum('amount');

        $debts = [];
        foreach ($loantypes as $loantype) {
            $loans = Loan::where('member_id', $id)
                ->where('loan_type_id', $loantype->id)
                ->whereNull('completed_at')
                ->whereYear('loaned_at', '<', $year)
                ->get();

            $outstanding = ($loans->count() > 0) ? $loans->sum('outstanding') : 0;
            $payments = 0;
            foreach ($loans as $loan) {
                $payments += Payment::where('loan_id', $loan->id)
                    ->whereYear('pay_date', '<=', $year)
                    ->sum('principle');
            }

            $debt = new stdClass();
            $debt->name = $loantype->name;
            $debt->balance = $outstanding - $payments;
            $debts[] = $debt;
        }

        return view('admin.member.print', [
            'member' => $member,
            'year' => $year,
            'debts' => $debts,
            'shareholding' => $shareholding
        ]);
    }

    
    public function getPrintPdfCashflow($id, $year) {
        $member = Member::find($id);
        $loantypes = LoanType::whereYear('expire_date', '>=', Diamond::today()->year)->get();
        $shareholding = Shareholding::where('member_id', $id)->whereYear('pay_date', '<=', $year)->sum('amount');

        $debts = [];
        foreach ($loantypes as $loantype) {
            $loans = Loan::where('member_id', $id)
                ->where('loan_type_id', $loantype->id)
                ->whereNull('completed_at')
                ->whereYear('loaned_at', '<', $year)
                ->get();

            $outstanding = ($loans->count() > 0) ? $loans->sum('outstanding') : 0;
            $payments = 0;
            foreach ($loans as $loan) {
                $payments += Payment::where('loan_id', $loan->id)
                    ->whereYear('pay_date', '<=', $year)
                    ->sum('principle');
            }

            $debt = new stdClass();
            $debt->name = $loantype->name;
            $debt->balance = $outstanding - $payments;
            $debts[] = $debt;
        }

        $data = [
            'member' => $member,
            'year' => $year,
            'debts' => $debts,
            'shareholding' => $shareholding
        ];

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('admin.member.pdf', $data)->download('หนังสือขอยืนยันยอดลูกหนี้ เงินรับฝากและทุนเรือนหุ้น ปี ' . ($year + 543) . '.pdf');
    }
}
