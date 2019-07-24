<?php

namespace App\Classes;

use App\Member;
use App\Loan;

use App\RoutinePayment;
use App\RoutinePaymentDetail;
use App\RoutineShareholding;
use App\RoutineShareholdingDetail;

use LoanCalculator;

class Routine
{
    // Constants
    const DAY = 10;

    public function shareholding($date, $member_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays(self::DAY - 1); // วันที่ 10
        $member = Member::find($member_id);

        if ($member->profile->employee->employee_type_id == 1) {
            if ($date->lessThan($d_day)) {
                if (RoutineShareholding::where('calculated_date', $startOfMonth)->whereNull('saved_date')->count() > 0) {
                    $routine = RoutineShareholding::where('calculated_date', $startOfMonth)->first();

                    if ($routine != null) {
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
        }
    }

    public function payment($date, $loan_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays(self::DAY - 1); // วันที่ 10
        $loan = Loan::find($loan_id);

        if ($loan->member->profile->employee->employee_type_id == 1) {
            if (RoutinePayment::where('calculated_date', $startOfMonth)->whereNull('saved_date')->count() > 0) {
                $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();

                if ($routine != null) {
                    $dayRate = $loan->rate / 100 / 365;
                    $pmt = ($loan->pmt == 0) ? LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period) : $loan->pmt;
                    $principle = $pmt / (1 + ($dayRate * $date->diffInDays($endOfMonth, false)));  

                    if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() > 0) {
                        // update
                        $detail = RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->first();
                        $detail->principle = round($principle, 2);
                        $detail->interest = round($pmt - $principle, 2);
                        $detail->save();
                    }
                    else {
                        // insert
                        $detail = new RoutinePaymentDetail();
                        $detail->routine_payment_id = $routine->id;
                        $detail->loan_id = $loan->member->id;
                        $detail->pay_date = Diamond::parse($endOfMonth->format('Y-m-d'));
                        $detail->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                        $detail->principle = round($principle, 2);
                        $detail->interest = round($pmt - $principle, 2);
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
                if (RoutinePayment::where('calculated_date', $startOfMonth)->whereNull('saved_date')->count() > 0) {
                    $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();
                    $payment = LoanCalculator::normal_payment($loan, $loan->pmt, $endOfMonth->format('Y-m-j'));

                    if ($routine != null) {
                        if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() == 0) {
                            // insert
                            $detail = new RoutinePaymentDetail();
                            $detail->routine_payment_id = $routine->id;
                            $detail->loan_id = $loan->member->id;
                            $detail->pay_date = Diamond::parse($endOfMonth->format('Y-m-d'));
                            $detail->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                            $detail->principle = round($payment->principle, 2);
                            $detail->interest = round($payment->interest, 2);
                            $detail->save();
                        }
                    }
                }
            }
        }
    }

    public function closeloan($date, $loan_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $d_day = $startOfMonth->copy()->addDays(self::DAY - 1); // วันที่ 10
        $loan = Loan::find($loan_id);

        if ($loan->member->profile->employee->employee_type_id == 1) {
            if ($date->lessThan($d_day)) {
                // ปิดก่อนวันที่ 10 ลบนำส่ง
                if (RoutinePayment::where('calculated_date', $startOfMonth)->whereNull('saved_date')->count() > 0) {
                    $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();
    
                    if ($routine != null) {
                        if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() > 0) {
                            // delete
                            $detail = RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->first();
                            $detail->delete();
                        }
                    }
                }
            }
            else {
                // ปิดหลังวันที่ 10 คำนวณนำส่งใหม่
                if (RoutinePayment::where('calculated_date', $startOfMonth)->whereNull('saved_date')->count() > 0) {
                    $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();

                    if ($routine != null) {
                        $dayRate = $loan->rate / 100 / 365;
                        $pmt = ($loan->pmt == 0) ? LoanCalculator::pmt($loan->rate, $loan->outstanding, $loan->period) : $loan->pmt;
                        $principle = $pmt / (1 + ($dayRate * $date->diffInDays($endOfMonth, false)));        

                        if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() > 0) {            
                            // update
                            $detail = RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->first();
                            $detail->principle = round($principle, 2);
                            $detail->interest = round($pmt - $principle, 2);
                            $detail->save();
                        }
                        else {
                            // insert
                            $detail = new RoutinePaymentDetail();
                            $detail->routine_payment_id = $routine->id;
                            $detail->loan_id = $loan->member->id;
                            $detail->pay_date = Diamond::parse($endOfMonth->format('Y-m-d'));
                            $detail->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                            $detail->principle = round($principle, 2);
                            $detail->interest = round($pmt - $principle, 2);
                            $detail->save();
                        }
                    }
                }
            }
        }
    }

    public function delete($date, $loan_id) {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $loan = Loan::find($loan_id);

        if (RoutinePayment::where('calculated_date', $startOfMonth)->whereNull('saved_date')->count() > 0) {
            $routine = RoutinePayment::where('calculated_date', $startOfMonth)->first();
            $payment = LoanCalculator::normal_payment($loan, $loan->pmt, $endOfMonth->format('Y-m-j'));

            if ($routine != null) {
                if (RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->count() > 0) {            
                    // update
                    $detail = RoutinePaymentDetail::where('routine_payment_id', $routine->id)->where('loan_id', $loan->id)->first();
                    $detail->principle = round($payment->principle, 2);
                    $detail->interest = round($payment->interest, 2);
                    $detail->save();
                }
            }
        }
    }

    public function dday() {
        return self::DAY;
    }
}