<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubdistrictIdForPostcodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postcodes', function (Blueprint $table) {
            $table->integer('subdistrict_id')->unsigned()->after('code');
            $table->foreign('subdistrict_id')->references('id')
                ->on('subdistricts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postcodes', function (Blueprint $table) {
            $table->dropForeign('postcodes_subdistrict_id_foreign');
        });
    }
}
