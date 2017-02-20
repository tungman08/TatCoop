<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHistoryTypeIdForUserHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_histories', function (Blueprint $table) {
            $table->integer('history_type_id')->unsigned()->after('id');
            $table->foreign('history_type_id')->references('id')
                ->on('history_types')->onDelete('cascade');

            $table->integer('user_id')->unsigned()->after('id');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_histories', function (Blueprint $table) {
            $table->dropForeign('user_histories_history_type_id_foreign');
            $table->dropForeign('user_histories_user_id_foreign');
        });
    }
}
