<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winners', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('reward_id')->unsigned();
            $table->foreign('reward_id')->references('id')
                ->on('rewards')->onDelete('cascade');

            $table->integer('member_id')->unsigned();
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->boolean('status')->default(false);
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
        Schema::table('winners', function (Blueprint $table) {
            $table->dropForeign('winners_reward_id_foreign');
            $table->dropForeign('winners_member_id_foreign');
        });

        Schema::drop('winners');
    }
}
