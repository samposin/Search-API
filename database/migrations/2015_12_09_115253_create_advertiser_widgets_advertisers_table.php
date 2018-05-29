<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertiserWidgetsAdvertisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser_widgets_advertisers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id')->unsigned()->index();
            $table->integer('advertiser_widget_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('advertiser_widgets_advertisers');
    }
}
