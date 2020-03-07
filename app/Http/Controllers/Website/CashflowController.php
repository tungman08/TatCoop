<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\Member;
use App\LoanType;
use App\Loan;
use App\Shareholding;
use App\Payment;
use Diamond;
use PDF;
use stdClass;

class CashflowController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $year = $id;
        $member = Member::find(Auth::user()->member_id);
        $loantypes = LoanType::whereYear('expire_date', '>=', $year)->get();
        $shareholding = Shareholding::where('member_id', $member->id)->whereYear('pay_date', '<=', $year)->sum('amount');

        $debts = [];
        foreach ($loantypes as $loantype) {
            $loans = Loan::where('member_id', $member->id)
                ->where('loan_type_id', $loantype->id)
                ->whereNull('completed_at')
                ->whereYear('loaned_at', '<=', $year)
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

        return view('website.cashflow.show', [
            'member' => $member,
            'year' => $year,
            'debts' => $debts,
            'shareholding' => $shareholding
        ]);
    }

    public function getPrintCashflow($year) {
        $member = Member::find(Auth::user()->member_id);
        $loantypes = LoanType::whereYear('expire_date', '>=', $year)->get();
        $shareholding = Shareholding::where('member_id', $member->id)->whereYear('pay_date', '<=', $year)->sum('amount');

        $debts = [];
        foreach ($loantypes as $loantype) {
            $loans = Loan::where('member_id', $member->id)
                ->where('loan_type_id', $loantype->id)
                ->whereNull('completed_at')
                ->whereYear('loaned_at', '<=', $year)
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

        return view('website.cashflow.print', [
            'member' => $member,
            'year' => $year,
            'debts' => $debts,
            'shareholding' => $shareholding
        ]);
    }

    public function getPrintPdfCashflow($year) {
        $member = Member::find(Auth::user()->member_id);
        $loantypes = LoanType::whereYear('expire_date', '>=', $year)->get();
        $shareholding = Shareholding::where('member_id', $member->id)->whereYear('pay_date', '<=', $year)->sum('amount');

        $debts = [];
        foreach ($loantypes as $loantype) {
            $loans = Loan::where('member_id', $member->id)
                ->where('loan_type_id', $loantype->id)
                ->whereNull('completed_at')
                ->whereYear('loaned_at', '<=', $year)
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
            ->loadView('website.cashflow.pdf', $data)->download('หนังสือขอยืนยันยอดลูกหนี้ เงินรับฝากและทุนเรือนหุ้น ปี ' . ($year + 543) . '.pdf');
    }
}
