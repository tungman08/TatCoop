<?php

namespace App\Classes;

use App\Dividend;
use App\Member;
use App\Shareholding;
use App\Loan;
use DB;
use stdClass;

/**
 * MemberProperty short summary.
 *
 * MemberProperty description.
 *
 * @version 1.0
 * @author tungm
 */
class MemberProperty
{
    const DAYS_IN_YEAR = 365;

    public function getDividend(Member $member, $year) {
        $dividends = collect([]);
        $rate = Dividend::where('rate_year', $year)->first();

        if ($rate != null) {
            $forward_shareholding = $member->shareHoldings->filter(function ($value, $key) use ($year) { return Diamond::parse($value->pay_date)->year < $year; })->sum('amount');
            $item = new stdClass();
            $item->name = 'ยอดยกมา';
            $item->shareholding = $forward_shareholding;
            $item->shareholding_dividend = $forward_shareholding * ($rate->shareholding_rate / 100);
            $item->interest = 0;
            $item->interest_dividend = 0;
            $item->total = $item->shareholding_dividend + $item->interest_dividend;
            $dividends->push($item);

            $shareholdings = $member->shareHoldings->filter(function ($value, $key) use ($year) { return Diamond::parse($value->pay_date)->year == $year; });
            for ($month = 1; $month <= 12; $month++) {
                $shareholding = $shareholdings->filter(function ($value, $key) use ($month) { return Diamond::parse($value->pay_date)->month == $month; })->sum('amount');
                $interest = 0;
                foreach ($member->loans as $loan) {
                    $interest += $loan->payments->filter(function ($value, $key) use ($year, $month) { 
                        return Diamond::parse($value->pay_date)->year == $year && Diamond::parse($value->pay_date)->month == $month; 
                    })->sum('interest');
                }

                $item = new stdClass();
                $item->name = Diamond::parse("$year-$month-1")->thai_format('F Y');
                $item->shareholding = $shareholding;
                $item->shareholding_dividend = $shareholding * ($rate->shareholding_rate / 100) * ((12 - $month) / 12);
                $item->interest = $interest;
                $item->interest_dividend = $interest * ($rate->loan_rate / 100);
                $item->total = $item->shareholding_dividend + $item->interest_dividend;
                $dividends->push($item);  
            }
        }

        return $dividends;
    }

    public function getDividendQuick(Member $member, $year) {
        $rate = Dividend::where('rate_year', $year)->first();
        $shareholding_dividend = 0;
        $interest_dividend = 0;

        if ($rate != null) {
            $forward_shareholding = $member->shareHoldings->filter(function ($value, $key) use ($year) { return Diamond::parse($value->pay_date)->year < $year; })->sum('amount');
            $shareholding_dividend = $forward_shareholding * ($rate->shareholding_rate / 100);

            $shareholdings = $member->shareHoldings->filter(function ($value, $key) use ($year) { return Diamond::parse($value->pay_date)->year == $year; });        
            for ($month = 1; $month <= 12; $month++) {
                $shareholding = $shareholdings->filter(function ($value, $key) use ($month) { return Diamond::parse($value->pay_date)->month == $month; })->sum('amount');
                $shareholding_dividend += $shareholding * ($rate->shareholding_rate / 100) * ((12 - $month) / 12);
            }

            $interest = 0;
            foreach ($member->loans as $loan) {
                $interest += $loan->payments->filter(function ($value, $key) use ($year) { 
                    return Diamond::parse($value->pay_date)->year == $year; 
                })->sum('interest');
            }
            $interest_dividend += $interest * ($rate->loan_rate / 100);
        }

        $dividends = new stdClass();
        $dividends->shareholding_dividend = $shareholding_dividend;
        $dividends->interest_dividend = $interest_dividend;
        $dividends->total = $shareholding_dividend + $interest_dividend;

        return $dividends;
    }

    public function getTotalPayment(Member $member) {
        $payment = new stdClass();
        $payment->outstanding = $member->loans->filter(function ($value, $key) { return is_null($value->completed_at); })->sum('outstanding');
        $payment->principle = $member->loans->filter(function ($value, $key) { return is_null($value->completed_at); })->sum('payments.principle');
        $payment->interest = (new LoanCalculator)->total_interest($member, Diamond::today());

        return $payment;
    }

    public function getMonthlyPayment(Member $member, Diamond $date) {
        $principle = 0;
        $interest = 0;

        foreach($member->loans->filter(function ($value, $key) { return is_null($value->completed_at); }) as $loan) {
            $monthly = (new LoanCalculator)->monthly_payment($loan, $date);

            $principle += $monthly->principle;
            $interest += $monthly->interest;
        }

        $payment = new stdClass();
        $payment->principle = $principle;
        $payment->interest = $interest;

        return $payment;
    }

    // public function findPrev($id) {
    //     $min_id = Member::active()->min('id');

    //     if ($id > $min_id) {
    //         $member = $this->getMember($id - 1);

    //         while ($member->leave_date != null) {
    //             $member = $this->getMember($member->id - 1);
    //         }

    //         return ($member->id >= $min_id) ? $member->id : $min_id;
    //     }
    //     else {
    //         return $id;
    //     }
    // }

    // public function findNext($id) {
    //     $max_id = Member::active()->max('id');

    //     if ($id < $max_id) {
    //         $member = $this->getMember($id + 1);

    //         while ($member->leave_date != null) {
    //             $member = $this->getMember($member->id + 1);
    //         }

    //         return ($member->id <= $max_id) ? $member->id : $max_id;
    //     }
    //     else {
    //         return $id;
    //     }
    // }

    // protected function getMember($id) {
    //     return Member::find($id);
    // }

    // public function getDividend($id, $year) {
    //     $dividend_month = (Diamond::today()->year > $year) ? 12 : Diamond::today()->month;
    //     $dividend_rate = Dividend::where('rate_year', $year)->first();
    //     $forward_amount = Shareholding::where('member_id', $id)->whereYear('pay_date', '<', $year)->sum('amount');
    //     $forward = collect([(object)[
    //         'name' => 'ยอดยกมา', 
    //         'shareholding' => $forward_amount / 10, 
    //         'amount' => $forward_amount, 
    //         'dividend' => (is_null($dividend_rate) ? 0 : $forward_amount * ($dividend_rate->rate / 100) * ($dividend_month / $dividend_month)),
    //         'remark' => (is_null($dividend_rate) ? 'ยังไม้ได้กำหนดอัตราเงินปันผล' : '')
    //     ]]);
    //     $dividends = $forward->merge(Shareholding::where('member_id', $id)
    //         ->whereYear('pay_date', '=', $year)
    //         ->select(DB::raw('str_to_date(concat(\'1/\', month(pay_date), \'/\', year(pay_date)), \'%d/%m/%Y\') as name'),
    //             DB::raw('sum(amount) / 10 as shareholding'),
    //             DB::raw('sum(amount) as amount'),
    //             DB::raw(is_null($dividend_rate) ? 
    //                 '0 as dividend' : 
    //                 '(sum(amount) * ' . ($dividend_rate->rate / 100) . ' * (' . $dividend_month . ' - month(pay_date)) / ' . $dividend_month . ') as dividend'),
    //             DB::raw(is_null($dividend_rate) ? '\'ยังไม้ได้กำหนดอัตราเงินปันผล\' as remark' : '\'\' as remark'))
    //         ->groupBy(DB::raw('year(pay_date)'), DB::raw('month(pay_date)'))
    //         ->get());

    //     return $dividends;
    // }
}