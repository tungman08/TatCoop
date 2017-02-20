<?php

use Illuminate\Database\Seeder;

class CarouselsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (in_array('carousels', Storage::directories())) {
            Storage::deleteDirectory('carousels');
            Storage::makeDirectory('carousels');
        }
    }
}
