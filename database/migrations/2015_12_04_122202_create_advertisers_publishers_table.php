<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisersPublishersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisers_publishers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id')->unsigned()->index();
            $table->integer('publisher_id')->unsigned()->index();
            $table->string('publisher_id1',50);
            $table->decimal('share', 5, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('advertisers_publishers');
    }
}
