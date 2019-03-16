<?php

namespace App\Classes;

use DB;
use Diamond;
use stdClass;
use App\Member;
use App\Shareholding;
use App\Loan;
use App\LoanType;
use App\Payment;

class Dashboard {
    public function info() {
        $info = new stdClass();
        $info->members = Member::whereNull('leave_date')->count();
        $info->shareholdings = Shareholding::all()->sum('amount');
        $info->loans =  Loan::whereNull('completed_at')->get()->sum(function($value) { return $value->outstanding - $value->payments->sum('principle'); });

        return $info;
    }

    public function summary($year) {
        $selected = Diamond::parse("$year-1-1");

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
            ->orderBy('members.start_date', 'desc')
            ->orderBy('members.id', 'desc')
            ->take(5)
            ->get();

        $monthshareholding = Member::join('profiles', 'members.profile_id', '=', 'profiles.id')
            ->join('prefixs', 'profiles.prefix_id', '=', 'prefixs.id')
            ->join('employees', 'profiles.id', '=', 'employees.profile_id')
            ->join('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
            ->whereNull('members.leave_date')
            ->select(
                DB::raw("CONCAT('/service/', members.id, '/shareholding') as link"),
                DB::raw("CONCAT(prefixs.name, ' ', profiles.name, ' ', profiles.lastname) as fullname"),
                DB::raw('employee_types.name as employee_type'),
                DB::raw("CONCAT('หุ้นรายเดือน ', FORMAT(members.shareholding, 0), ' หุ้น') as message"))
            ->orderByRaw('members.shareholding desc')
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

            $loantypes = LoanType::join('loans', 'loan_types.id', '=', 'loans.loan_type_id')
                ->whereNull('loan_types.deleted_at')
                ->whereNotNull('loans.code')
                ->whereYear('loaned_at', '=', $selected->year)
                ->groupBy('loan_types.id', 'loan_types.name')
                ->select(
                    DB::raw("CONCAT('/database/loantype/', loan_types.id) as link"),
                    DB::raw("loan_types.name as fullname"),
                    DB::raw("CONCAT(FORMAT(COUNT(loans.id), 0), ' สัญญา') as employee_type"),
                    DB::raw("CONCAT('เป็นเงิน ', FORMAT(SUM(loans.outstanding), 2), ' บาท') as message"))
                ->orderByRaw('SUM(loans.outstanding) desc, COUNT(loans.id) desc')
                ->take(5)
                ->get();

            $summary = new stdClass();
            $summary->members = $members;
            $summary->monthshareholding = $monthshareholding;
            $summary->shareholdings = $shareholdings;
            $summary->loans = $loans;
            $summary->loantypes = $loantypes;

            return $summary;
    }

    public function chart($year) {
        $selected = Diamond::parse("$year-1-1");
        $lastyear = $selected->copy()->subYear();

        $new_members = Member::whereYear('start_date', '=', $selected->year)
            ->groupBy(DB::raw('MONTH(start_date)'))
            ->select(
                DB::raw("MONTH(start_date) as monthname"),
                DB::raw("COUNT(id) as amount"))
            ->get();

        $leaved_members = Member::whereYear('leave_date', '=', $selected->year)
            ->groupBy(DB::raw('MONTH(leave_date)'))
            ->select(
                DB::raw("MONTH(leave_date) as monthname"),
                DB::raw("COUNT(id) as amount"))
            ->get();

        $now_shareholdings = Shareholding::where('amount', '>', 0)
            ->whereYear('pay_date', '=', $selected->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(amount) as amount"))
            ->get();

        $total_shareholdings = Shareholding::whereYear('pay_date', '=', $selected->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(amount) as amount"))
            ->get();

        $forward = Shareholding::whereYear('pay_date', '<', $selected->year)->sum('amount');
        $total = $forward;
        $cumulate_shareholdings = collect();

        for ($i = 1; $i < $total_shareholdings->min('monthname'); $i++) {
            $object = new stdClass();
            $object->monthname = $i;
            $object->amount = $total;

            $cumulate_shareholdings->push($object);
        }

        foreach($total_shareholdings as $item) {
            $total += $item->amount;

            $object = new stdClass();
            $object->monthname = $item->monthname;
            $object->amount = $total;
            $cumulate_shareholdings->push($object);
        }

        for ($i = $cumulate_shareholdings->count() + 1; $i <= 12; $i++) {
            $object = new stdClass();
            $object->monthname = $i;
            $object->amount = $total;

            $cumulate_shareholdings->push($object);
        }

        $draw_shareholdings = Shareholding::where('amount', '<', 0)
            ->whereYear('pay_date', '=', $selected->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("ABS(SUM(amount)) as amount"))
            ->get();

        $last_shareholdings = Shareholding::where('amount', '>', 0)
            ->whereYear('pay_date', '=', $lastyear->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(amount) as amount"))
            ->get();

        $now_payment = Payment::whereYear('pay_date', '=', $selected->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(principle + interest) as amount"))
            ->get();

        $last_payment = Payment::whereYear('pay_date', '=', $lastyear->year)
            ->groupBy(DB::raw('MONTH(pay_date)'))
            ->select(
                DB::raw("MONTH(pay_date) as monthname"),
                DB::raw("SUM(principle + interest) as amount"))
            ->get();

        $loans = Loan::whereNotNull('code')
            ->whereYear('loaned_at', '=', $selected->year)
            ->whereNotNull('code')
            ->groupBy(DB::raw('MONTH(loaned_at)'))
            ->select(
                DB::raw("MONTH(loaned_at) as monthname"),
                DB::raw("SUM(outstanding) as amount"))
            ->get();

        $normalloans = Loan::whereNotNull('code')
            ->whereYear('loaned_at', '=', $selected->year)
            ->whereNotNull('code')
            ->where('loan_type_id', '=', 1)
            ->groupBy(DB::raw('MONTH(loaned_at)'))
            ->select(
                DB::raw("MONTH(loaned_at) as monthname"),
                DB::raw("SUM(outstanding) as amount"))
            ->get();

        $emergingloans = Loan::whereNotNull('code')
            ->whereYear('loaned_at', '=', $selected->year)
            ->whereNotNull('code')
            ->where('loan_type_id', '=', 2)
            ->groupBy(DB::raw('MONTH(loaned_at)'))
            ->select(
                DB::raw("MONTH(loaned_at) as monthname"),
                DB::raw("SUM(outstanding) as amount"))
            ->get();

        $specialloans = Loan::whereNotNull('code')
            ->whereYear('loaned_at', '=', $selected->year)
            ->whereNotNull('code')
            ->where('loan_type_id', '>', 2)
            ->groupBy(DB::raw('MONTH(loaned_at)'))
            ->select(
                DB::raw("MONTH(loaned_at) as monthname"),
                DB::raw("SUM(outstanding) as amount"))
            ->get();

        // $last_loans = Loan::whereNotNull('code')
        //     ->whereYear('loaned_at', '=', $lastyear->year)
        //     ->whereNotNull('code')
        //     ->groupBy(DB::raw('MONTH(loaned_at)'))
        //     ->select(
        //         DB::raw("MONTH(loaned_at) as monthname"),
        //         DB::raw("SUM(outstanding) as amount"))
        //     ->get();

        $chart = new stdClass();
        $chart->members = $this->line_chart($selected->year, [$new_members, $leaved_members]);
        $chart->totalshareholdings = $this->line_chart($selected->year, [$cumulate_shareholdings]);
        $chart->shareholdings = $this->line_chart($selected->year, [$now_shareholdings, $draw_shareholdings, $last_shareholdings]);
        $chart->loans = $this->line_chart($selected->year, [$now_payment, $last_payment]);
        $chart->loantypes = $this->line_chart($selected->year, [$loans, $normalloans, $emergingloans, $specialloans]);

        return $chart;
    }

    protected function line_chart($year, $array) {
        $result = collect([]);

        $ticks = [];
        for ($i = 1; $i <= 12; $i++) {
            $ticks[] = [$i, Diamond::create($year, $i, 1, 0, 0, 0)->thai_format('M')];
        }

        $result->push($ticks);

        foreach ($array as $data) {
            $amounts = [];

            for ($i = 1; $i < $data->min('monthname'); $i++) {
                $amounts[] = [$i, 0];
            }

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