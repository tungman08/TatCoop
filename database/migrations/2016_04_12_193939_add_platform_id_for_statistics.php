<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlatformIdForStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_statistics', function (Blueprint $table) {
            $table->integer('platform_id')->unsigned()->after('ip_address');
            $table->foreign('platform_id')->references('id')
                ->on('platforms')->onDelete('cascade');
        });

        Schema::table('administrator_statistics', function (Blueprint $table) {
            $table->integer('platform_id')->unsigned()->after('ip_address');
            $table->foreign('platform_id')->references('id')
                ->on('platforms')->onDelete('cascade');
        });

        Schema::table('user_statistics', function (Blueprint $table) {
            $table->integer('platform_id')->unsigned()->after('ip_address');
            $table->foreign('platform_id')->references('id')
                ->on('platforms')->onDelete('cascade');
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
            $table->dropForeign('visitor_statistics_platform_id_foreign');
        });

        Schema::table('administrator_statistics', function (Blueprint $table) {
            $table->dropForeign('administrator_statistics_platform_id_foreign');
        });

        Schema::table('user_statistics', function (Blueprint $table) {
            $table->dropForeign('user_statistics_platform_id_foreign');
        });
    }
}
