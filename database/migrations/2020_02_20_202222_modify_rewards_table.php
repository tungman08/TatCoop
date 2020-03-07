<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->integer('reward_status_id')->unsigned()->after('session');
            $table->foreign('reward_status_id')->references('id')
                ->on('reward_statuses')->onDelete('cascade');
        });

        Schema::create('member_reward', function (Blueprint $table) {
            $table->integer('member_id')->unsigned()->nullable();
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');

            $table->integer('reward_id')->unsigned()->nullable();
            $table->foreign('reward_id')->references('id')
                ->on('rewards')->onDelete('cascade');

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
        Schema::table('member_reward', function (Blueprint $table) {
            $table->dropForeign('member_reward_member_id_foreign');
            $table->dropForeign('member_reward_reward_id_foreign');
        });

        Schema::drop('member_reward');

        Schema::table('rewards', function (Blueprint $table) {
            $table->dropForeign('rewards_reward_status_id_foreign');
        });
    }
}
