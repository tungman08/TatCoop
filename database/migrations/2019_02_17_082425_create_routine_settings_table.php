<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutineSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routine_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('name', ['shareholding', 'payment']);
            $table->boolean('calculate_status')->default(false);
            $table->boolean('approve_status')->default(false);
            $table->boolean('save_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('routine_settings');
    }
}
