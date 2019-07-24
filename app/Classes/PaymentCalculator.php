<?php

namespace App\Classes;

use DB;
use Diamond;
use stdClass;
use LoanCalculator;
use App\Member;
use App\RoutineSetting;
use App\RoutinePayment;
use App\RoutinePaymentDetail;
use App\Loan;
use App\Payment;

class PaymentCalculator {
    public function calculate($date) {
        $setting = RoutineSetting::find(2);
        
        if ($setting->calculate_status == true) {
            return $this->payment($date);
        }

        return 'Nothing';
    }
 
    public function approve($date) {
        $setting = RoutineSetting::find(2);
        
        if ($setting->approve_status == true) {
            return $this->check($date);
        }

        return 'Nothing';
    }

    public function store() {
        $setting = RoutineSetting::find(2);

        if ($setting->save_status == true) {
            return $this->save();
        }

        return 'Nothing';
    }

    protected function payment($date) {
        $result = 'Nothing';

        if (!empty($date)) {
            if (Diamond::createFromFormat('Y-m-d', $date) === false) {
                $result = 'Invalid date format.';
            }
            else {
                $mydate = Diamond::parse($date);
                $result = $this->addnew($mydate);
            }
        }
        else {
            $mydate = Diamond::today();
            $result = $this->addnew($mydate);
        }

        return $result;
    }

    public function addnew($date) {
        $result = 'Nothing';
        $mydate = Diamond::parse($date->startOfMonth()->format('Y-m-d'));
        $count = RoutinePayment::whereDate('calculated_date', '=', $mydate)->count();

        if ($count == 0) {
            $members = Member::join('employees', 'employees.profile_id', '=', 'members.profile_id')
                ->join('loans', 'members.id', '=', 'loans.member_id')
                ->where('employees.employee_type_id', 1)
                ->whereYear('members.start_date', '<=', $mydate->year)
                ->where(function ($query) use ($mydate) {
                    $query->whereYear('members.leave_date', '>', $mydate->year)
                        ->orWhereNull('members.leave_date'); })
                ->whereNotNull('loans.code')
                ->whereNull('loans.completed_at')
                ->select([
                    'members.id as id',
                    'loans.id as loan_id'])
                ->get();

            DB::transaction(function() use ($mydate, $members) {
                $routine = new RoutinePayment();
                $routine->calculated_date = $mydate;
                $routine->save();
    
                foreach ($members as $member) {
                    $loan = Loan::find($member->loan_id);
                    $payment = LoanCalculator::monthly_payment($loan, $mydate);

                    $detail = new RoutinePaymentDetail();
                    $detail->routine_payment_id = $routine->id;
                    $detail->loan_id = $member->loan_id;
                    $detail->pay_date = Diamond::parse($mydate->endOfMonth()->format('Y-m-d'));
                    $detail->period = ($loan->payments->count() > 0) ? $loan->payments->max('period') + 1 : 1;
                    $detail->principle = round($payment->principle, 2);
                    $detail->interest = round($payment->interest, 2);
                    $detail->save();
                }
            });

            $result = "Created {$members->count()} payment(s).";
        }

        return $result;
    }
    
    protected function check($date) {
        $result = 'Nothing';

        if (!empty($date)) {
            if (Diamond::createFromFormat('Y-m-d', $date) === false) {
                $result = 'Invalid date format.';
            }
            else {
                $mydate = Diamond::parse($date);
                $result = $this->setapprove($mydate);
            }
        }
        else {
            $mydate = Diamond::today();
            $result = $this->setapprove($mydate);
        }

        return $result;
    }

    protected function setapprove($date) {
        $routines = RoutinePayment::whereNull('approved_date')
            ->whereNull('saved_date')
            ->where('status', false)
            ->get();

        if ($routines->count() > 0) {
            foreach ($routines as $routine) {
                DB::transaction(function() use ($routine, $date) {
                    $routine->approve_date = $date;
                    $routine->save();
                });
            }

            return 'Approved all payment to database successfully.';
        }

        return 'Nothing';
    }

    protected function save() {        
        $this->updateStatus();
        
        $routines = RoutinePayment::whereNotNull('approved_date')
            ->whereNull('saved_date')
            ->where('status', false)
            ->get();

        if ($routines->count() > 0) {
            foreach ($routines as $routine) {
                DB::transaction(function() use ($routine) {
                    foreach ($routine->details as $detail) {
                        if ($detail->status == false) {
                            $payment = new Payment();
                            $payment->loan_id = $detail->member_id;
                            $payment->pay_date = Diamond::parse($detail->pay_date);
                            $payment->period = $detail->period;
                            $payment->principle = $detail->principle;
                            $payment->interest = $detail->interest;
                            $payment->remark = 'ป้อนข้อมูลอัตโนมัติ';
                            $payment->save();
                
                            $detail->status = true;
                            $detail->save();
                        }
                    }

                    $routine->saved_date = Diamond::today();
                    $routine->status = true;
                    $routine->save();
                });
            }

            return 'Saved all payment to database successfully.';
        }

        return 'Nothing';
    }

    protected function updateStatus() {
        // also would work, temporary turn off auto timestamps
        with($model = new RoutinePaymentDetail)->timestamps = false;

        $model->join('payments', function ($query) {
            $query->on('routine_payment_details.loan_id', '=', 'payments.loan_id')
                ->on('routine_payment_details.pay_date', '=', 'payments.pay_date'); })
        ->where('routine_payment_details.status', false)
        ->update([
            'routine_payment_details.status' => true
        ]);
    }
}