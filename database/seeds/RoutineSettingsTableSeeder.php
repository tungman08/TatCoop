<?php

use Illuminate\Database\Seeder;

class RoutineSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'shareholding', 'calculate_status' => false, 'save_status' => false],
            ['name' => '[payment]', 'calculate_status' => false, 'save_status' => false]
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $setting) {
            $obj = new App\RoutineSetting($setting);
            $obj->save();
        }
    }
}
