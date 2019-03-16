<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'SuperAdmin'],
            ['name' => 'Administrator'],
            ['name' => 'Viewer'],
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $role) {
            $obj = new App\Role($role);
            $obj->save();
        }
    }
}
