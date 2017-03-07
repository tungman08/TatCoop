<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanSuretiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_sureties', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_id')->unsigned()->nullable();
            $table->foreign('loan_id')->references('id')
                ->on('loans')->onDelete('cascade');

            $table->integer('member_id')->unsigned()->nullable();
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->boolean('myself')->default(false);
            $table->double('amount');
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
        Schema::table('loan_sureties', function (Blueprint $table) {
            $table->dropForeign('loan_sureties_loan_id_foreign');
        });

        Schema::table('loan_sureties', function (Blueprint $table) {
            $table->dropForeign('loan_sureties_member_id_foreign');
        });

        Schema::drop('loan_sureties');
    }
}
