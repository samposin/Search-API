<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchClickReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_click_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('total_search')->default(0);
            $table->integer('total_search_advertiser_call')->default(0);
            $table->integer('total_search_advertiser_call_with_result')->default(0);
            $table->integer('total_viewed')->default(0);
            $table->integer('total_clicked')->default(0);
            $table->string('dl_source');
            $table->string('sub_dl_source');
            $table->string('widget');
            $table->string('country_code',50);
            $table->string('api',50);
            $table->string('jsver',50);
            $table->string('browser',50);
            $table->date('date');
            $table->tinyInteger('hour_int');
            $table->string('hour_display',50);
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
        Schema::drop('search_click_report');
    }
}
