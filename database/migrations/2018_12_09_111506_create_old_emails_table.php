<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('remark');
            $table->timestamps();
        });

        Schema::table('old_emails', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->after('id');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('newaccount')->default(false)->after('confirmed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('newaccount');
        });

        Schema::table('old_emails', function (Blueprint $table) {
            $table->dropForeign('old_emails_users_id_foreign');
        });

        Schema::drop('old_emails');
    }
}
