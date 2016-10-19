<?php

use Illuminate\Database\Seeder;

class EmployeeTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'พนักงาน'],
            ['name' => 'ลูกจ้าง ททท.'],
            ['name' => 'บุคคลภายนอก'],
        ];

        // Loop through each type above and create the record for them in the database
        foreach ($array as $types) {
            $obj = new App\EmployeeType($types);
            $obj->save();
        }
    }
}
