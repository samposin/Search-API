<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WikiSaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wiki_save', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->string('title');
            $table->string('description');
            $table->string('keyword');
            $table->string('category');
            $table->string('date');
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
        Schema::drop('wiki_save');
    }
}
