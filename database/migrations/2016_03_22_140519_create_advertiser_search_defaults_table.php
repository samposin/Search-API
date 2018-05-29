<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertiserSearchDefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser_search_defaults', function (Blueprint $table) {
            $table->increments('id');
            $table->string('geo',10);
            $table->string('main_api',50);
            $table->string('first_backfill_api',50);
            $table->string('second_backfill_api',50);
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
        Schema::drop('advertiser_search_defaults');
    }
}
