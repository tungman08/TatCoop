<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBailsmenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bailsmen', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('employee_type_id')->unsigned()->nullable();
            $table->foreign('employee_type_id')->references('id')
                ->on('employee_types')->onDelete('cascade');

            $table->enum('self_type', ['shareholding', 'salary']);
            $table->float('self_rate');
            $table->double('self_maxguaruntee');
            $table->double('self_netsalary');
            $table->enum('other_type', ['shareholding', 'salary']);
            $table->float('other_rate');
            $table->double('other_maxguaruntee');
            $table->double('other_netsalary');
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
        Schema::table('bailsmen', function (Blueprint $table) {
            $table->dropForeign('bailsmen_employee_type_id_foreign');
        });

        Schema::drop('bailsmen');
    }
}
