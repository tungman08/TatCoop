<?php

use Illuminate\Database\Seeder;

class ThemesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'ฟ้า', 'code' => 'skin-blue'],
            ['name' => 'ขาว', 'code' => 'skin-black'],
            ['name' => 'เขียว', 'code' => 'skin-green'],
            ['name' => 'ม่วง', 'code' => 'skin-purple'],
            ['name' => 'แดง', 'code' => 'skin-red'],
            ['name' => 'เหลือง', 'code' => 'skin-yellow'],
            ['name' => 'ฟ้าสว่าง', 'code' => 'skin-blue-light'],
            ['name' => 'ขาวสว่าง', 'code' => 'skin-black-light'],
            ['name' => 'เขียวสว่าง', 'code' => 'skin-green-light'],
            ['name' => 'ม่วงสว่าง', 'code' => 'skin-purple-light'],
            ['name' => 'แดงสว่าง', 'code' => 'skin-red-light'],
            ['name' => 'เหลืองสว่าง', 'code' => 'skin-yellow-light'],
        ];
        // Loop through each theme above and create the record for them in the database
        foreach ($array as $theme) {
            $obj = new App\Theme($theme);
            $obj->save();
        }
    }
}
