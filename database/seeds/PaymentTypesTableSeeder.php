<?php

use Illuminate\Database\Seeder;

class PaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'ผ่อนชำระแบบคงยอด'],
            ['name' => 'ผ่อนชำระแบบคงต้น'],
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $payment_type) {
            $obj = new App\PaymentType($payment_type);
            $obj->save();
        }
    }
}
