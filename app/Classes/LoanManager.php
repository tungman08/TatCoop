<?php

namespace App\Classes;

use stdClass;
use App\Loan;
use App\Payment;
use App\Member;
use LoanCalculator;

class LoanManager {
    // สร้างการกู้เบื้องต้น
    public function create_loan($member_id, $loantype_id) {
        if (Loan::where('member_id', $member_id)->where('loan_type_id', $loantype_id)->whereNull('code')->count() == 0) {
            $member = Member::find($member_id);

            $loan = new Loan();
            $loan->member_id = $member->id;
            $loan->loan_type_id = $loantype_id;
            $loan->payment_type_id = 1;
            $loan->shareholding = ($member->profile->employee->employee_type_id == 2);
            $loan->step = 0;
            $loan->save();

            return $loan;
        }

        return Loan::where('member_id', $member_id)->where('loan_type_id', $loantype_id)->whereNull('code')->first();
    }

    // ลบการกู้ที่ไม่สมบูรณ์
    public function delete_incomplete($member_id) {
        $loans = Loan::where('member_id', $member_id)
            ->whereNull('code');
        $loans->delete();
    }

    // ตรวจสอบยอดสูงสุดที่สามารถกู้ได้
    public function check_maxcash($validator, Loan $loan, $outstanding) {
        $maxcash = $loan->loanType->limits->max('cash_end');
        if ($outstanding > $maxcash) {
            $validator->errors()->add('maxcash', "ไม่สามารถกู้ได้ เนื่องจากยอดเงินที่ขอกู้มากกว่าวงเงินที่สามารถกู้ได้ (วงเงินที่กู้ได้สูงสุด " . number_format($maxcash, 2, '.', ',') . " บาท)");
        }
    }

    // ตรวจสอบระยะเวลาที่สามารถผ่อนได้สูงสุด
    public function check_period($validator, Loan $loan, $outstanding, $period) {
        $limit = $loan->loanType->limits()
            ->where('cash_begin', '<=', $outstanding)
            ->where('cash_end', '>=', $outstanding)
            ->first();

        if ($limit != null) {
            if ($limit->period < $period) {
                $validator->errors()->add('period', "ไม่สามารถกู้ได้ เนื่องจากระยะเวลาผ่อนชำระนานกว่าที่กำหนด (จำนวนงวดสูงสุด " . number_format($limit->period, 0, '.', ',') . " งวด)");
            }
        }
    }

    // ตรวจสอบจำนวนหุ้นที่จำเป็นต้องใช้ในการกู้
    public function check_shareholding($validator, Loan $loan, $outstanding) {
        $limit = $loan->loanType->limits()
            ->where('cash_begin', '<=', $outstanding)
            ->where('cash_end', '>=', $outstanding)
            ->first();
        $shareholding = $loan->member->shareholdings->sum('amount');
        $percent = ($shareholding / $outstanding) * 100; // ใช้หุ้น < เงินกู้

        if ($limit != null) {
            if ($percent < $limit->shareholding) {
                $validator->errors()->add('shareholding', "ไม่สามารถกู้ได้ เนื่องจากมีทุนเรือนหุ้นไม่พอ (ต้องการหุ้น " . number_format($limit->shareholding, 1, '.', ',') . "%, ผู้กู้มี " . number_format($percent, 1, '.', ',') . "%)");
            }
        }
    }

    // ตรวจสอบเงินเดือนผู้กู้ กรณีกู้สามัญ
    public function check_salarynormal($validator, Loan $loan, $salary, $outstanding) {
        $maxcash = $loan->loanType->limits->max('cash_end');
        $max_outstanding = ($salary * $loan->loanType->salarytimes >= $maxcash) ? $maxcash : $salary * $loan->loanType->salarytimes;

        if ($outstanding > $max_outstanding) {
            $validator->errors()->add('salary', "ไม่สามารถกู้ได้ เนื่องจากเงินเดือนน้อยกว่าวงเงินที่ขอกู้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_outstanding, 2, '.', ',') . " บาท)");
        }
    }

    // ตรวจสอบเงินเดือนผู้กู้ กรณีไม่ใช่กู้สามัญ
    public function check_salaryabnormal($validator, Loan $loan, $salary, $outstanding) {
        $maxcash = $loan->loanType->limits->max('cash_end');
        $max_outstanding = ($salary * $loan->loanType->salarytimes >= $maxcash) ? $maxcash : $salary * $loan->loanType->salarytimes;

        if ($outstanding > $max_outstanding) {
            $validator->errors()->add('salary', "ไม่สามารถกู้ได้ เนื่องจากเงินเดือนน้อยกว่าวงเงินที่ขอกู้ (วงเงินที่กู้ได้สูงสุด " . number_format($max_outstanding, 2, '.', ',') . " บาท)");
        }
    }

    // ตรวจสอบเงินเดือนสุทธิของผู้กู้
    public function check_netsalary($validator, Loan $loan, $netsalary, $outstanding, $period, $payment_type) {
        $pay = ($payment_type == 1)
            ? LoanCalculator::pmt($loan->loanType->rate, $outstanding, $period) 
            : LoanCalculator::flat($loan->loanType->rate, $outstanding, $period);
        if ($netsalary - $pay < 3000) {
            $validator->errors()->add('netsalary', "ไม่สามารถกู้ได้ เนื่องจากเงินเดือนสุทธิไม่พอสำหรับขอกู้ (ค่างวดต่อเดือน " . number_format($pay, 2, '.', ',') . " บาท)");
        }
    }

    // ตรวจสอบยอดเงินกู้สามัญ + กู้เฉพาะกิจอื่นๆ คงเหลือ
    public function check_overflow($validator, Loan $loan, $outstanding) {
        $loans = Loan::where('member_id', $loan->member_id)
            ->where('loan_type_id', '<>', 2)
            ->whereNotNull('code')
            ->whereNull('completed_at')
            ->get();
        $total_outstanding = $loans->sum('outstanding');
        $total_payments = $loans->sum(function($value) { return $value->payments->sum('principle'); });

        $balance = $total_outstanding - $total_payments;

        if ($outstanding + $balance > 1200000) {
            $validator->errors()->add('overflow', "ไม่สามารถกู้ได้ เนื่องจากผลรวมยอดเงินกู้สามัญ + กู้เฉพาะกิจอื่นๆ รวมกันแล้วเกิน 1,200,000 บาท (สามารถกู้สูงสุดได้ " . number_format(1200000 - $balance, 2, '.', ',') . " บาท)");
        }
    }

    // ตรวจสอบความสามารถในการค้ำประกันตนเองของพนักงาน / ลูกจ้าง
    public function check_employee_selfsurety($validator, Loan $loan, $outstanding) {
        $member = Member::find($loan->member_id);
        $available = ($member->shareholdings->sum('amount') * 0.9 < 1200000) ? $member->shareholdings->sum('amount') * 0.9 : 1200000;

        if ($available < $outstanding) {
            $validator->errors()->add('unavailable', "ไม่สามารถกู้ได้ เนื่องจาก 90% ของหุ้นผู้กู้มีเพียง " . number_format($available, 2, '.', ',') . " บาท");
        }
    }

    // ตรวจสอบความสามารถในการค้ำประกันตนเองของบุคคลภายนอก
    public function check_outsider_selfsurety($validator, Loan $loan, $outstanding) {
        $member = Member::find($loan->member_id);
        $shareholding = ($member->shareholdings->sum('amount') * 0.8 < 1200000) ? $member->shareholdings->sum('amount') * 0.8 : 1200000;
        $loans = Loan::where('member_id', $loan->member_id)
            ->whereNotNull('code')
            ->whereNull('completed_at');
        $balance = $loans->sum('outstanding') - $loans->get()->sum(function($value) { return $value->payments->sum('principle'); });
        $available = $shareholding - $balance;

        if ($available < $outstanding) {
            $validator->errors()->add('unavailable', "ไม่สามารถกู้ได้ เนื่องจาก 80% ของหุ้นผู้กู้มีเพียง " . number_format($available, 2, '.', ',') . " บาท");
        }
    }

    // ตรวจสอบจำนวนผู้ค้ำที่จำเป็นต้องใช้
    public function check_countsurety($validator, Loan $loan) {
        $limit = $loan->loanType->limits()
            ->where('cash_begin', '<=', $loan->outstanding)
            ->where('cash_end', '>=', $loan->outstanding)
            ->first();
        $limit_suerty = collect(explode('-', $limit->surety));

        if ($limit_suerty->max() < $loan->sureties->count() || $limit_suerty->min() > $loan->sureties->count()) {
            $validator->errors()->add('sureties', 'ต้องการผู้ค้ำประกัน ' . $limit->surety . ' คน (มีผู้ค้ำ ' . $loan->sureties->count() . ' คน)');
        }
    }

    // ตรวจสอบจำนวนยอดค้ำประกัน
    public function check_amountsurety($validator, Loan $loan) {
        $limit = $loan->loanType->limits()
            ->where('cash_begin', '<=', $loan->outstanding)
            ->where('cash_end', '>=', $loan->outstanding)
            ->first();
        $amount_sum = ($loan->sureties->count() > 0) ? $loan->sureties->sum('pivot.amount') : 0;

        if ($loan->outstanding != $amount_sum) {
            $validator->errors()->add('sureties_amount', 'จำนวนเงินค้ำประกันต้องเท่ากับยอดที่ต้องการกู้ (' . number_format($amount_sum, 2, '.', ',') . '/' . number_format($loan->outstanding, 2, '.', ',') . ' บาท)');
        }
    }
}