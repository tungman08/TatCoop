<?php

use Illuminate\Database\Seeder;

class BillingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['manager' => 'ศิลาชัย สุหร่าย', 'treasurer' => 'สุภัตร รัตนา']
        ];

        // Loop through each member above and create the record for them in the database
        foreach ($array as $billing) {
            $obj = new App\Billing($billing);
            $obj->save();
        }
    }
}
