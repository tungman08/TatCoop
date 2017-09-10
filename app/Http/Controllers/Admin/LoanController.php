<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\LoanType;
use App\Member;
use App\PaymentType;
use App\Loan;
use DB;
use Auth;
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
            'loans' => Loan::where('member_id', $member->id)->whereNotNull('code')->orderBy('id', 'desc')->get(),
            'loantypes' => LoanType::active()->get()
        ]);
    }

    public function show($id, $loan_id) {
        $member = Member::find($id);
        $loan = Loan::find($loan_id);

         return view('admin.loan.show', [
            'member' => $member,
            'loan' => $loan,
        ]);       
    }

    public function getCreateLoan($member_id, $loantype_id) {
        $member = Member::find($member_id);
        $loans = $member->loans->filter(function ($value, $key) use ($loantype_id) { return $value->loan_type_id == $loantype_id  && !is_null($value->code) && is_null($value->completed_at); });

        switch ($loantype_id) {
            case 1: // กู้สามัญ
                if ($loans->count() == 0) {
                    // กู้ใหม่
                    return redirect()->route('service.loan.create.normal', [
                        'id' => $member->id,
                        'loantype_id' => $loantype_id,
                        'loan_id' => 0,                    
                        'step' => 1
                    ]);
                }
                else if ($loans->count() == 1) {
                    $loan = $loans->first();
                    $period = ($loan->payments->count() / $loan->period) * 100;
                    $payment = ($loan->payments->sum('principle') / $loan->outstanding) * 100;

                    if ($period >= 10 || $payment >= 10) {
                        // รีไฟแนนซ์ เงื่อนไข ผ่อนไปแล้ว 1 ใน 10 งวด หรือ ชำระเงินไปแล้ว 10%
                        return redirect()->route('service.loan.refinance', [
                            'id' => $member->id
                        ]);
                    }
                    else {
                        // ไม่สามารถกู้ได้
                        return redirect()->back()
                            ->with('flash_message', 'ไม่สามารถกู้ได้ เนื่องจากมีสัญญาเงินกู้เดิมอยู่ และยังไม่สามารถทำรีไฟแนนซ์ได้')
                            ->with('callout_class', 'callout-danger'); 
                    }
                }
                else {
                    // ไม่สามารถกู้ได้
                    return redirect()->redirect()->back()
                        ->with('flash_message', 'ไม่สามารถกู้ได้ เนื่องจากมีสัญญาเงินกู้เดิมอยู่ และยังไม่สามารถทำรีไฟแนนซ์ได้')
                        ->with('callout_class', 'callout-danger');
                }
                break;
            case 2: // กู้ฉุกเฉิน
                if ($loans->count() == 0) {
                    // กู้ใหม่
                    return redirect()->route('service.loan.create.emerging', [
                        'id' => $member->id,
                        'loantype_id' => $loantype_id,
                        'loan_id' => 0,                    
                        'step' => 1
                    ]);
                }
                else {
                    // ไม่สามารถกู้ได้
                    return redirect()->redirect()->back()
                        ->with('flash_message', 'ไม่สามารถกู้ได้ กรุณาปิดยอดสัญญาเงินกู้ฉุกเฉินก่อน')
                        ->with('callout_class', 'callout-danger');
                }
                break;
            default: // กู้เฉพาะกิจ
                if ($loans->count() == 0) {
                    // กู้ใหม่
                    return redirect()->route('service.loan.create.special', [
                        'id' => $member->id,
                        'loantype_id' => $loantype_id,
                        'loan_id' => 0,
                        'step' => 1
                    ]);
                }
                else {
                    // ไม่สามารถกู้ได้
                    return redirect()->redirect()->back()
                        ->with('flash_message', 'ไม่สามารถกู้ได้ กรุณาปิดยอดสัญญาเงินกู้เฉพาะกิจก่อน')
                        ->with('callout_class', 'callout-danger');
                }
                break;
        }
    }

    public function postCreateLoan($id, $loantype_id, Request $request) {
        $member = Member::find($id);
        $loanType = LoanType::find($loantype_id);
        $step = $request->input('step');

        switch ($loanType->id) {
            case 1:
                // กู้สามัญ
                switch ($step) {
                    case 2:
                        $validator = $this->validateStep2($member, $loanType, $request);

                        if ($validator->fails()) {
                            return redirect()->back()
                                ->withErrors($validator);
                        }
                        else {
                            return redirect()->route('service.loan.create.normal', [
                                'id' => $member->id,
                                'loantype_id' => $loanType->id,
                                'loan_id' => $request->input('loan_id'),
                                'step' => 3
                            ]);  
                        }
                        break;
                    case 3:
                        $validator = $this->validateStep3($member, $loanType, $request);

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
                        $validator = ($member->profile->employee->employee_type_id == 1) 
                            ? $this->validateNormalEmployeeStep1($member, $loanType, $request) 
                            : $this->validateNormalOutsiderStep1($member, $loanType, $request);

                        if ($validator->fails()) {
                            return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                        }
                        else {
                            $incompletes = Loan::where('member_id', $member->id)
                                ->whereNull('code')
                                ->delete();

                            $loan = new Loan();
                            $loan->member_id = $member->id;
                            $loan->loan_type_id = $loanType->id;
                            $loan->payment_type_id = $request->input('payment_type');
                            $loan->code = null;
                            $loan->outstanding = $request->input('outstanding');
                            $loan->rate = $loanType->rate;
                            $loan->period = $request->input('period');
                            $loan->save();

                            return redirect()->route('service.loan.create.normal', [
                                'id' => $member->id,
                                'loantype_id' => $loanType->id,
                                'loan_id' => $loan->id,
                                'step' => 2
                            ]);
                        }
                        break;
                }
                break;
            case 2:
                // กู้ฉุกเฉิน
                switch ($step) {
                    case 2:
                        $validator = $this->validateStep2($member, $loanType, $request);

                        if ($validator->fails()) {
                            return redirect()->back()
                                ->withErrors($validator);
                        }
                        else {
                            return redirect()->route('service.loan.create.emerging', [
                                'id' => $member->id,
                                'loantype_id' => $loanType->id,
                                'loan_id' => $request->input('loan_id'),
                                'step' => 3
                            ]);  
                        }
                        break;
                    case 3:
                        $validator = $this->validateStep3($member, $loanType, $request);

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
                        $validator = $this->validateStep1($member, $loanType, $request);

                        if ($validator->fails()) {
                            return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                        }
                        else {
                            $incompletes = Loan::where('member_id', $member->id)
                                ->whereNull('code')
                                ->delete();

                            $loan = new Loan();
                            $loan->member_id = $member->id;
                            $loan->loan_type_id = $loanType->id;
                            $loan->payment_type_id = $request->input('payment_type');
                            $loan->code = null;
                            $loan->outstanding = $request->input('outstanding');
                            $loan->rate = $loanType->rate;
                            $loan->period = $request->input('period');
                            $loan->save();

                            // ค้ำประกันตนเอง
                            $loan->sureties()->attach($member->id, ['amount' => $request->input('outstanding'), 'yourself' => true]);

                            return redirect()->route('service.loan.create.emerging', [
                                'id' => $member->id,
                                'loantype_id' => $loanType->id,
                                'loan_id' => $loan->id,
                                'step' => 2
                            ]);
                        }
                    break;
                }
                break;
            default:
                // กู้เฉพาะ
                switch ($step) {
                    case 2:
                        $validator = $this->validateStep2($member, $loanType, $request);

                        if ($validator->fails()) {
                            return redirect()->back()
                                ->withErrors($validator);
                        }
                        else {
                            return redirect()->route('service.loan.create.special', [
                                'id' => $member->id,
                                'loantype_id' => $loanType->id,
                                'loan_id' => $request->input('loan_id'),
                                'step' => 3
                            ]);  
                        }
                        break;
                    case 3:
                        $validator = $this->validateStep3($member, $loanType, $request);

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
                        $validator = $this->validateStep1($member, $loanType, $request);

                        if ($validator->fails()) {
                            return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                        }
                        else {
                            $incompletes = Loan::where('member_id', $member->id)
                                ->whereNull('code')
                                ->delete();

                            $loan = new Loan();
                            $loan->member_id = $member->id;
                            $loan->loan_type_id = $loanType->id;
                            $loan->payment_type_id = $request->input('payment_type');
                            $loan->code = null;
                            $loan->outstanding = $request->input('outstanding');
                            $loan->rate = $loanType->rate;
                            $loan->period = $request->input('period');
                            $loan->save();

                            // ค้ำประกันตนเอง
                            $loan->sureties()->attach($member->id, ['amount' => $request->input('outstanding'), 'yourself' => true]);

                            return redirect()->route('service.loan.create.special', [
                                'id' => $member->id,
                                'loantype_id' => $loanType->id,
                                'loan_id' => $loan->id,
                                'step' => 2
                            ]);
                        }
                        break;
                }
                break;
        }
    }

    public function getCreateNormalLoan($id, $loantype_id, Request $request) {
        $loan = Loan::find($request->input('loan_id'));

        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $step = (!is_null($loan)) ? ($loan->sureties->count() > 0) ? $request->input('step') : 2 : 1;

        return view('admin.loan.normal.create', [
            'loan_id' => (!is_null($loan)) ? $loan->id : 0,
            'step' => $step,
            'member' => $member,
            'loantype' => $loantype
        ]);
    }

    public function getCreateEmergingLoan($id, $loantype_id, Request $request) {
        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $step = $request->input('step');   

        return view('admin.loan.emerging.create', [
            'loan_id' => $request->input('loan_id'),            
            'step' => $step,
            'member' => $member,
            'loantype' => $loantype
        ]);   
    }
    
    public function getCreateSpecialLoan($id, $loantype_id, Request $request) {
        $member = Member::find($id);
        $loantype = LoanType::find($loantype_id);
        $step = $request->input('step'); 

        return view('admin.loan.special.create', [
            'loan_id' => $request->input('loan_id'),            
            'step' => $step,
            'member' => $member,
            'loantype' => $loantype
        ]);   
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
            $percent = ($shareholding / $outstanding) * 100; // ใช้หุ้น < เงินกู้

            $max_cash = $loanType->limits->max('cash_end');
            if ($outstanding > $max_cash) {
                $validator->errors()->add('max_cash', "ไม่สามารถกู้ได้ เนื่องจากยอดเงินที่ขอกู้มากกว่าวงเงินที่สามารถกู้ได้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_cash, 2, '.', ',') . " บาท)");
            }

            $max_outstanding = $salary * 40;
            if ($outstanding > $max_outstanding) {
                $validator->errors()->add('max_outstanding', "ไม่สามารถกู้ได้ เนื่องจากเงินเดือนน้อยกว่าวงเงินที่ขอกู้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_outstanding, 2, '.', ',') . " บาท)");
            }

            if ($member->loans->sum('outstanding') + $outstanding >= 1200000) {
                $validator->errors()->add('overflow', "ไม่สามารถกู้ได้ เนื่องจากยอดที่กู้รวมกันแล้วเกิน 1,200,000 บาท");
            }

            if ($net_salary - $pmt < 3000) {
                $validator->errors()->add('pmt', "ไม่สามารถกู้ได้ เนื่องจากเงินเดือนสุทธิไม่พอสำหรับขอกู้ (ค่างวดต่อเดือน " . number_format($pmt, 2, '.', ',') . " บาท)");
            }
            if ($limit != null) {
                if ($limit->period < $period) {
                    $validator->errors()->add('period', "ไม่สามารถกู้ได้ เนื่องจากระยะเวลาผ่อนชำระนานกว่าที่กำหนด (จำนวนงวดสูงสุด " . number_format($limit->period, 0, '.', ',') . " งวด)");
                }

                if ($percent < $limit->shareholding) {
                    $validator->errors()->add('shareholding', "ไม่สามารถกู้ได้ เนื่องจากมีทุนเรือนหุ้นไม่พอ (ต้องการหุ้น " . number_format($limit->shareholding, 1, '.', ',') . "%, ผู้กู้มี " . number_format($percent, 1, '.', ',') . "%)");
                }
            }
            else {
                $validator->errors()->add('max_cash', "ไม่สามารถกู้ได้ เนื่องจากยอดเงินที่ขอกู้มากกว่าวงเงินที่สามารถกู้ได้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_cash, 2, '.', ',') . " บาท)");                
            }
        });

        return $validator;
    }

    protected function validateNormalOutsiderStep1($member, $loanType, $request) {
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

        $validator->after(function($validator) use ($member, $loanType, $request) {
            $outstanding = $request->input('outstanding');
            $period = $request->input('period');
            $limit = $loanType->limits()->where('cash_begin', '<=', $outstanding)
                ->where('cash_end', '>=', $outstanding)->first();
            $shareholding = $member->shareholdings->sum('amount');
            $pmt = LoanCalculator::pmt($loanType->rate, $outstanding, $period);
            $percent = ($outstanding / $shareholding) * 100; // ใช้หุ้น > เงินกู้

            $max_cash = $loanType->limits->max('cash_end');
            if ($outstanding > $max_cash) {
                $validator->errors()->add('max_cash', "ไม่สามารถกู้ได้ เนื่องจากยอดเงินที่ขอกู้มากกว่าวงเงินที่สามารถกู้ได้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_cash, 2, '.', ',') . " บาท)");
            }

            if ($member->loans->sum('outstanding') + $outstanding >= 1200000) {
                $validator->errors()->add('overflow', "ไม่สามารถกู้ได้ เนื่องจากยอดที่กู้รวมกันแล้วเกิน 1,200,000 บาท");
            }

            if ($limit != null) {
                if ($limit->period < $period) {
                    $validator->errors()->add('period', "ไม่สามารถกู้ได้ เนื่องจากระยะเวลาผ่อนชำระนานกว่าที่กำหนด (จำนวนงวดสูงสุด " . number_format($limit->period, 0, '.', ',') . " งวด)");
                }

                if ($percent > 80.0) {
                    $validator->errors()->add('shareholding', "ไม่สามารถกู้ได้ เนื่องจากมีทุนเรือนหุ้นไม่พอ (วงเงินที่กู้ต้องไม่เกิน 80% ของหุ้น, ผู้กู้กู้ไป " . number_format($percent, 1, '.', ',') . "%)");
                }
            }
            else {
                $validator->errors()->add('max_cash', "ไม่สามารถกู้ได้ เนื่องจากยอดเงินที่ขอกู้มากกว่าวงเงินที่สามารถกู้ได้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_cash, 2, '.', ',') . " บาท)");
            }
        });

        return $validator;
    }

    protected function validateStep1($member, $loanType, $request) {
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

        $validator->after(function($validator) use ($member, $loanType, $request) {
            $outstanding = $request->input('outstanding');
            $period = $request->input('period');
            $limit = $loanType->limits()->where('cash_begin', '<=', $outstanding)->where('cash_end', '>=', $outstanding)->first();
            $shareholding = $member->shareholdings->sum('amount');
            $pmt = LoanCalculator::pmt($loanType->rate, $outstanding, $period);
            $percent = ($shareholding * 100) / $outstanding;

            $max_cash = $loanType->limits->max('cash_end');
            if ($outstanding > $max_cash) {
                $validator->errors()->add('max_cash', "ไม่สามารถกู้ได้ เนื่องจากยอดเงินที่ขอกู้มากกว่าวงเงินที่สามารถกู้ได้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_cash, 2, '.', ',') . " บาท)");
            }

            if ($limit->period < $period) {
                $validator->errors()->add('period', "ไม่สามารถกู้ได้ เนื่องจากระยะเวลาผ่อนชำระนานกว่าที่กำหนด (จำนวนงวดสูงสุด " . number_format($limit->period, 0, '.', ',') . " งวด)");
            }

            if ($percent < $limit->shareholding) {
                $validator->errors()->add('shareholding', "ไม่สามารถกู้ได้ เนื่องจากมีทุนเรือนหุ้นไม่พอ (ต้องการหุ้น " . number_format($limit->shareholding, 1, '.', ',') . "%, ผู้กู้มี " . number_format($percent, 1, '.', ',') . "%)");
            }

            // ถ้าไม่ใช่กู้เฉพาะกิจ ให้ตรวจยอดเงินกู้รวม
            if ($loanType < 3) {
                if ($member->loans->sum('outstanding') + $outstanding >= 1200000) {
                    $validator->errors()->add('overflow', "ไม่สามารถกู้ได้ เนื่องจากยอดที่กู้รวมกันแล้วเกิน 1,200,000 บาท");
                }
            }

            // ถ้าไม่ใช่กู้สามัญ ให้ตรวจสอบหุ้น
            if ($loanType > 1) {
                $available = LoanCalculator::shareholding_available($member);

                if ($available < $outstanding) {
                    $validator->errors()->add('surety', "ไม่สามารถกู้ได้ เนื่องจากมีทุนเรือนหุ้นเพื่อค้ำประกันไม่พอ (ต้องการหุ้น " . number_format($outstanding, 2, '.', ',') . " บาท, ผู้กู้มี " . number_format($available, 2, '.', ',') . " บาท)");
                }
            }
        });

        return $validator;
    }

    protected function validateStep2($member, $loanType, $request) {
        $validator = Validator::make($request->all(), []);

        $validator->after(function($validator) use ($member, $loanType, $request) {
            $loan = Loan::find($request->input('loan_id'));
            $outstanding = $loan->outstanding;
            $limit = $loan->loanType->limits()->where('cash_begin', '<=', $outstanding)
                ->where('cash_end', '>=', $outstanding)->first();
            $amount_sum = ($loan->sureties->count() > 0) ? $loan->sureties->sum('pivot.amount') : 0;
            $limit_suerty = collect(explode('-', $limit->surety));

            if ($limit_suerty->count() > 1) {
                if ($limit_suerty->max() < $loan->sureties->count() || $limit_suerty->min() > $loan->sureties->count()) {
                    $validator->errors()->add('sureties', 'ต้องการผู้ค้ำประกัน ' . $limit->surety . ' คน (มีผู้ค้ำ ' . $loan->sureties->count() . ' คน)');
                }
            }
            else if ($limit_suerty[0] > 0) {
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

    protected function validateStep3($member, $loanType, $request) {
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

    public function edit($id, $loan_id) {
        $member = Member::find($id);
        $loan = Loan::find($loan_id);

        return view('admin.loan.edit', [
            'member' => $member,
            'loan' => $loan,
        ]);  
    }

    public function update($id, $loan_id, Request $request) {
        $rules = [
            'code' => 'required'
        ];
        
        $attributeNames = [
            'code' => 'เลขที่สัญญา'
        ];    

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        else {
            DB::transaction(function() use ($request, $loan_id) {
                $code = $request->input('code');

                $loan = Loan::find($loan_id);
                $loan->code = $code;
                $loan->save();

                History::addAdminHistory(Auth::guard($this->guard)->id(), 'แก้ไขข้อมูล', 'แก้ไขข้อมูลสัญญาเงินกู้เลขที่ ' . $request->input('code'));
            });

            return redirect()->action('Admin\LoanController@show', ['id' => $id, 'loan_id' => $loan_id])
                ->with('flash_message', 'แก้ไขสัญญาเงินกู้เลขที่ ' . $request->input('code') . ' เรียบร้อยแล้ว')
                ->with('callout_class', 'callout-success');
        }
    }
}
