<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostcodeIdForSubdistricts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subdistricts', function (Blueprint $table) {
            $table->integer('postcode_id')->unsigned()->after('name');
            $table->foreign('postcode_id')->references('id')
                  ->on('postcodes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subdistricts', function (Blueprint $table) {
            $table->dropForeign('subdistricts_postcode_id_foreign');
        });
    }
}
