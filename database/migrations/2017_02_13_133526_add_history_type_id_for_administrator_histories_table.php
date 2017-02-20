<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHistoryTypeIdForAdministratorHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('administrator_histories', function (Blueprint $table) {
            $table->integer('history_type_id')->unsigned()->after('id');
            $table->foreign('history_type_id')->references('id')
                ->on('history_types')->onDelete('cascade');

            $table->integer('admin_id')->unsigned()->after('id');
            $table->foreign('admin_id')->references('id')
                ->on('administrators')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('administrator_histories', function (Blueprint $table) {
            $table->dropForeign('administrator_histories_history_type_id_foreign');
            $table->dropForeign('administrator_histories_admin_id_foreign');
        });
    }
}
