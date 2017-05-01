<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoanTypeIdToLoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->integer('loan_type_id')->unsigned()->after('id');
            $table->foreign('loan_type_id')->references('id')
                ->on('loan_types')->onDelete('cascade');

            $table->integer('member_id')->unsigned()->after('id');
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->integer('payment_type_id')->unsigned()->after('loan_type_id');
            $table->foreign('payment_type_id')->references('id')
                ->on('payment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign('loans_payment_type_id_foreign');
            $table->dropForeign('loans_member_id_foreign');
            $table->dropForeign('loans_loan_type_id_foreign');
        });
    }
}
