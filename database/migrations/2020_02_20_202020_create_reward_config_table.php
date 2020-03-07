<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_configs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('reward_id')->unsigned()->after('id');
            $table->foreign('reward_id')->references('id')
                ->on('rewards')->onDelete('cascade');

            $table->double('price');
            $table->integer('amount');
            $table->boolean('register');
            $table->boolean('special');
            $table->timestamps();
        });

        Schema::create('reward_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('winners', function (Blueprint $table) {
            $table->dropForeign('winners_member_id_foreign');
            $table->dropForeign('winners_reward_id_foreign');
            $table->renameColumn('reward_id', 'reward_config_id');
        });

        Schema::rename('winners', 'reward_winners');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('reward_winners', 'winners'); 

        Schema::table('winners', function (Blueprint $table) {
            $table->renameColumn('reward_config_id', 'reward_id');

            $table->foreign('reward_id')->references('id')
                ->on('rewards')->onDelete('cascade');
            $table->foreign('member_id')->references('id')
                ->on('members')->onDelete('cascade');
        });

        Schema::drop('reward_statuses');
        Schema::drop('reward_configs');
    }
}
