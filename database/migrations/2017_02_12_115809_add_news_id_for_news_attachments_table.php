<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewsIdForNewsAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news_attachments', function (Blueprint $table) {
            $table->integer('news_id')->unsigned()->after('id');
            $table->foreign('news_id')->references('id')
                ->on('newses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_attachments', function (Blueprint $table) {
            $table->dropForeign('news_attachments_news_id_foreign');
        });
    }
}
