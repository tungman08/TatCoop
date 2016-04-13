<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdministratorIdForAdministratorStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('administrator_statistics', function (Blueprint $table) {
            $table->integer('administrator_id')->unsigned()->after('id');
            $table->foreign('administrator_id')->references('id')
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
        Schema::table('administrator_statistics', function (Blueprint $table) {
            $table->dropForeign('administrator_statistics_administrator_id_foreign');
        });
    }
}
