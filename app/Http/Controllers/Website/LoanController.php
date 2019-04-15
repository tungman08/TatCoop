<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Loan;
use App\LoanType;
use App\Payment;
use App\Member;
use App\Billing;
use Auth;
use DB;
use Diamond;
use History;
use PDF;

class LoanController extends Controller
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
        $this->middleware('auth:users', ['except' => [ 'getCalculate' ]]);
    }


    public function index() {
        $id = Auth::user()->member_id;
        $member = Member::find($id);

        return view('website.loan.index', [
            'member' => $member,
            'loans' => Loan::where('member_id', $member->id)->whereNotNull('code')->orderBy('completed_at', 'asc')->orderBy('loaned_at', 'desc')->get(),
            'loantypes' => LoanType::active()->get()
        ]);
    }

    public function show($id) {
        $member = Member::find(Auth::user()->member_id);
        $loan = Loan::find($id);
        $payments = Payment::where('loan_id', $id)
            ->orderBy('period', 'desc')
            ->get();

        return view('website.loan.show', [
            'member' => $member,
            'loan' => $loan,
            'payments' => $payments
        ]);
    }

    public function getBilling($loan_id, $payment_id, $date) {
        $id = Auth::user()->member_id;
        $billdate = Diamond::parse($date);
        $member = Member::find($id);
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        return view('website.loan.billing', [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
    }

    public function getPrint($loan_id, $payment_id, $date) {
        $id = Auth::user()->member_id;
        $billdate = Diamond::parse($date);
        $member = Member::find($id);
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return view('website.loan.print', [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ]);
    }

    public function getPdf($loan_id, $payment_id, $date) {
        $id = Auth::user()->member_id;
        $billdate = Diamond::parse($date);
        $member = Member::find($id);
        $loan = Loan::find($loan_id);
        $payment = Payment::find($payment_id);

        $data = [
            'member' => $member,
            'loan' => $loan,
            'billing' => Billing::latest()->first(),
            'payment' => $payment,
            'billno' => $billdate->thai_format('Y') . str_pad($payment->loan->id, 8, '0', STR_PAD_LEFT),
            'date' => $billdate
        ];

        History::addUserHistory(Auth::guard()->id(), 'นำข้อมูลออก', 'นำข้อมูลใบเสร็จออกจากระบบ');

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('website.loan.pdf', $data)->download('ใบเสร็จรับเงินค่างวด สัญญาเลขที่ ' . $loan->code . ' เดือน-' . $billdate->thai_format('M-Y') . '.pdf');
    }
    
    public function getCalculate() {
        $loan_types = LoanType::active()->get();

        return view('website.loan.calculate', [
            'loan_types' => $loan_types
        ]);
    }
}
