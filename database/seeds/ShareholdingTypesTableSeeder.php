<?php

use Illuminate\Database\Seeder;

class ShareholdingTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'ค่าหุ้นปกติ'],
            ['name' => 'ค่าหุ้นเงินสด'],
        ];

        // Loop through employee above and create the record for them in the database
        foreach ($array as $type) {
            $obj = new App\ShareholdingType($type);
            $obj->save();
        }
    }
}
