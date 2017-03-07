<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTypeLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_type_limits', function (Blueprint $table) {
            $table->increments('id');
            $table->double('cash_begin');
            $table->double('cash_end');
            $table->double('shareholding');
            $table->string('surety');
            $table->integer('period');
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
        Schema::drop('loan_type_limits');
    }
}
