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
            $table->string('name');
            $table->bigInteger('size');
            $table->integer('length')->unsigned();
            $table->string('type');
            $table->text('data');
            $table->text('transcript');
            $table->string('analysis');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('questionnaire_video', function (Blueprint $table) {
            $table->integer('questionnaire_id')->unsigned()->index();
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');
            $table->integer('video_id')->unsigned()->index();
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
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
        Schema::dropIfExists('videos');
        Schema::dropIfExists('questionnaire_video');
    }
}
