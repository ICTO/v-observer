<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Questionaire extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionaires', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('interval')->unsigned();
            $table->integer('creator_id')->unsigned()->nullable()->index();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('owner_id')->unsigned()->nullable()->index();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('questionaire_id')->unsigned()->index();
            $table->foreign('questionaire_id')->references('id')->on('questionaires')->onDelete('cascade');
            $table->integer('parent_id')->unsigned()->nullable()->index();
            $table->foreign('parent_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->string('type');
            $table->integer('order')->unsigned();
            $table->text('data');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('questions');
        Schema::dropIfExists('blocks');
    }
}
