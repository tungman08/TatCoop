<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareholdingAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shareholding_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file');
            $table->string('display');
            $table->timestamps();
        });

        Schema::table('shareholding_attachments', function (Blueprint $table) {
            $table->integer('shareholding_id')->unsigned()->after('id');
            $table->foreign('shareholding_id')->references('id')
                ->on('shareholdings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shareholding_attachments', function (Blueprint $table) {
            $table->dropForeign('shareholding_attachments_shareholding_id_foreign');
        });

        Schema::drop('shareholding_attachments');
    }
}
