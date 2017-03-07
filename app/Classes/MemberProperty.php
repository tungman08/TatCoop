<?php

namespace App\Classes;

use App\Dividend;
use App\Member;
use App\Shareholding;
use DB;

/**
 * MemberProperty short summary.
 *
 * MemberProperty description.
 *
 * @version 1.0
 * @author tungm
 */
class MemberProperty
{
    public function findPrev($id) {
        $min_id = Member::active()->min('id');

        if ($id > $min_id) {
            $member = $this->getMember($id - 1);

            while ($member->leave_date != null) {
                $member = $this->getMember($member->id - 1);
            }

            return ($member->id >= $min_id) ? $member->id : $min_id;
        }
        else {
            return $id;
        }
    }

    public function findNext($id) {
        $max_id = Member::active()->max('id');

        if ($id < $max_id) {
            $member = $this->getMember($id + 1);

            while ($member->leave_date != null) {
                $member = $this->getMember($member->id + 1);
            }

            return ($member->id <= $max_id) ? $member->id : $max_id;
        }
        else {
            return $id;
        }
    }

    protected function getMember($id) {
        return Member::find($id);
    }

    public function getDividend($id, $year) {
        $dividend_month = (Diamond::today()->year > $year) ? 12 : Diamond::today()->month;
        $dividend_rate = Dividend::where('rate_year', $year)->first();
        $forward_amount = Shareholding::where('member_id', $id)->whereYear('pay_date', '<', $year)->sum('amount');
        $forward = collect([(object)[
            'name' => 'ยอดยกมา', 
            'shareholding' => $forward_amount / 10, 
            'amount' => $forward_amount, 
            'dividend' => (is_null($dividend_rate) ? 0 : $forward_amount * ($dividend_rate->rate / 100) * ($dividend_month / $dividend_month)),
            'remark' => (is_null($dividend_rate) ? 'ยังไม้ได้กำหนดอัตราเงินปันผล' : '')
        ]]);
        $dividends = $forward->merge(Shareholding::where('member_id', $id)
            ->whereYear('pay_date', '=', $year)
            ->select(DB::raw('str_to_date(concat(\'1/\', month(pay_date), \'/\', year(pay_date)), \'%d/%m/%Y\') as name'),
                DB::raw('sum(amount) / 10 as shareholding'),
                DB::raw('sum(amount) as amount'),
                DB::raw(is_null($dividend_rate) ? 
                    '0 as dividend' : 
                    '(sum(amount) * ' . ($dividend_rate->rate / 100) . ' * (' . $dividend_month . ' - month(pay_date)) / ' . $dividend_month . ') as dividend'),
                DB::raw(is_null($dividend_rate) ? '\'ยังไม้ได้กำหนดอัตราเงินปันผล\' as remark' : '\'\' as remark'))
            ->groupBy(DB::raw('year(pay_date)'), DB::raw('month(pay_date)'))
            ->get());

        return $dividends;
    }

    public function getExcelColumn($val) {
        $first = floor(($val / 26));
        $second = $val % 26;

        if ($first > 0) {
            if ($second == 0) {
                return 'Z';
            }
            else {
                return chr(64 + $first) . chr(64 + $second);
            }
        }
        else {
            return chr(64 + $second);
        }
    }
}