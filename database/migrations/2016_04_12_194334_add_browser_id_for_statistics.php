<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrowserIdForStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_statistics', function (Blueprint $table) {
            $table->integer('browser_id')->unsigned()->after('ip_address');
            $table->foreign('browser_id')->references('id')
                ->on('browsers')->onDelete('cascade');
        });

        Schema::table('administrator_statistics', function (Blueprint $table) {
            $table->integer('browser_id')->unsigned()->after('ip_address');
            $table->foreign('browser_id')->references('id')
                ->on('browsers')->onDelete('cascade');
        });

        Schema::table('user_statistics', function (Blueprint $table) {
            $table->integer('browser_id')->unsigned()->after('ip_address');
            $table->foreign('browser_id')->references('id')
                ->on('browsers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_statistics', function (Blueprint $table) {
            $table->dropForeign('visitor_statistics_browser_id_foreign');
        });

        Schema::table('administrator_statistics', function (Blueprint $table) {
            $table->dropForeign('administrator_statistics_browser_id_foreign');
        });

        Schema::table('user_statistics', function (Blueprint $table) {
            $table->dropForeign('user_statistics_browser_id_foreign');
        });
    }
}
