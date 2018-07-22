<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_sureties', function (Blueprint $table) {
            $table->dropForeign('loan_sureties_loan_id_foreign');
            $table->dropForeign('loan_sureties_member_id_foreign');
        });

        Schema::drop('loan_sureties');

        Schema::create('loan_member', function (Blueprint $table) {
            $table->integer('loan_id')->unsigned()->nullable();
            $table->foreign('loan_id')->references('id')
                ->on('loans')->onDelete('cascade');

            $table->integer('member_id')->unsigned()->nullable();
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->double('salary');
            $table->double('amount');
            $table->boolean('yourself');
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
        Schema::table('loan_member', function (Blueprint $table) {
            $table->dropForeign('loan_member_loan_id_foreign');
            $table->dropForeign('loan_member_member_id_foreign');
        });

        Schema::drop('loan_member');

        Schema::create('loan_sureties', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_id')->unsigned()->nullable();
            $table->foreign('loan_id')->references('id')
                ->on('loans')->onDelete('cascade');

            $table->integer('member_id')->unsigned()->nullable();
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->double('amount');
            $table->boolean('yourself');
            $table->timestamps();
        });
    }
}
