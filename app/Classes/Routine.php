<?php

namespace App\Classes;

use App\Member;
use App\Loan;

use App\RoutinePayment;
use App\RoutinePaymentDetail;
use App\RoutineShareholding;
use App\RoutineShareholdingDetail;

use Diamond;
use LoanCalculator;

class Routine
{
    // Constants
    const DAY = 10;

    public function shareholding($date, $member_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays(self::DAY - 1); // วันที่ 10

        if ($date->lessThan($d_day)) {
            $member = Member::find($member_id);

            if (RoutineShareholding::where('calculated_date', $startOfMonth)->count() > 0) {
                $routine = RoutineShareholding::where('calculated_date', $startOfMonth)->first();

                if (RoutineShareholdingDetail::where('routine_shareholding_id', $routine->id)->where('member_id', $member->id)->count() > 0) {
                    // update
                    $detail = RoutineShareholdingDetail::where('routine_shareholding_id', $routine->id)->where('member_id', $member_id)->first();
                    $detail->amount = $member->shareholding * 10;
                    $detail->save();
                }
                else {
                    // insert
                    $detail = new RoutineShareholdingDetail();
                    $detail->routine_shareholding_id = $routine->id;
                    $detail->member_id = $member->id;
                    $detail->pay_date = Diamond::parse($endOfMonth->format('Y-m-d'));
                    $detail->amount = $member->shareholding * 10;
                    $detail->save();
                }
            }
        }
    }

    public function payment($date, $loan_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays(self::DAY - 1); // วันที่ 10

        if ($date->lessThan($d_day)) {
            $loan = Loan::find($loan_id);

            if ($loan->member->profile->employee->employee_type_id == 1) {
                if (RoutinePayment::where('calculated_date', $startOfMonth)->count() > 0) {
                    $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();
                    $payment = LoanCalculator::monthly_payment($loan, $date);
    
                    if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() > 0) {
                        // update
                        $detail = RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan)->first();
                        $detail->principle = $payment->principle;
                        $detail->interest = $payment->interest;
                        $detail->save();
                    }
                    else {
                        // insert
                        $detail = new RoutinePaymentDetail();
                        $detail->routine_payment_id = $routine->id;
                        $detail->loan_id = $loan->member->id;
                        $detail->pay_date = Diamond::parse($endOfMonth->format('Y-m-d'));
                        $detail->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                        $detail->principle = $payment->principle;
                        $detail->interest = $payment->interest;
                        $detail->save();
                    }
                }
            }
        }
    }

    public function createloan($date, $loan_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays(self::DAY - 1); // วันที่ 10

        if ($date->lessThan($d_day)) {
            $loan = Loan::find($loan_id);

            if ($loan->member->profile->employee->employee_type_id == 1) {
                if (RoutinePayment::where('calculated_date', $startOfMonth)->count() > 0) {
                    $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();
                    $payment = LoanCalculator::monthly_payment($loan, $date);

                    if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() == 0) {
                        // insert
                        $detail = new RoutinePaymentDetail();
                        $detail->routine_payment_id = $routine->id;
                        $detail->loan_id = $loan->member->id;
                        $detail->pay_date = Diamond::parse($endOfMonth->format('Y-m-d'));
                        $detail->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                        $detail->principle = $payment->principle;
                        $detail->interest = $payment->interest;
                        $detail->save();
                    }
                }
            }
        }
    }

    public function closeloan($date, $loan_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays(self::DAY - 1); // วันที่ 10

        if ($date->lessThan($d_day)) {
            $loan = Loan::find($loan_id);

            if ($loan->member->profile->employee->employee_type_id == 1) {
                if (RoutinePayment::where('calculated_date', $startOfMonth)->count() > 0) {
                    $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();
    
                    if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() > 0) {
                        // delete
                        $detail = RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->first();
                        $detail->delete();
                    }
                }
            }
        }
    }

    public function dday() {
        return self::DAY;
    }
}