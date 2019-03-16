<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutinePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routine_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('status')->default(false);
            $table->date('calculated_date');
            $table->date('approved_date');
            $table->date('saved_date');
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
        Schema::drop('routine_payments');
    }
}
