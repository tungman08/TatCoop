<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->date('loaned_at')->nullable();
            $table->double('outstanding')->nullable();
            $table->float('rate')->nullable();
            $table->integer('period')->nullable();
            $table->boolean('shareholding');
            $table->date('completed_at')->nullable();
            $table->integer('step');
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
        Schema::drop('loans');
    }
}
