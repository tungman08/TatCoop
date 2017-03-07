<?php

use Illuminate\Database\Seeder;

class LoanTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'เงินกู้สามัญ', 'rate' => 6.5, 'start_date' => Diamond::minValue(), 'expire_date' => Diamond::maxValue(), 'limits' => [
                ['cash_begin' => 1.0, 'cash_end' => 300000.0, 'shareholding' => '15.0', 'surety' => '1-2', 'period' => 72],
                ['cash_begin' => 300001.0, 'cash_end' => 700000.0, 'shareholding' => '20.0', 'surety' => '1-2', 'period' => 100],
                ['cash_begin' => 700001.0, 'cash_end' => 1200000.0, 'shareholding' => '25.0', 'surety' => '2-3', 'period' => 120],
            ]],
            ['name' => 'เงินกู้ฉุกเฉิน', 'rate' => 6.5,'start_date' => Diamond::minValue(), 'expire_date' => Diamond::maxValue(), 'limits' => [
                ['cash_begin' => 1.0, 'cash_end' => 100000.0, 'shareholding' => '0.0', 'surety' => '0', 'period' => 15],
            ]],
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $type) {
            $obj_type = new App\LoanType();
            $obj_type->name = $type['name'];
            $obj_type->rate = $type['rate'];
            $obj_type->start_date = $type['start_date'];
            $obj_type->expire_date = $type['expire_date'];
            $obj_type->save();

            foreach ($type['limits'] as $limit) {
                $obj_limit = new App\LoanTypeLimit();
                $obj_limit->cash_begin = $limit['cash_begin'];
                $obj_limit->cash_end = $limit['cash_end'];
                $obj_limit->shareholding = $limit['shareholding'];
                $obj_limit->surety = $limit['surety'];
                $obj_limit->period = $limit['period'];
                $obj_type->limits()->save($obj_limit);
            }
        }
    }
}
