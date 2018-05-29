<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchClicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_clicks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('search_id')->unsigned();
            $table->string('api');
            $table->string('dl_source');
            $table->string('sub_dl_source');
            $table->string('widget');
            $table->string('domain');
            $table->string('country_code');
            $table->string('clicks')->default(1);
            $table->date('date');
            $table->string('jsver');
            $table->string('category');
            $table->string('api_category');
            $table->integer('api_category_id');
            $table->string('keyword');
            $table->string('ip');
            $table->string('http_user_agent');
            $table->string('browser');
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
        Schema::drop('search_clicks');
    }
}
