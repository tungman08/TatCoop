<?php

namespace App\Classes;

class LoanCalculator {
    public function payment($rate, $payment_type, $outstanding, $period) {
        return ($payment_type == 1) ? 
            $this->payment_general($rate, $outstanding, $period) : 
            $this->payment_stable($rate, $outstanding, $period);
    }

    public function pmt($rate, $outstanding, $period) {
        return round($outstanding / ((1 - (1 / pow(1 + ($rate / 100 / 12), $period))) / ($rate / 100 / 12)), 0);
    }

    protected function payment_general($rate, $outstanding, $period) {
        $data = [];
        $pmt = $this->pmt($rate, $outstanding, $period);
        $monthRate = $rate / 100 / 12;

        $forward = $outstanding;

        for ($i = 0; $i < $period; $i++) {
            $month = $i + 1;
            $interest = $forward * $monthRate;
            $pay = ($i < $period - 1) ? ($pmt < $forward) ? $pmt : $forward + $interest : $forward + $interest;
            $principle = $pay - $interest;
            $balance = $forward - $principle;

            if ($pay > 0) {
                $payment = ['month' => "งวดที่ $month",
                    'pay' => $pay,
                    'interest' => $interest,
                    'principle' => $principle,
                    'balance' => $balance,
                ];

                $data[] = $payment;
            }

            $forward = $balance;
        }

        return $data;
    }

    protected function payment_stable($rate, $outstanding, $period) {
        $data = [];
        $pmt = $this->pmt($rate, $outstanding, $period);
        $monthRate = $rate / 100 / 12;

        $forward = $outstanding;

        for ($i = 0; $i < $period; $i++) {
            $month = $i + 1;
            $interest = $forward * $monthRate;
            $principle = ($i < $period - 1) ? ($pmt < $forward) ? $pmt : $forward : $forward;
            $pay = $principle + $interest;
            $addon = ($i < $period - 1) ? ($pmt < $forward) ? $this->addon($pay) : 0 : 0;
            $balance = $forward - ($principle + $addon);

            if ($pay > 0) {
                $payment = ['month' => "งวดที่ $month",
                    'pay' => $pay,
                    'addon' => $addon,
                    'interest' => $interest,
                    'principle' => $principle,
                    'balance' => $balance,
                ];

                $data[] = $payment;
            }
            
            $forward = $balance;
        }

        return $data;
    }

    private function addon($pay) {
        return ($pay - floor($pay) > 0) ? 1.00 - ($pay - floor($pay)) : 0;
    }
}