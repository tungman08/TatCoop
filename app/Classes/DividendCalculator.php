<?php

namespace App\Classes;

use DB;
use Diamond;
use stdClass;
use App\Dividend;
use App\Dividendmember;
use App\Member;
use App\Payment;
use App\Shareholding;

class DividendCalculator {
    public function calculate($year, $force) {
        if (empty($force)) {
            return $this->dividend($year);
        }
        else {
            return $this->dividend_force($year);
        }
    }

    protected function dividend($year) {
        $result = 'Nothing';

        if (!is_null($year)) {
            $dividend = Dividend::where('calculated', false)
                ->where('rate_year', $year)
                ->first();

            if (!is_null($dividend)) {
                $result = $this->addnew($dividend->id);
            }
        }
        else {
            $dividends = Dividend::where('calculated', false)
                ->get();

            if ($dividends->count() > 0) {
                foreach ($dividends as $dividend) {
                    $this->addnew($dividend->id);
                }

                $result = "Created dividend {{$dividends->count()}} year(s).";
            }
        }

        return $result;
    }

    protected function dividend_force($year) {
        $result = 'Nothing';

        if (!is_null($year)) {
            $dividend = Dividend::where('rate_year', $year)
                ->first();

            if (!is_null($dividend)) {
                $result = $this->update($dividend->id);        
            }
        }
        else {
            $dividends = Dividend::all();

            if ($dividends->count() > 0) {
                foreach ($dividends as $dividend) {       
                    $this->update($dividend->id);
                }

                $result = "Updated dividend {{$dividends->count()}} year(s).";
            }
        }

        return $result;
    }

    public function addnew($id) {
        $result = 'Nothing';
        $dividend = Dividend::find($id);
        $count = Dividendmember::where('dividend_id', $dividend->id)->count();

        if ($count == 0) {
            $members = Member::whereYear('start_date', '<=', $dividend->rate_year)
                ->where(function ($query) use ($dividend) {
                    $query->whereYear('leave_date', '>', $dividend->rate_year)
                        ->orWhereNull('leave_date');
                })->get();
            
            foreach ($members as $member) {
                $member_dividends = $this->dividend_calculate($member->id, $dividend->id);

                foreach ($member_dividends as $member_dividend) {
                    DB::transaction(function() use ($member_dividend, $dividend, $member) {
                        $dividend_member = new Dividendmember();
                        $dividend_member->member_id = $member->id;
                        $dividend_member->dividend_name = $member_dividend->name;
                        $dividend_member->dividend_date = $member_dividend->date;
                        $dividend_member->shareholding = $member_dividend->shareholding;
                        $dividend_member->shareholding_dividend = $member_dividend->shareholding_dividend;
                        $dividend_member->interest = $member_dividend->interest;
                        $dividend_member->interest_dividend = $member_dividend->interest_dividend;
                        $dividend->members()->save();
                    });
                }
            }

            $retult = "Created dividend's year {{$dividend->rate_year}}.";
        }

        return $result;
    }

    public function update($id) {
        $dividend = Dividend::find($id);
        $count = Dividendmember::where('dividend_id', $dividend->id)->count();

        if ($count > 0) {
            DB::statement('update dividend_member ' .
                'set interest = 0, ' .
                'interest_dividend = 0 ' .
                'where dividend_id = ' . $dividend->id . ';');

            // DB::statement('update dividend_member as d, ' .
            //     '(' .
            //     'select l.member_id, sum(p.interest) as interest ' .
            //     'from loans l ' .
            //     'inner join payments p on l.id = p.loan_id ' .
            //     'where year(p.pay_date) = ' . strval($dividend->rate_year - 1) . ' and month(p.pay_date) = 12 ' .
            //     'group by l.member_id' .
            //     ') as p ' .
            //     'set d.interest = p.interest, ' .
            //     'd.interest_dividend = (p.interest * ' . ($dividend->loan_rate / 100) . ') ' .
            //     'where d.member_id = p.member_id ' .
            //     'and d.dividend_id = ' . $dividend->id . ' ' .
            //     'and year(d.dividend_date) = ' . $dividend->rate_year . ' ' .
            //     'and month(d.dividend_date) = 1 and ' .
            //     'day(d.dividend_date) = 1;');

            // for ($month = 1; $month < 12; $month++) {
            for ($month = 1; $month <= 12; $month++) {
                DB::statement('update dividend_member as d, ' .
                    '(' .
                    'select l.member_id, sum(p.interest) as interest ' .
                    'from loans l ' .
                    'inner join payments p on l.id = p.loan_id ' .
                    'where year(p.pay_date) = ' . $dividend->rate_year . ' and month(p.pay_date) = ' . $month . ' ' .
                    'group by l.member_id' .
                    ') as p ' .
                    'set d.interest = p.interest, ' .
                    'd.interest_dividend = (p.interest * ' . ($dividend->loan_rate / 100) . ') ' .
                    'where d.member_id = p.member_id ' .
                    'and d.dividend_id = ' . $dividend->id . ' ' .
                    'and year(d.dividend_date) = ' . $dividend->rate_year . ' ' .
                    'and month(d.dividend_date) = ' . $month . ' ' .
                    'and day(d.dividend_date) <> 1;');
            }
        }
        else {
            $this->addnew($dividend->id);
        }

        return "Updated dividend's year {{$dividend->rate_year}}.";
    }

    protected function dividend_calculate($member_id, $dividend_id) {
        $dividend = Dividend::find($dividend_id);

        $forward_shareholding = Shareholding::where('member_id', $member_id)
            ->whereYear('pay_date', '<', $dividend->rate_year)
            ->sum('amount');

        $member_dividends = collect([]);

        $item = new stdClass();
        $item->name = 'ยอดยกมา';
        $item->date = Diamond::parse("{$dividend->rate_year}-1-1");
        $item->shareholding = $forward_shareholding;
        $item->shareholding_dividend = $forward_shareholding * ($dividend->shareholding_rate / 100);
        $item->interest = 0;
        $item->interest_dividend = 0;
        $member_dividends->push($item);

        $shareholdings = Shareholding::where('member_id', $member_id)
            ->whereYear('pay_date', '=', $dividend->rate_year)
            ->get();

        $payments = Payment::join('loans', 'payments.loan_id', '=', 'loans.id')
            ->where('loans.member_id', $member_id)
            ->whereYear('payments.pay_date', '=', $dividend->rate_year)
            ->get();

        for ($month = 1; $month <= 12; $month++) {
            $shareholding = $shareholdings->filter(function ($value, $key) use ($month) { 
                return Diamond::parse($value->pay_date)->month == $month; 
            })->sum('amount');

            $interest = $payments->filter(function ($value, $key) use ($month) { 
                return Diamond::parse($value->pay_date)->month == $month; 
            })->sum('interest');

            $item = new stdClass();
            $item->name = Diamond::parse("{$dividend->rate_year}-$month-1")->thai_format('M Y');
            $item->date = Diamond::parse("{$dividend->rate_year}-$month-31");
            $item->shareholding = $shareholding;
            $item->shareholding_dividend = $shareholding * ($dividend->shareholding_rate / 100) * ((12 - $month) / 12);
            $item->interest = $interest;
            $item->interest_dividend = $interest * ($dividend->loan_rate / 100);
            $member_dividends->push($item);      
        }

        return $member_dividends;
    }
}