<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_attachments', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')
                ->on('loans')->onDelete('cascade');

            $table->string('file');
            $table->string('display');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_attachments', function (Blueprint $table) {
            $table->dropForeign('loan_attachments_loan_id_foreign');
        });

        Schema::drop('loan_documents');
    }
}
