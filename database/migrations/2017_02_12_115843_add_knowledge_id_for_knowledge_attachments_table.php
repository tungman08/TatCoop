<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKnowledgeIdForKnowledgeAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('knowledge_attachments', function (Blueprint $table) {
            $table->integer('knowledge_id')->unsigned()->after('id');
            $table->foreign('knowledge_id')->references('id')
                ->on('knowledges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('knowledge_attachments', function (Blueprint $table) {
            $table->dropForeign('knowledge_attachments_knowledge_id_foreign');
        });
    }
}
