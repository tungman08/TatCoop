<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutineShareholdingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routine_shareholding_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('routine_shareholding_id')->unsigned();
            $table->foreign('routine_shareholding_id')->references('id')
                ->on('routine_shareholdings')->onDelete('cascade');

            $table->integer('member_id')->unsigned();
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->boolean('status')->default(false);
            $table->date('pay_date');
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
        Schema::table('routine_payment_details', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('pay_date');
            $table->dropColumn('status');
            $table->dropForeign('routine_shareholding_details_routine_shareholding_id_foreign');
            $table->dropForeign('routine_shareholding_details_member_id_foreign');
        });

        Schema::drop('routine_shareholding_details');
    }
}
