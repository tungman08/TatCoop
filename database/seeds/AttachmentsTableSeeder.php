<?php

use Illuminate\Database\Seeder;

class AttachmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (in_array('attachments', Storage::directories())) {
            Storage::deleteDirectory('attachments');
            Storage::makeDirectory('attachments');
        }
    }
}
