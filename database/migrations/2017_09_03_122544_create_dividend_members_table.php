<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDividendMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dividend_member', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('dividend_id')->unsigned();
            $table->foreign('dividend_id')->references('id')
                ->on('dividends')->onDelete('cascade');

            $table->integer('member_id')->unsigned();
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->string('dividend_name');
            $table->date('dividend_date');
            $table->double('shareholding');
            $table->double('shareholding_dividend');
            $table->double('interest');
            $table->double('interest_dividend');

            $table->timestamps();
        });

        Schema::table('dividends', function (Blueprint $table) {
            $table->date('release_date')->after('loan_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dividends', function (Blueprint $table) {
            $table->dropColumn('release_date');
        });

        Schema::table('dividend_members', function (Blueprint $table) {
            $table->dropForeign('dividend_members_dividend_id_foreign');
            $table->dropForeign('dividend_members_member_id_foreign');
        });

        Schema::drop('dividend_members');
    }
}
