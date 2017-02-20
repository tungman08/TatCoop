<?php

use Illuminate\Database\Seeder;

class HistoryTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'สร้างบัญชีผู้ใช้', 'icon' => 'fa-plus', 'color' => 'bg-light-blue'],
            ['name' => 'สร้างบัญชีผู้ดูแลระบบ', 'icon' => 'fa-plus', 'color' => 'bg-light-blue'],
            ['name' => 'เข้าสู่ระบบ', 'icon' => 'fa-unlock-alt', 'color' => 'bg-green'],
            ['name' => 'เปลี่ยนรหัสผ่าน', 'icon' => 'fa-key', 'color' => 'bg-fuchsia'],
            ['name' => 'ตั้งค่ารหัสผ่านใหม่', 'icon' => 'fa-key', 'color' => 'bg-lime'],
            ['name' => 'เพิ่มข้อมูล', 'icon' => 'fa-asterisk', 'color' => 'bg-purple'],
            ['name' => 'แก้ไขข้อมูล', 'icon' => 'fa-edit', 'color' => 'bg-teal'],
            ['name' => 'ลบข้อมูล', 'icon' => 'fa-trash', 'color' => 'bg-orange'],
            ['name' => 'คืนสภาพข้อมูล', 'icon' => 'fa-undo', 'color' => 'bg-blue'],
            ['name' => 'ลบข้อมูลอย่างถาวร', 'icon' => 'fa-times', 'color' => 'bg-red'],
            ['name' => 'สร้างข้อมูลสมาชิกใหม่', 'icon' => 'fa-user-plus', 'color' => 'bg-olive'],
            ['name' => 'บันทึกการลาออกของสมาชิก', 'icon' => 'fa-user-times', 'color' => 'bg-maroon'],
            ['name' => 'ลาออก', 'icon' => 'minus', 'color' => 'fa-bg-maroon'],
            ['name' => 'ป้อนการชำระค่าหุ้นแบบอัตโนมัติ', 'icon' => 'fa-clipboard', 'color' => 'bg-aqua'],
            ['name' => 'นำข้อมูลออก', 'icon' => 'fa-share-square-o', 'color' => 'bg-yellow']
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $type) {
            $obj = new App\HistoryType($type);
            $obj->save();
        }
    }
}
