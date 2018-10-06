<?php

namespace App\Classes;

use DB;
use Diamond;
use MemberProperty as M_Property;
use App\Member;
use App\Dividend;
use App\Dividendmember;

class DividendCalculator {
    public function calculate($year = null) {
        $result = false;
        $today = Diamond::today();
        $dividend = (is_null($year)) ? 
            Dividend::where('rate_year', $today->year - 1)->first() :
            Dividend::where('rate_year', $year)->first();

        if (!is_null($dividend)) {
            $count = Dividendmember::where('diviend_id', $dividend->id)->count();

            if ($count == 0) {
                $members = Member::active()->get();

                foreach ($members as $member) {
                    if ($dividend->members->where('member_id', $member->id)->count() == 0) {
                        $m_dividends = M_Property::getDividend($member, $dividend->rate_year);
    
                        foreach ($m_dividends as $m_dividend) {
                            DB::transaction(function() use ($m_dividend, $dividend, $member) {
                                $dividend_member = new Dividendmember();
                                $dividend_member->dividend_id = $dividend->id;
                                $dividend_member->member_id = $member->id;
                                $dividend_member->dividend_name = $m_dividend->name;
                                $dividend_member->shareholding = $m_dividend->shareholding;
                                $dividend_member->shareholding_dividend = $m_dividend->shareholding_dividend;
                                $dividend_member->interest = $m_dividend->interest;
                                $dividend_member->interest_dividend = $m_dividend->interest_dividend;
                                $dividend_member->save();
                            });
                        }
                    }
                }
            }

            $result = true;
         }

        return $result;
    }
}