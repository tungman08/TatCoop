<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutinePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routine_payment_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('routine_payment_id')->unsigned();
            $table->foreign('routine_payment_id')->references('id')
                ->on('routine_payments')->onDelete('cascade');

            $table->integer('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')
                ->on('loans')->onDelete('cascade');

            $table->boolean('status')->default(false);
            $table->date('pay_date');
            $table->integer('period')->unsigned();
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
        Schema::table('routine_payment_details', function (Blueprint $table) {
            $table->dropForeign('routine_payment_details_routine_payment_id_foreign');
            $table->dropForeign('routine_payment_details_loan_id_foreign');
        });
        
        Schema::drop('routine_payment_details');
    }
}
