<?php

use Illuminate\Database\Seeder;

class BailsmansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [
                'employee_type_id' => 1,
                'self_type' => 'shareholding', 'self_rate' => 0.9, 'self_maxguaruntee' => 1200000, 'self_netsalary' => 0,
                'other_type' => 'salary', 'other_rate' => 40.0, 'other_maxguaruntee' => 1200000, 'other_netsalary' => 1000,
            ],
            [
                'employee_type_id' => 2, 
                'self_type' => 'shareholding', 'self_rate' => 0.8, 'self_maxguaruntee' => 1200000, 'self_netsalary' => 0,
                'other_type' => 'shareholding', 'other_rate' => 0.8, 'other_maxguaruntee' => 1200000, 'other_netsalary' => 0,
            ]
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $prefix) {
            $obj = new App\Prefix($prefix);
            $obj->save();
        }
    }
}
