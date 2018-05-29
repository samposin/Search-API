<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronExecutionInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_execution_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->tinyInteger('currency')->default(0)->comment = "If currency cron execute or not";
            $table->tinyInteger('reporting')->default(0)->comment = "If reporting cron execute or not";
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
        Schema::drop('cron_execution_info');
    }
}
