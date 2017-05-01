<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\LoanType;
use App\Member;
use App\PaymentType;
use App\Loan;
use Diamond;
use History;
use LoanCalculator;
use Validator;

class LoanController extends Controller
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

    public function getMember() {
        return view('admin.loan.member');
    }

    public function index($id) {
        $member = Member::find($id);

        return view('admin.loan.index', [
            'member' => $member,
            'loans' => Loan::where('member_id', $member->id)->orderBy('id', 'desc')->get(),
            'loantypes' => LoanType::active()->get()
        ]);
    }

    public function getCreateLoan($member_id, $loantype_id) {
        $member = Member::find($member_id);

        switch ($loantype_id) {
            case 1:
                if ($member->profile->employee->employee_type->id < 3) {
                    return redirect()->route('service.loan.create.normal.employee', [
                        'id' => $member->id,
                        'loantype_id' => $loantype_id,
                        'loan_id' => 0,
                        'step' => 1
                    ]);
                }
                else {
                    return redirect()->route('service.loan.create.normal.outsider', [
                        'id' => $member->id,
                        'loantype_id' => $loantype_id,
                        'loan_id' => 0,
                        'step' => 1
                    ]);   
                }
                break;
            case 2:
                return redirect()->route('service.loan.create.emerging', [
                    'id' => $member->id,
                    'loantype_id' => $loantype_id,
                    'loan_id' => 0,                    
                    'step' => 1
                ]);
                break;
            default:
                return redirect()->route('service.loan.create.special', [
                    'id' => $member->id,
                    'loantype_id' => $loantype_id,
                    'loan_id' => 0,
                    'step' => 1
                ]);
                break;
        }
    }

    public function postCreateLoan($id, $loantype_id, Request $request) {
        $member = Member::find($id);
        $loanType = LoanType::find($loantype_id);
        $step = $request->input('step');

        switch ($loanType->id) {
            case 1:
                if ($member->profile->employee->employee_type->id < 3) {
                    // พนักงาน ททท./ลูกจ้าง กู้สามัญ
                    switch ($step) {
                        case 2:
                            $validator = $this->validateNormalEmployeeStep2($member, $loanType, $request);

                            if ($validator->fails()) {
                                return redirect()->back()
                                    ->withErrors($validator);
                            }
                            else {
                                return redirect()->route('service.loan.create.normal.employee', [
                                    'id' => $member->id,
                                    'loantype_id' => $loanType->id,
                                    'loan_id' => $request->input('loan_id'),
                                    'step' => 3
                                ]);  
                            }
                            break;
                        case 3:
                            $validator = $this->validateNormalEmployeeStep3($member, $loanType, $request);

                            if ($validator->fails()) {
                                return redirect()->back()
                                    ->withErrors($validator)
                                    ->withInput();
                            }
                            else {
                                $loan = Loan::find($request->input('loan_id'));
                                $loan->code = $request->input('loan_code');
                                $loan->loaned_at = Diamond::now();
                                $loan->save();

                                History::addAdminHistory(Auth::guard($this->guard)->id(), 'เพิ่มข้อมูล', 'ทำสัญญากู้ยืมเลขที่ ' . $request->input('loan_code'));

                                return redirect()->action('Admin\LoanController@index', [
                                    'member_id' => $member->id
                                ]);  
                            }
                            break;
                        default:
                            $validator = $this->validateNormalEmployeeStep1($member, $loanType, $request);

                            if ($validator->fails()) {
                                return redirect()->back()
                                    ->withErrors($validator)
                                    ->withInput();
                            }
                            else {
                                $loan = new Loan();
                                $loan->member_id = $member->id;
                                $loan->loan_type_id = $loanType->id;
                                $loan->payment_type_id = $request->input('payment_type');
                                $loan->outstanding = $request->input('outstanding');
                                $loan->rate = $loanType->rate;
                                $loan->period = $request->input('period');
                                $loan->save();

                                return redirect()->route('service.loan.create.normal.employee', [
                                    'id' => $member->id,
                                    'loantype_id' => $loanType->id,
                                    'loan_id' => $loan->id,
                                    'step' => 2
                                ]);
                            }
                            break;
                    }
                }
                else {
                    // บุคคลภายนอก กู้สามัญ
                }
                break;
            case 2:
                // กู้ฉุกเฉิน
                break;
            default:
                // กู้เฉพาะ
                break;
        }
    }

    public function getCreateNormalEmployeeLoan($id, $loantype_id, Request $request) {
        $loan = Loan::find($request->input('loan_id'));

        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $step = (!is_null($loan)) ? ($loan->sureties->count() > 0) ? $request->input('step') : 2 : 1;

        return view('admin.loan.normal.employee.create', [
            'loan_id' => (!is_null($loan)) ? $loan->id : 0,
            'step' => $step,
            'member' => $member,
            'loantype' => $loantype
        ]);
    }

    public function getCreateNormalOutsiderLoan($id, $loantype_id, Request $request) {
        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $step = $request->input('step');

        return view('admin.loan.normal.outsider.create', [
            'loan_id' => $request->input('loan_id'),            
            'step' => $step,
            'member' => $member,
            'loantype' => $loantype
        ]);
    }
    
    public function getCreateEmergingLoan($id, $loantype_id, Request $request) {
        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $step = $request->input('step');        
    }
    
    public function getCreateSpecialLoan($id, $loantype_id, Request $request) {
        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $step = $request->input('step');    
    }

    protected function validateNormalEmployeeStep1($member, $loanType, $request) {
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

        $validator->after(function($validator) use ($member, $loanType, $request) {
            $outstanding = $request->input('outstanding');
            $period = $request->input('period');
            $salary = $request->input('salary');
            $net_salary = $request->input('net_salary');
            $limit = $loanType->limits()->where('cash_begin', '<=', $outstanding)
                ->where('cash_end', '>=', $outstanding)->first();
            $shareholding = $member->shareholdings->sum('amount');
            $pmt = LoanCalculator::pmt($loanType->rate, $outstanding, $period);
            $percent = ($shareholding * 100) / $outstanding;

            $max_cash = $loanType->limits->max('cash_end');
            if ($outstanding > $max_cash) {
                $validator->errors()->add('max_cash', "ไม่สามารถกู้ได้ เนื่องจากยอดเงินที่ขอกู้มากกว่าวงเงินที่สามารถกู้ได้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_cash, 2, '.', ',') . " บาท)");
            }

            $max_outstanding = $salary * 40;
            if ($outstanding > $max_outstanding) {
                $validator->errors()->add('max_outstanding', "ไม่สามารถกู้ได้ เนื่องจากเงินเดือนน้อยกว่าวงเงินที่ขอกู้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_outstanding, 2, '.', ',') . " บาท)");
            }

            if ($net_salary - $pmt < 3000) {
                $validator->errors()->add('pmt', "ไม่สามารถกู้ได้ เนื่องจากเงินเดือนสุทธิไม่พอสำหรับขอกู้ (ค่างวดต่อเดือน " . number_format($pmt, 2, '.', ',') . " บาท)");
            }

            if ($limit->period < $period) {
                $validator->errors()->add('period', "ไม่สามารถกู้ได้ เนื่องจากระยะเวลาผ่อนชำระนานกว่าที่กำหนด (จำนวนงวดสูงสุด " . number_format($limit->period, 0, '.', ',') . " งวด)");
            }

            if ($percent < $limit->shareholding) {
                $validator->errors()->add('shareholding', "ไม่สามารถกู้ได้ เนื่องจากมีทุนเรือนหุ้นไม่พอ (ต้องการหุ้น " . number_format($limit->shareholding, 1, '.', ',') . "%, ผู้กู้มี " . number_format($percent, 1, '.', ',') . "%)");
            }
        });

        return $validator;
    }

    protected function validateNormalEmployeeStep2($member, $loanType, $request) {
        $validator = Validator::make($request->all(), []);

        $validator->after(function($validator) use ($member, $loanType, $request) {
            $loan = Loan::find($request->input('loan_id'));
            $outstanding = $loan->outstanding;
            $limit = $loan->loanType->limits()->where('cash_begin', '<=', $outstanding)
                ->where('cash_end', '>=', $outstanding)->first();
            $amount_sum = ($loan->sureties->count() > 0) ? $loan->sureties->sum('pivot.amount') : 0;
            $limit_suerty = collect(explode('-', $limit->surety));

            if ($limit_suerty->count() > 0) {
                if ($limit_suerty->max() < $loan->sureties->count() || $limit_suerty->min() > $loan->sureties->count()) {
                    $validator->errors()->add('sureties', 'ต้องการผู้ค้ำประกัน ' . $limit->surety . ' คน (มีผู้ค้ำ ' . $loan->sureties->count() . ' คน)');
                }
            }

            if ($loan->outstanding != $amount_sum) {
                $validator->errors()->add('sureties_amount', 'จำนวนเงินค้ำประกันต้องเท่ากับยอดที่ต้องการกู้ (' . number_format($amount_sum, 2, '.', ',') . '/' . number_format($loan->outstanding, 2, '.', ',') . ' บาท)');
            }
        });

        return $validator;
    }

    protected function validateNormalEmployeeStep3($member, $loanType, $request) {
        $rules = [
            'loan_code' => 'required|unique:loans,code'
        ];
        
        $attributeNames = [
            'loan_code' => 'รหัสสัญญากู้ยืม'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        return $validator;
    }
}
