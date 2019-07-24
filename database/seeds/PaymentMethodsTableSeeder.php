<?php

use Illuminate\Database\Seeder;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['name' => 'ค่าชำระเงินกู้ปกติ'],
            ['name' => 'ค่าชำระเงินกู้เงินสด'],
        ];

        // Loop through each prefix above and create the record for them in the database
        foreach ($array as $payment_method) {
            $obj = new App\PaymentMethod($payment_method);
            $obj->save();
        }
    }
}
