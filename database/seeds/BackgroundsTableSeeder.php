<?php

use Illuminate\Database\Seeder;

class BackgroundsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (in_array('backgrounds', Storage::directories())) {
            Storage::deleteDirectory('backgrounds');
            Storage::makeDirectory('backgrounds');
        }
    }
}
