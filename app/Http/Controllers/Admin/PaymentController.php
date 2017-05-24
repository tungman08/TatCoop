<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Member;
use App\Loan;
use LoanCalculator;

class PaymentController extends Controller
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

    public function create($member_id, $loan_id) {
        return view('admin.payment.create', [
            'member' => Member::find($member_id),
            'loan' => Loan::find($loan_id)
        ]);
    }

    public function store($member_id, $loan_id, Request $request) {
        
    }

    public function getAutoPayment() {
        return view('admin.payment.auto');
    }

    public function postAutoPayment(Request $request) {
        $date = Diamond::parse($request->input('month') . '-1')->endOfMonth();

        $members = Member::join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->join('loans', 'members.id', '=', 'loans.member_id')
            ->where('employees.employee_type_id', '<', 3)
            ->whereDate('members.start_date', '<', $date)
            ->whereNull('loans.completed_at')
            ->get();

        DB::transaction(function() use ($members, $date) {
            foreach($members as $member) {
                foreach($member->loans->filter(function ($value, $key) { return is_null($value->completed_at); }) as $loan) {
                    $pay = LoanCalculator::monthly_payment($loan, $date);

                    $payment = new Payment();
                    $payment->pay_date = $date;
                    $payment->principle = $pay->principle;
                    $payment->interest = $pay->interest;
                    $loan->payments()->save($payment);
                }
            }

            History::addAdminHistory(Auth::guard($this->guard)->id(), 'ป้อนการชำระเงินกู้แบบอัตโนมัติ', 'ทำรายการชำระเงินกู้อัตโนมัติประจำเดือน' . $date->thai_format('F Y'));
        });

        return redirect()->action('Admin\LoanController@getMember')
            ->with('flash_message', 'ทำรายการชำระเงินกู้อัตโนมัติประจำเดือน' . $date->thai_format('F Y') . ' จำนวน ' . $members->count() . ' คน เรียบร้อยแล้ว')
            ->with('callout_class', 'callout-success');
    }
}
