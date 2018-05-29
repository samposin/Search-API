<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisionapiReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visionapi_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->string('dl_source');
            $table->string('sub_dl_source');
            $table->string('widget');
            $table->string('country');
            $table->string('searches');
            $table->integer('clicks')->default(0);
            $table->decimal('estimated_revenue', 20, 10);
            $table->string('symbol');
            $table->tinyInteger('file_output_type');
            $table->string('advertiser_name');
            $table->tinyInteger('is_estimated')->default(0);
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
        Schema::drop('visionapi_reports');
    }
}
