<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('widget');
            $table->string('keyword');
            $table->string('domain');
            $table->string('ip');
            $table->text('request_uri');
            $table->string('http_user_agent');
			$table->tinyInteger('is_viewed')->default(0);
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
        Schema::drop('search_info');
    }
}
