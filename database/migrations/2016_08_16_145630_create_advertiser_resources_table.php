<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertiserResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser_resource', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->timestamps();
        });

        Schema::create('advertiser_has_resource', function (Blueprint $table) {
            $table->integer('advertiser_id')->unsigned();
            $table->integer('advertiser_resource_id')->unsigned();
            $table->text('options');
            $table->string('name', 128);
            $table->timestamps();
            $table->unique(['advertiser_id', 'advertiser_resource_id'], 'advertiser_has_resource');
            $table->foreign('advertiser_id')->references('id')->on('advertisers');
            $table->foreign('advertiser_resource_id')->references('id')->on('advertiser_resource');
        });

        Schema::create('advertiser_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->integer('advertiser_id')->unsigned();
            $table->string('dl_source');
            $table->string('sub_dl_source');
            $table->string('country', 3);
            $table->string('currency', 5)->nullable(false)->defult('USD');
            $table->integer('clicks');
            $table->integer('leads')->nullable(false)->default(0);
            $table->float('estimated_revenue')->nullable(false)->default(0.00);
            $table->float('revenue_usd')->nullable(false)->default(0.00);
            $table->float('rate_usd')->nullable(false)->default(0.00);
            $table->float('cpc');
            $table->float('cts')->comment('cost to sale');
            $table->timestamps();

            $table->foreign('advertiser_id')->references('id')->on('advertisers');
        });

        Schema::table('currency_exchange_rates', function (Blueprint $table) {
            $table->index(['date', 'from_currency', 'to_currency'], 'currency_exchange_rates_ckey1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('advertiser_has_resource');
        Schema::drop('advertiser_resource');
        Schema::drop('advertiser_stats');
        Schema::table('currency_exchange_rates', function ($table) {
            $table->dropIndex(['currency_exchange_rates_ckey1']);
        });
    }
}
