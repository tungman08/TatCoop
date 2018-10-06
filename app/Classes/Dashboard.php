<?php

namespace App\Classes;

use DB;
use Diamond;
use stdClass;
use App\Member;
use App\Shareholding;
use App\Loan;
use App\Payment;

class Dashboard {
    public function info() {
        $info = new stdClass();
        $info->members = Member::whereNull('leave_date')->count();
        $info->shareholdings = Shareholding::all()->sum('amount');
        $info->loans =  Loan::whereNull('completed_at')->get()->sum(function($value) { return $value->outstanding - $value->payments->sum('principle'); });

        return $info;
    }

    public function summary() {
        $members = Member::join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('prefixs', 'profiles.prefix_id', '=', 'prefixs.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->whereNull('members.leave_date')
            ->select(
                DB::raw("CONCAT('/service/member/', members.id) as link"),
                DB::raw("CONCAT(prefixs.name, ' ', profiles.name, ' ', profiles.lastname) as fullname"),
                DB::raw('employee_types.name as employee_type'),
                DB::raw("CONCAT('เป็นสมาชิกเมื่อ ', DATE_FORMAT(members.start_date, '%Y-%m-%d')) as message"))
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        $shareholdings = Member::join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('prefixs', 'profiles.prefix_id', '=', 'prefixs.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->join('shareholdings', 'members.id', '=', 'shareholdings.member_id')
            ->whereNull('members.leave_date')
            ->groupBy('members.id', 'prefixs.name', 'profiles.name', 'profiles.lastname', 'employee_types.name')
            ->select(
                DB::raw("CONCAT('/service/', members.id, '/shareholding') as link"),
                DB::raw("CONCAT(prefixs.name, ' ', profiles.name, ' ', profiles.lastname) as fullname"),
                DB::raw('employee_types.name as employee_type'),
                DB::raw("CONCAT('ทุนเรือนหุ้นสะสม ', FORMAT(SUM(shareholdings.amount), 2), ' บาท') as message"))
            ->orderByRaw('SUM(shareholdings.amount) desc')
            ->take(5)
            ->get();

        $loans = Member::join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('prefixs', 'profiles.prefix_id', '=', 'prefixs.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->join('loans', 'members.id', '=', 'loans.member_id')
            ->join(DB::raw('(SELECT loans.member_id as member_id, SUM(payments.principle) as principle FROM loans INNER JOIN payments ON loans.id = payments.loan_id WHERE loans.code IS NOT NULL AND loans.completed_at IS NULL GROUP BY loans.member_id) as l'), 'members.id', '=', 'l.member_id')
            ->whereNull('members.leave_date')
            ->whereNull('loans.completed_at')
            ->whereNotNull('loans.code')
            ->groupBy('members.id', 'prefixs.name', 'profiles.name', 'profiles.lastname', 'employee_types.name')
            ->select(
                DB::raw("CONCAT('/service/', members.id, '/loan') as link"),
                DB::raw("CONCAT(prefixs.name, ' ', profiles.name, ' ', profiles.lastname) as fullname"),
                DB::raw('employee_types.name as employee_type'),
                DB::raw("CONCAT('เงินกู้คงเหลือที่ยังไม่ได้ชำระรวม ', FORMAT(SUM(loans.outstanding) - l.principle, 2), ' บาท') as message"))
            ->orderByRaw('(SUM(loans.outstanding) - l.principle) desc')
            ->take(5)
            ->get();

            $summary = new stdClass();
            $summary->members = $members;
            $summary->shareholdings = $shareholdings;
            $summary->loans = $loans;

            return $summary;
    }

    public function chart() {
        $today = Diamond::today();

        $new_members = Member::whereYear('start_date', '=', $today->year)
            ->groupBy(DB::raw('MONTH(start_date)'))
            ->select(
                DB::raw("MONTH(start_date) as monthname"),
                DB::raw("COUNT(id) as amount"))
            ->get();

        $leaved_members = Member::whereYear('leave_date', '=', $today->year)
            ->groupBy(DB::raw('MONTH(leave_date)'))
            ->select(
                DB::raw("MONTH(leave_date) as monthname"),
                DB::raw("COUNT(id) as amount"))
            ->get();

        $now_shareholdings = Shareholding::where('amount', '>', 0)
            ->whereYear('pay_date', '=', $today->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(amount) as amount"))
            ->get();

        $draw_shareholdings = Shareholding::where('amount', '<', 0)
            ->whereYear('pay_date', '=', $today->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("ABS(SUM(amount)) as amount"))
            ->get();

        $last_shareholdings = Shareholding::where('amount', '>', 0)
            ->whereYear('pay_date', '=', $today->copy()->subYear()->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(amount) as amount"))
            ->get();

        $loans = Loan::whereNotNull('code')
            ->whereYear('loaned_at', '=', $today->year)
            ->groupBy(DB::raw('MONTH(loaned_at)'))
            ->select(
                DB::raw("MONTH(loaned_at) as monthname"),
                DB::raw("SUM(outstanding) as amount"))
            ->get();

        $now_interest = Payment::whereYear('pay_date', '=', $today->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(interest) as amount"))
            ->get();

        $last_interest = Payment::whereYear('pay_date', '=', $today->copy()->subYear()->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(interest) as amount"))
            ->get();

        $chart = new stdClass();
        $chart->members = $this->line_chart([$new_members, $leaved_members]);
        $chart->shareholdings = $this->line_chart([$now_shareholdings, $draw_shareholdings, $last_shareholdings]);
        $chart->loans = $this->line_chart([$loans, $now_interest, $last_interest]);

        return $chart;
    }

    protected function line_chart($array) {
        $result = collect([]);
        $year = Diamond::today()->year;

        $ticks = [];
        for ($i = 1; $i <= 12; $i++) {
            $ticks[] = [$i, Diamond::create($year, $i, 1, 0, 0, 0)->thai_format('M')];
        }

        $result->push($ticks);

        foreach ($array as $data) {
            $amounts = []; $i = 1;
            foreach ($data->sortBy('monthname') as $row) {
                $amounts[] = [$i, $row->amount];
                $i++;
            }

            for ($i = count($amounts) + 1; $i <= 12; $i++) {
                $amounts[] = [$i, 0];
            }

            $result->push($amounts);
        }

        return $result->toArray();
    }
}