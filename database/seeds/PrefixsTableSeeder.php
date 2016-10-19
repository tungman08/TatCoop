<?php

use Illuminate\Database\Seeder;

class PrefixsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'นาย'],
            ['name' => 'นาง'],
            ['name' => 'น.ส.'],
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $prefix) {
            $obj = new App\Prefix($prefix);
            $obj->save();
        }
    }
}
