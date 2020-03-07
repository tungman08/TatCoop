<?php

use Illuminate\Database\Seeder;

class RewardStatussTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'เริ่มต้น'],
            ['name' => 'รอลงทะเบียน'],
            ['name' => 'พร้อมใช้งาน'],
            ['name' => 'เสร็จสิ้น'],
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $status) {
            $obj = new App\RewardStatus($status);
            $obj->save();
        }
    }
}
