<?php

namespace App\Classes;

use stdClass;
use App\Bailsman;
use App\Loan;
use App\Payment;
use App\Member;
use App\RoutinePaymentDetail;

class LoanCalculator {
    const DAYS_IN_YEAR = 365;

    public function pmt($rate, $outstanding, $period) {
        return round($outstanding / ((1 - (1 / pow(1 + ($rate / 100 / 12), $period))) / ($rate / 100 / 12)), 0);
    }

    public function flat($rate, $outstanding, $period) {
        return round(($outstanding + ($outstanding * ($rate / 100) * ($period / 12))) / $period, 0);
    }

    public function payment($rate, $pmt, $payment_type, $outstanding, $period, Diamond $start) {
        return ($payment_type == 1) ? 
            $this->payment_general($rate, ($pmt == 0) ? $this->pmt($rate, $outstanding, $period) : $pmt, $outstanding, $period, $start) : 
            $this->payment_stable($rate, ($pmt == 0) ? $this->pmt($rate, $outstanding, $period) : $pmt, $outstanding, $period, $start);
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
        $days = $lastpay->diffInDays($date, false);

        return $balance * $dayRate * $days;
    }

    public function monthly_payment(Loan $loan, Diamond $date) {
        $lastPay = ($loan->payments->count() > 0) ? Diamond::parse($loan->payments->max('pay_date')) : Diamond::parse($loan->loaned_at);
        $days = $lastPay->diffInDays($date, false);
        $balance = ($loan->payments->count() > 0) ? $loan->outstanding - $loan->payments->sum('principle') : $loan->outstanding;
        $pmt = ($loan->pmt == 0) ? $this->pmt($loan->rate, $loan->outstanding, $loan->period) : $loan->pmt;
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

    //กู้สามัญด้วยเงินเดือน
    public function shareholding_available($member) {
        // (หุ้น x 0.8 บุคคลภายนอก) - เงินที่กู้ไปแล้ว ณ ปัจจุบัน
        // (หุ้น x 0.9 พนักงาน) - เงินที่กู้ไปแล้ว ณ ปัจจุบัน
        $weitgh = ($member->profile->employee->employee_type_id == 1) ? 0.9 : 0.8;
        $shareholding = $member->shareholdings->sum('amount') * $weitgh < 1200000 ? $member->shareholdings->sum('amount') * $weitgh : 1200000;
        $gaurantee = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum(function ($value) { return $value->sureties->filter(function ($value, $key) { return $value->yourself; })->sum('amount'); });
        $principle = $member->loans->filter(function ($value, $key) { return !is_null($value->code) && is_null($value->completed_at); })->sum(function ($value) { return $value->payments->sum('principle'); });
        
        return $shareholding - ($gaurantee - $principle);
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

    public function normal_payment($loan, $amount, $date) {
        $dayRate = $loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR;

        $balance = $loan->outstanding - $loan->payments->sum('principle');
        $last_payment_date = !is_null($loan->payments->max('pay_date')) ? Diamond::parse($loan->payments->max('pay_date')) : Diamond::parse($loan->loaned_at);
        $days = $last_payment_date->diffInDays(Diamond::parse($date), false);
        $interest = $balance * $dayRate * $days;

        $result = new stdClass();
        $result->principle = $amount - $interest;
        $result->interest = $interest;

        return $result;
    }

    public function normal_payment_2($loan, $lastpay, $paydate, $amount) {
        $day = 10;
        $pay_date = Diamond::parse($paydate);
        $dayRate = $loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR;
        $last_payment_date = Diamond::parse($lastpay);
        $days = $last_payment_date->diffInDays($pay_date, false);

        $startOfMonth = $pay_date->copy()->startOfMonth();
        $endOfMonth = $pay_date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays($day - 1); // วันที่ 10
        $result = new stdClass();

        if ($loan->member->profile->employee->employee_type_id == 1) { 
            // พนักงาน/ลูกจ้าง
            if ($loan->member->shareholding > 0) {
                // นำส่งตัดเงินเดือน คำนวณนำส่งใหม่
                if (RoutinePaymentDetail::where('loan_id', $loan->id)->whereDate('pay_date', '=', $endOfMonth->toDateString())->count() > 0) {
                    $routine = RoutinePaymentDetail::where('loan_id', $loan->id)->whereDate('pay_date', '=', $endOfMonth->toDateString())->first();

                    if ($routine != null) {
                        $pmt = ($loan->pmt == 0) ? $this->pmt($loan->rate, $loan->outstanding, $loan->period) : $loan->pmt;
                        $balance = $loan->outstanding - $loan->payments->sum('principle');
                        $interest = $balance * $dayRate * $days;
                        $routine_pay = (($pmt < $balance) ? $pmt : $balance) / (1 + ($dayRate * $pay_date->diffInDays($endOfMonth, false)));

                        $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$pay_date->thai_format('d M Y')}";
                        $result->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                        $result->principle = $amount - $interest;
                        $result->interest = $interest;
                
                        if ($endOfMonth->gt($pay_date->copy()->addDay(1))) {
                            $result->routine_cal = "{$pay_date->copy()->addDay(1)->thai_format('j M Y')} - {$endOfMonth->thai_format('d M Y')}";
                            $result->routine_period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 2 : 2;
                            $result->routine_principle = $routine_pay;
                            $result->routine_interest = $pmt - $routine_pay;
                        }
                        else {
                            $result->routine_cal = '-';
                            $result->routine_period = 0;
                            $result->routine_principle = 0;
                            $result->routine_interest = 0;
                        }
                    }
                    else {
                        $balance = $loan->outstanding - $loan->payments->sum('principle');
                        $interest = $balance * $dayRate * $days;
        
                        $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$pay_date->thai_format('d M Y')}";
                        $result->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                        $result->principle = $amount - $interest;
                        $result->interest = $interest;
                
                        $result->routine_cal = '-';
                        $result->routine_period = 0;
                        $result->routine_principle = 0;
                        $result->routine_interest = 0;
                    }
                }
                else {
                    $balance = $loan->outstanding - $loan->payments->sum('principle');
                    $interest = $balance * $dayRate * $days;
    
                    $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$pay_date->thai_format('d M Y')}";
                    $result->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                    $result->principle = $amount - $interest;
                    $result->interest = $interest;
            
                    $result->routine_cal = '-';
                    $result->routine_period = 0;
                    $result->routine_principle = 0;
                    $result->routine_interest = 0;
                }
            }
            else {
                // ไม่ได้นำส่งตัดเงินเดือน
                $balance = $loan->outstanding - $loan->payments->sum('principle');
                $interest = $balance * $dayRate * $days;

                $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$pay_date->thai_format('d M Y')}";
                $result->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                $result->principle = $amount - $interest;
                $result->interest = $interest;
        
                $result->routine_cal = '-';
                $result->routine_period = 0;
                $result->routine_principle = 0;
                $result->routine_interest = 0;
            }
        }
        else {
            // บุคคลภายนอก ไม่ได้นำส่งตัดเงินเดือน
            $balance = $loan->outstanding - $loan->payments->sum('principle');
            $interest = $balance * $dayRate * $days;
            
            $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$pay_date->thai_format('d M Y')}";
            $result->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
            $result->principle = $amount - $interest;
            $result->interest = $interest;
    
            $result->routine_cal = '-';
            $result->routine_period = 0;
            $result->routine_principle = 0;
            $result->routine_interest = 0;
        }

        return $result;
    }

    public function close_payment($loan, $lastpay, $paydate) {
        $day = 10;
        $close_date = Diamond::parse($paydate);
        $dayRate = $loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR;
        $last_payment_date = Diamond::parse($lastpay);
        $days = $last_payment_date->diffInDays($close_date, false);

        $startOfMonth = $close_date->copy()->startOfMonth();
        $endOfMonth = $close_date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays($day - 1); // วันที่ 10
        $result = new stdClass();

        if ($loan->member->profile->employee->employee_type_id == 1) { 
            // พนักงาน/ลูกจ้าง
            if ($loan->member->shareholding > 0) {
                // นำส่งตัดเงินเดือน
                if ($close_date->lessThan($d_day)) {
                    // ปิดก่อนวันที่ 10
                    $balance = $loan->outstanding - $loan->payments->sum('principle');

                    $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('j M Y')}";
                    $result->principle = $balance;
                    $result->interest = $balance * $dayRate * $days;

                    $result->routine_cal = "-";
                    $result->routine_principle = 0;
                    $result->routine_interest = 0;
                }
                else {
                    // ปิดหลังวันที่ 10 คำนวณนำส่งใหม่
                    if (RoutinePaymentDetail::where('loan_id', $loan->id)->whereDate('pay_date', '=', $endOfMonth->toDateString())->count() > 0) {
                        $routine = RoutinePaymentDetail::where('loan_id', $loan->id)->whereDate('pay_date', '=', $endOfMonth->toDateString())->first();

                        if ($routine != null) {
                            $pmt = ($loan->pmt == 0) ? $this->pmt($loan->rate, $loan->outstanding, $loan->period) : $loan->pmt;
                            $balance = $loan->outstanding - $loan->payments->sum('principle');
                            $routine_pay = (($pmt < $balance) ? $pmt : $balance) / (1 + ($dayRate * $close_date->diffInDays($endOfMonth, false)));
                            $payment = $balance - $routine_pay;

                            $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
                            $result->principle = $payment;
                            $result->interest = $balance * $dayRate * $days;
             
                            $result->routine_cal = "{$close_date->copy()->addDay(1)->thai_format('j M Y')} - {$endOfMonth->thai_format('d M Y')}";
                            $result->routine_principle = $routine_pay;
                            $result->routine_interest = $pmt - $routine_pay;
                        }
                        else {
                            $balance = $loan->outstanding - $loan->payments->sum('principle');
     
                            $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
                            $result->principle = $balance;
                            $result->interest = $balance * $dayRate * $days;

                            $result->routine_cal = "-";
                            $result->routine_principle = 0;
                            $result->routine_interest = 0;
                        }
                    }
                    else {
                        $balance = $loan->outstanding - $loan->payments->sum('principle');
     
                        $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
                        $result->principle = $balance;
                        $result->interest = $balance * $dayRate * $days; 
                        
                        $result->routine_cal = "-";
                        $result->routine_principle = 0;
                        $result->routine_interest = 0;
                    }
                }
            }
            else {
                // ไม่ได้นำส่งตัดเงินเดือน
                $balance = $loan->outstanding - $loan->payments->sum('principle');
     
                $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
                $result->principle = $balance;
                $result->interest = $balance * $dayRate * $days;

                $result->routine_cal = "-";
                $result->routine_principle = 0;
                $result->routine_interest = 0;
            }
        }
        else {
            // บุคคลภายนอก ไม่ได้นำส่งตัดเงินเดือน
            $balance = $loan->outstanding - $loan->payments->sum('principle');
     
            $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
            $result->principle = $balance;
            $result->interest = $balance * $dayRate * $days;

            $result->routine_cal = "-";
            $result->routine_principle = 0;
            $result->routine_interest = 0;
        }

        return $result;
    }

    public function refinance_payment($loan, $lastpay, $paydate) {
        $day = 10;
        $close_date = Diamond::parse($paydate);
        $dayRate = $loan->rate / 100 / LoanCalculator::DAYS_IN_YEAR;
        $last_payment_date = Diamond::parse($lastpay);
        $days = $last_payment_date->diffInDays($close_date, false);

        $startOfMonth = $close_date->copy()->startOfMonth();
        $endOfMonth = $close_date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays($day - 1); // วันที่ 10
        $result = new stdClass();

        if ($loan->member->profile->employee->employee_type_id == 1) { 
            // พนักงาน/ลูกจ้าง
            if ($loan->member->shareholding > 0) {
                // นำส่งตัดเงินเดือน
                if ($close_date->lessThan($d_day)) {
                    // ปิดก่อนวันที่ 10
                    $balance = $loan->outstanding - $loan->payments->sum('principle');

                    $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('j M Y')}";
                    $result->principle = $balance;
                    $result->interest = $balance * $dayRate * $days;

                    $result->refund = 0;
                }
                else {
                    // ปิดหลังวันที่ 10 คำนวณนำส่งใหม่
                    $routine = RoutinePaymentDetail::where('loan_id', $loan->id)->whereDate('pay_date', '=', $endOfMonth->toDateString())->first();

                    if ($routine != null) {
                        $pmt = ($loan->pmt == 0) ? $this->pmt($loan->rate, $loan->outstanding, $loan->period) : $loan->pmt;
                        $balance = $loan->outstanding - $loan->payments->sum('principle');

                        $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
                        $result->principle = $balance;
                        $result->interest = $balance * $dayRate * $days;
         
                        $result->refund = (($pmt < $balance) ? $pmt : $balance) / (1 + ($dayRate * $close_date->diffInDays($endOfMonth, false)));
                    }
                    else {
                        $balance = $loan->outstanding - $loan->payments->sum('principle');
 
                        $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
                        $result->principle = $balance;
                        $result->interest = $balance * $dayRate * $days;

                        $result->refund = 0;
                    }
                }
            }
            else {
                // ไม่ได้นำส่งตัดเงินเดือน
                $balance = $loan->outstanding - $loan->payments->sum('principle');
     
                $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
                $result->principle = $balance;
                $result->interest = $balance * $dayRate * $days;

                $result->refund = 0;
            }
        }
        else {
            // บุคคลภายนอก ไม่ได้นำส่งตัดเงินเดือน
            $balance = $loan->outstanding - $loan->payments->sum('principle');
     
            $result->cal = "{$last_payment_date->thai_format('j M Y')} - {$close_date->thai_format('d M Y')}";
            $result->principle = $balance;
            $result->interest = $balance * $dayRate * $days;

            $result->refund = 0;
        }

        return $result;
    }

    protected function payment_general($rate, $pmt, $outstanding, $period, Diamond $start) {
        $data = [];
        $forward = $outstanding;

        for ($i = 0; $i < $period; $i++) {
            $date = $start->addMonths(1);
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

    protected function payment_stable($rate, $pmt, $outstanding, $period, Diamond $start) {
        $data = [];
        $forward = $outstanding;

        for ($i = 0; $i < $period; $i++) {
            $date = $start->addMonths(1);
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