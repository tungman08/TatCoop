<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file');
            $table->string('display');
            $table->timestamps();
        });

        Schema::table('payment_attachments', function (Blueprint $table) {
            $table->integer('payment_id')->unsigned()->after('id');
            $table->foreign('payment_id')->references('id')
                ->on('payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_attachments', function (Blueprint $table) {
            $table->dropForeign('payment_attachments_payment_id_foreign');
        });

        Schema::drop('payment_attachments');
    }
}
