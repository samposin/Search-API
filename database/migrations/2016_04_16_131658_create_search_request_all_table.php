<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchRequestAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_request_all', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dl_source');
            $table->string('sub_dl_source');
            $table->string('widget');
            $table->string('keyword');
            $table->string('domain');
            $table->string('ip');
            $table->string('user_country_code');
            $table->text('request_uri');
            $table->string('api_country_code');
            $table->string('api_used');
            $table->string('api_used_order');
            $table->string('category');
            $table->string('api_category');
            $table->integer('api_category_id');
            $table->string('http_user_agent');
            $table->string('browser');
            $table->string('configurator_unique_id');
            $table->text('msg');
            $table->tinyInteger('is_succeed')->default(0);
            $table->tinyInteger('is_completed')->default(0);
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
        Schema::drop('search_request_all');
    }
}
