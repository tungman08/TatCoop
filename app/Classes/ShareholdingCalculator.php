<?php

namespace App\Classes;

use DB;
use Diamond;
use stdClass;
use App\Member;
use App\RoutineSetting;
use App\RoutineShareholding;
use App\RoutineShareholdingDetail;
use App\Shareholding;

class ShareholdingCalculator {
    public function calculate($date) {
        $setting = RoutineSetting::find(1);
        
        if ($setting->calculate_status == true) {
            return $this->shareholding($date);
        }

        return 'Nothing';
    }

    public function store() {
        $setting = RoutineSetting::find(1);

        if ($setting->save_status == true) {
            return $this->save();
        }

        return 'Nothing';
    }

    protected function shareholding($date) {
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
        $count = RoutineShareholding::whereDate('calculated_date', '=', $mydate)->count();

        if ($count == 0) {
            $members = Member::join('employees', 'employees.profile_id', '=', 'members.profile_id')
                ->where('employees.employee_type_id', 1)
                ->whereYear('members.start_date', '<=', $mydate->year)
                ->where(function ($query) use ($mydate) {
                    $query->whereYear('members.leave_date', '>', $mydate->year)
                        ->orWhereNull('members.leave_date'); })
                ->select([
                    'members.id',
                    'members.shareholding'])
                ->get();

            DB::transaction(function() use ($mydate, $members) {
                $routine = new RoutineShareholding();
                $routine->calculated_date = $mydate;
                $routine->save();
    
                foreach ($members as $member) {
                    $detail = new RoutineShareholdingDetail();
                    $detail->routine_shareholding_id = $routine->id;
                    $detail->member_id = $member->id;
                    $detail->pay_date = Diamond::parse($mydate->endOfMonth()->format('Y-m-d'));
                    $detail->amount = $member->shareholding * 10;
                    $detail->save();
                }
            });

            $result = "Created {$members->count()} shareholding(s).";
        }

        return $result;
    }

    protected function save() {
        $routines = RoutineShareholding::whereNotNull('approved_date')
            ->whereNull('saved_date')
            ->where('status', false)
            ->get();

        if ($routines->count() > 0) {
            foreach ($routines as $routine) {
                DB::transaction(function() use ($routine) {
                    foreach ($routine->details as $detail) {
                        if ($detail->status == false) {
                            $shareholding = new Shareholding();
                            $shareholding->member_id = $detail->member_id;
                            $shareholding->shareholding_type_id = 1;
                            $shareholding->pay_date = Diamond::parse($detail->pay_date);
                            $shareholding->amount = $detail->amount;
                            $shareholding->remark = 'ป้อนข้อมูลอัตโนมัติ';
                            $shareholding->save();
                
                            $detail->status = true;
                            $detail->save();
                        }
                    }

                    $routine->saved_date = Diamond::today();
                    $routine->status = true;
                    $routine->save();
                });
            }

            return 'Save all shareholding to database successfully.';
        }

        return 'Nothing';
    }
}