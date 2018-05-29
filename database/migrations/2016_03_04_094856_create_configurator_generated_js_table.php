<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfiguratorGeneratedJsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurator_generated_js', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('publisher_id')->unsigned();
            $table->string('configurator_unique_id');
            $table->string('piwik_website_id');
            $table->string('piwik_website_name');
            $table->string('partner_name');
            $table->string('product_name');
            $table->string('product_sub_id');
            $table->integer('publisher_configurator_generated_js_no');
            $table->tinyInteger('is_delete')->default(0);
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
        Schema::drop('configurator_generated_js');
    }
}
