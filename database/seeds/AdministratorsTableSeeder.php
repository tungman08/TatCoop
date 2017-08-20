<?php

use Illuminate\Database\Seeder;

class AdministratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'Administrator', 'email' => 'admin@tatcoop.com', 'password' => 't@tCo0p$02042528', 'password_changed' => true],
        ];

        // Loop through each administrator above and create the record for them in the database
        foreach ($array as $admin) {
            $obj = new App\Administrator($admin);
            $obj->save();

            History::addAdminHistory($obj->id, 'สร้างบัญชีผู้ดูแลระบบ');
        }
    }
}
