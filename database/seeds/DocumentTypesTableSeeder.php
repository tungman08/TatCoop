<?php

use Illuminate\Database\Seeder;

class DocumentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['display' => 'ระเบียบ/คำสั่ง/ข้อบังคับ', 'name' => 'Rule'],
            ['display' => 'ใบสมัคร/แบบฟอร์มต่างๆ', 'name' => 'Form'],
            ['display' => 'เอกสารอื่นๆ', 'name' => 'Other'],
        ];

        // Loop through each type above and create the record for them in the database
        foreach ($array as $types) {
            $obj = new App\DocumentType($types);
            $obj->save();
        }
    }
}
