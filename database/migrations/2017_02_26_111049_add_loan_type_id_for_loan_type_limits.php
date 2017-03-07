<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoanTypeIdForLoanTypeLimits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_type_limits', function (Blueprint $table) {
            $table->integer('loan_type_id')->unsigned()->after('id');
            $table->foreign('loan_type_id')->references('id')
                ->on('loan_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_type_limits', function (Blueprint $table) {
            $table->dropForeign('loan_type_limits_loan_type_id_foreign');
        });
    }
}
