<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClicksAdvertiserInternalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clicks_advertiser_internal', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->string('api',50);
            $table->integer('api_clicks')->default(0);
            $table->integer('internal_clicks')->default(0);
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
        Schema::drop('clicks_advertiser_internal');
    }
}
