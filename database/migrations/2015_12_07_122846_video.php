<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Video extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_id')->unsigned()->nullable()->index();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('questionaire_id')->unsigned()->nullable()->index();
            $table->foreign('questionaire_id')->references('id')->on('questionaires')->onDelete('set null');
            $table->string('name');
            $table->bigInteger('size');
            $table->string('type');
            $table->text('data');
            $table->text('transcript');
            $table->string('analysis');
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
        Schema::dropIfExists('videos');
    }
}
