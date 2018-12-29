<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->float('rate')->nullable();
            $table->float('employee_ratesalary')->nullable();
            $table->double('employee_netsalary')->nullable();
            $table->float('outsider_rateshareholding')->nullable();
            $table->double('max_loansummary')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan_types');
    }
}
