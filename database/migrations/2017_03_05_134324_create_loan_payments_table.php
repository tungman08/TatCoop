<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')
                ->on('loans')->onDelete('cascade');

            $table->date('pay_date');
            $table->double('principle');
            $table->double('interest');
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
        Schema::table('loan_payments', function (Blueprint $table) {
            $table->dropForeign('loan_payments_loan_id_foreign');
        });

        Schema::drop('loan_payments');
    }
}
