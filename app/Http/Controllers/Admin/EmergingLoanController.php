<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Loan;
use App\Member;
use Auth;
use Diamond;
use History;
use LoanCalculator;
use LoanManager;
use Validator;
use Routine;

class EmergingLoanController extends Controller
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

    public function getCreateEmployeeLoan($member_id, Request $request) {
        $loan = is_null(Loan::find($request->input('loan_id'))) 
            ? LoanManager::create_loan($member_id, 2) 
            : Loan::find($request->input('loan_id'));
        $member = Member::find($member_id);

        switch (intval($request->input('step'))) {
            default:
                return view('admin.loan.create.emerging.employee.create', [
                    'step' => 1,
                    'loan' => $loan
                ]);
                break;
            case 2:
                return view('admin.loan.create.emerging.employee.create', [
                    'step' => 2,
                    'loan' => $loan
                ]);
                break;
        }
    }

    public function postCreateEmployeeLoan(Request $request) {
        switch (intval($request->input('step'))) {
            default:
                return $this->validateEmployeeStep1($request);
                break;
            case 2:
                return $this->validateEmployeeStep2($request);
                break;
        }
    }
    
    protected function validateEmployeeStep1(Request $request) {
        $loan = Loan::find($request->input('id'));

        $rules = [
            'outstanding' => 'required|numeric',
            'period' => 'required|numeric',
            'salary' => 'required|numeric',
            'net_salary' => 'required|numeric'
        ];
        
        $attributeNames = [
            'outstanding' => 'ยอดเงินที่ต้องการขอกู้',
            'period' => 'จำนวนงวดการผ่อนชำระ',
            'salary' => 'เงินเดือนของผู้กู้',
            'net_salary' => 'เงินเดือนสุทธิของผู้กู้หักทุกอย่างใน slip'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $loan) {
            $outstanding = $request->input('outstanding');
            $period = $request->input('period');
            $salary = $request->input('salary');
            $netsalary = $request->input('net_salary');
            $payment_type = $request->input('payment_type_id');

            // ตรวจสอบวงเงินที่เปิดให้กู้สูงสุด
            LoanManager::check_maxcash($validator, $loan, $outstanding);

            // ตรวจสอบจำนวนงวดผ่อนชำระ
            LoanManager::check_period($validator, $loan, $outstanding, $period);

            // ตรวจสอบเงินเดือนผู้กู้ (วงเงินที่ต้องการกู้ต้องไม่เกิน 3 เท่าของเงินเดือนและสูงสุดไม่เกิน 100,000 บาท)
            LoanManager::check_salaryabnormal($validator, $loan, $salary, $outstanding);

            // ตรวจสอบเงินเดือนสุทธิของผู้กู้ ลบด้วยยอดใหม่ที่ต้องหักแล้วต้องไม่น้อยกว่า 3,000 บาท
            LoanManager::check_netsalary($validator, $loan, $netsalary, $outstanding, $period, $payment_type);
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $surety_type = intval($request->input('shareholding_type'));

            $loan->payment_type_id = $request->input('payment_type_id');
            $loan->outstanding = $request->input('outstanding');
            $loan->rate = $loan->loanType->rate;
            $loan->period = $request->input('period');
            $loan->step = 1;
            $loan->shareholding = true;
            $loan->save();

            return redirect()->route('service.loan.create.emerging.employee', [
                'member_id' => $loan->member_id,
                'loan_id' => $loan->id,
                'step' => 2
            ]);
        }
    }

    protected function validateEmployeeStep2(Request $request) {
        $rules = [
            //'loan_code' => 'required|unique:loans,code',
            'loaned_at' => 'required|date_format:Y-m-d'
        ];
        
        $attributeNames = [
            //'loan_code' => 'รหัสสัญญากู้ยืม',
            'loaned_at' => 'วันที่ทำสัญญา'
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
                $loan = Loan::find($request->input('id'));
                $loan->code = $request->input('loan_code');
                $loan->loaned_at = Diamond::parse($request->input('loaned_at'));
                $loan->step = 2;
                $loan->save();

                Routine::createloan(Diamond::parse($request->input('loaned_at')), $loan->id);
                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ทำสัญญากู้ยืมเลขที่ ' . $request->input('loan_code'));
            });

            $loan = Loan::find($request->input('id'));

            return redirect()->action('Admin\LoanController@index', [
                'member_id' => $loan->member_id
            ]);  
        }
    }

    public function getCreateOutsiderLoan($member_id, Request $request) {
        $loan = is_null(Loan::find($request->input('loan_id'))) 
            ? LoanManager::create_loan($member_id, 2) 
            : Loan::find($request->input('loan_id'));
        $member = Member::find($member_id);

        switch (intval($request->input('step'))) {
            default:
                return view('admin.loan.create.emerging.outsider.create', [
                    'step' => 1,
                    'loan' => $loan
                ]);
                break;
            case 2:
                return view('admin.loan.create.emerging.outsider.create', [
                    'step' => 2,
                    'loan' => $loan
                ]);
                break;
        }
    }

    public function postCreateOutsiderLoan(Request $request) {
        switch (intval($request->input('step'))) {
            default:
                return $this->validateOutsiderStep1($request);
                break;
            case 2:
                return $this->validateOutsiderStep2($request);
                break;
        }
    }

    protected function validateOutsiderStep1(Request $request) {
        $loan = Loan::find($request->input('id'));

        $rules = [
            'outstanding' => 'required|numeric',
            'period' => 'required|numeric'
        ];
        
        $attributeNames = [
            'outstanding' => 'ยอดเงินที่ต้องการขอกู้',
            'period' => 'จำนวนงวดการผ่อนชำระ'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function($validator) use ($request, $loan) {
            $outstanding = $request->input('outstanding');
            $period = $request->input('period');
            $payment_type = $request->input('payment_type_id');

            // ตรวจสอบวงเงินที่เปิดให้กู้สูงสุด
            LoanManager::check_maxcash($validator, $loan, $outstanding);

            // ตรวจสอบจำนวนงวดผ่อนชำระ
            LoanManager::check_period($validator, $loan, $outstanding, $period);

            // ตรวจสอบจำนวนหุ้นที่ต้องใช้
            LoanManager::check_outsider_selfsurety($validator, $loan, $outstanding);
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            $surety_type = intval($request->input('shareholding_type'));

            $loan->payment_type_id = $request->input('payment_type_id');
            $loan->outstanding = $request->input('outstanding');
            $loan->rate = $loan->loanType->rate;
            $loan->period = $request->input('period');
            $loan->step = 1;
            $loan->shareholding = true;
            $loan->save();

            return redirect()->route('service.loan.create.emerging.employee', [
                'member_id' => $loan->member_id,
                'loan_id' => $loan->id,
                'step' => 2
            ]);
        }
    }

    protected function validateOutsiderStep2(Request $request) {
        $rules = [
            //'loan_code' => 'required|unique:loans,code',
            'loaned_at' => 'required|date_format:Y-m-d'
        ];
        
        $attributeNames = [
            //'loan_code' => 'รหัสสัญญากู้ยืม',
            'loaned_at' => 'วันที่ทำสัญญา'
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
                $loan = Loan::find($request->input('id'));
                $loan->code = $request->input('loan_code');
                $loan->loaned_at = Diamond::parse($request->input('loaned_at'));
                $loan->step = 2;
                $loan->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ทำสัญญากู้ยืมเลขที่ ' . $request->input('loan_code'));
            });

            $loan = Loan::find($request->input('id'));

            return redirect()->action('Admin\LoanController@index', [
                'member_id' => $loan->member_id
            ]);  
        }
    }
}
