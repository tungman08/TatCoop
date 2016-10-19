<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShareholdingTypeIdForShareholdings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shareholdings', function (Blueprint $table) {
            $table->integer('shareholding_type_id')->unsigned()->after('pay_date');
            $table->foreign('shareholding_type_id')->references('id')
                ->on('shareholding_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shareholdings', function (Blueprint $table) {
            $table->dropForeign('shareholdings_shareholding_type_id_foreign');
        });
    }
}
