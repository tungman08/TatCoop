<?php

namespace App\Classes;

use stdClass;
use App\Loan;
use App\Member;

class LoanCalculator {
    const DAYS_IN_YEAR = 365;

    public function payment($rate, $payment_type, $outstanding, $period, Diamond $start) {
        return ($payment_type == 1) ? 
            $this->payment_general($rate, $outstanding, $period, $start) : 
            $this->payment_stable($rate, $outstanding, $period, $start);
    }

    public function total_interest(Member $member, Diamond $date) {
        $interest = 0;

        foreach ($member->loans->filter(function ($value, $key) { return is_null($value->completed_at); }) as $loan) {
            $interest += $this->loan_interest($loan, $date);
        }

        return $interest;
    }

    public function loan_interest(Loan $loan, Diamond $date) {
        $dayRate = $loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR;
        $balance = $loan->outstanding - $loan->payments->sum('principle');
        $lastpay = !is_null($loan->payments->max('pay_date')) ? Diamond::parse($loan->payments->max('pay_date')) : Diamond::parse($loan->loaned_at);
        $days = $lastpay->diffInDays($date);

        return $balance * $dayRate * $days;
    }

    public function pmt($rate, $outstanding, $period) {
        return round($outstanding / ((1 - (1 / pow(1 + ($rate / 100 / 12), $period))) / ($rate / 100 / 12)), 0);
    }

    public function monthly_payment(Loan $loan, Diamond $date) {
        $lastPay = ($loan->payments->count() > 0) ? Diamond::parse($loan->payments->max('pay_date')) : Diamond::parse($loan->loaned_at);
        $days = $lastPay->diffInDays($date);
        $balance = ($loan->payments->count() > 0) ? $loan->outstanding - $loan->payments->sum('principle') : $loan->outstanding;
        $pmt = $this->pmt($loan->rate, $loan->outstanding, $loan->period);
        $rate = ($loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR) * $days;
        
        if ($loan->paymentType->id == 1) {
            $interest = $balance * $rate;
            $pay = ($pmt < $balance) ? $pmt : $balance + $interest;
            $principle = $pay - $interest;

            $item = new stdClass();
            $item->principle = $principle;
            $item->interest = $interest;

            return $item;
        }
        else {
            $interest = $balance * $rate;
            $principle = ($pmt < $balance) ? $pmt : $balance;
            $pay = $principle + $interest;
            $addon = ($pmt < $balance) ? $this->addon($pay) : 0;

            $item = new stdClass();
            $item->principle = $principle + $addon;
            $item->interest = $interest;

            return $item;
        }
    }

    public function shareholding_available($member) {
        $shareholding = $member->shareholdings->sum('amount') * 0.8;
        $sureties = 0;

        foreach ($member->sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); }) as $loan) {
            $surety = $loan->pivot->amount;
            $outstanding = $loan->outstanding;
            $pay = $loan->payments->sum('principle');
            $sureties += $surety * (($outstanding - $pay) / $outstanding);
        }

        return $shareholding - $sureties;
    }

    public function sureties_balance($sureties) {
        $balance = 0;

        foreach ($sureties->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); }) as $loan) {
            $surety = $loan->pivot->amount;
            $outstanding = $loan->outstanding;
            $pay = $loan->payments->sum('principle');
            $balance += $surety * (($outstanding - $pay) / $outstanding);
        }

        return $balance;
    }

    public function surety_balance($surety) {
        $outstanding = $surety->outstanding;
        $pay = $surety->payments->sum('principle');

        return $surety->pivot->amount * (($outstanding - $pay) / $outstanding);
    }

    public function close_payment($loan, Diamond $date) {
        $dayRate = $loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR;
        $balance = $loan->outstanding - $loan->payments->sum('principle');
        $last_payment_date = !is_null($loan->payments->max('pay_date')) ? Diamond::parse($loan->payments->max('pay_date')) : Diamond::parse($loan->loaned_at);
        $days = $last_payment_date->diffInDays($date);

        $result = new stdClass();
        $result->principle = $balance;
        $result->interest = $balance * $dayRate * $days;

        return $result;
    }

    public function close_payment_with_refund($loan, Diamond $date) {
        $dayRate = $loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR;
        $end_month = Diamond::now()->endOfMonth();

        if ($end_month->gte($date)) {
            // จ่ายก่อนสิ้นเดือนหรือจ่ายสิ้นเดือนพอดี คำนวณดอกเบี้ยใหม่ คืนเงินดอกเบี้ยเดิม
           
            if ($loan->payments->count() == 0) { // ไม่เคยจ่ายมาก่อน
                $balance = $loan->outstanding;
                $last_payment_date = Diamond::parse($loan->loaned_at);
                $days = $last_payment_date->diffInDays($date);

                $result = new stdClass();
                $result->principle = $balance;
                $result->interest = $balance * $dayRate * $days;
                $result->refund = 0;

                return $result;
            }
            else if ($loan->payments->count() == 1) { // จ่ายแล้ว 1 งวด
                $last_payment = Payment::where('loan_id', $loan->id)->orderBy('pay_date', 'desc')->first();
                $balance = $loan->outstanding;
                $last_payment_date = Diamond::parse($loan->loaned_at);
                $days = $last_payment_date->diffInDays($date);

                $result = new stdClass();
                $result->principle = $balance;
                $result->interest = $balance * $dayRate * $days;
                $result->refund = $last_payment->principle + $last_payment->interest;
            }
            else { // จ่ายแล้วมากกว่า 2 งวด
                $last_payment = Payment::where('loan_id', $loan->id)->orderBy('pay_date', 'desc')->first();
                $balance = ($loan->outstanding - $loan->payments->sum('principle')) + $last_payment->principle;
                $last_payment_date = Diamond::parse(Payment::where('loan_id', $loan->id)->where('id', '<>', $last_payment->id)->orderBy('pay_date', 'desc')->first()->pay_date);
                $days = $last_payment_date->diffInDays($date);

                $result = new stdClass();
                $result->principle = $balance;
                $result->interest = $balance * $dayRate * $days;
                $result->refund = $last_payment->principle + $last_payment->interest;
            }            
        }
        else {
            // จ่ายหลังสิ้นเดือน ไม่มีต้องคืนเงินดอกเบี้ยเดิม ดำนวณดอกเบี้ยวันที่ 1 ถึงวันที่มาจ่าย
            $balance = $loan->outstanding - $loan->payments->sum('principle');
            $last_payment_date = !is_null($loan->payments->max('pay_date')) ? Diamond::parse($loan->payments->max('pay_date')) : Diamond::parse($loan->loaned_at);
            $days = $last_payment_date->diffInDays($date);
    
            $result = new stdClass();
            $result->principle = $balance;
            $result->interest = $balance * $dayRate * $days;
            $result->refund = 0;

            return $result;
        }
    }

    protected function payment_general($rate, $outstanding, $period, Diamond $start) {
        $data = [];
        $forward = $outstanding;

        $pmt = $this->pmt($rate, $outstanding, $period);

        for ($i = 0; $i < $period; $i++) {
            $date = $start->addMonths($i);
            $daysInMonth = $date->daysInMonth;
            $monthRate = ($rate / 100 / LoanCalculator::DAYS_IN_YEAR) * $daysInMonth;

            $interest = $forward * $monthRate;
            $pay = ($pmt < $forward) ? $pmt : $forward + $interest;
            $principle = $pay - $interest;
            $balance = $forward - $principle;

            if ($pay > 0) {
                $payment = ['month' => 'งวดที่ ' . strval($i + 1) . ' (' . $date->thai_format('M Y') . ')',
                    'pay' => $pay,
                    'interest' => $interest,
                    'principle' => $principle,
                    'balance' => $balance,
                ];

                $data[] = $payment;
            }

            $forward = $balance;
        }

        return $data;
    }

    protected function payment_stable($rate, $outstanding, $period, Diamond $start) {
        $data = [];
        $forward = $outstanding;

        $pmt = $this->pmt($rate, $outstanding, $period);

        for ($i = 0; $i < $period; $i++) {
            $date = $start->addMonths($i);
            $daysInMonth = $date->daysInMonth;
            $monthRate = ($rate / 100 / LoanCalculator::DAYS_IN_YEAR) * $daysInMonth;
            
            $interest = $forward * $monthRate;
            $principle = ($pmt < $forward) ? $pmt : $forward;
            $pay = $principle + $interest;
            $addon = ($i < $period - 1) ? ($pmt < $forward) ? $this->addon($pay) : 0 : 0;
            $balance = $forward - ($principle + $addon);

            if ($pay > 0) {
                $payment = ['month' => 'งวดที่ ' . strval($i + 1) . ' (' . $date->thai_format('M Y') . ')',
                    'pay' => $pay,
                    'addon' => $addon,
                    'interest' => $interest,
                    'principle' => $principle,
                    'balance' => $balance,
                ];

                $data[] = $payment;
            }
            
            $forward = $balance;
        }

        return $data;
    }

    private function addon($pay) {
        return ($pay - floor($pay) > 0) ? 1.00 - ($pay - floor($pay)) : 0;
    }
}