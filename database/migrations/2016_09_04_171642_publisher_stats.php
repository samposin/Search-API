<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class PublisherStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('publisher_id')->unsigned();
            $table->integer('advertiser_id')->unsigned()->nullable(true);
            $table->date('date');
            $table->string('dl_source');
            $table->string('sub_dl_source');
            $table->string('country', 3);
            $table->string('widget', 128);
            $table->integer('searches');
            $table->integer('clicks');
            $table->decimal('revenue');
            $table->decimal('rate', 8, 3)->nullable(true);
            $table->timestamps();

            $table->unique(
                ['publisher_id', 'advertiser_id', 'date', 'dl_source', 'sub_dl_source', 'country', 'widget'],
                'advertiser_has_resource'
            );
            $table->foreign('publisher_id')->references('id')->on('publishers');
            $table->foreign('advertiser_id')->references('id')->on('advertisers');
            $table->engine = 'InnoDB';
        });

        Schema::table('advertiser_stats', function ($table) {
            $table->decimal('cts', 8, 3)->change();
            $table->decimal('revenue_usd')->change();
            $table->decimal('rate_usd', 8, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('publisher_stats');
    }
}
