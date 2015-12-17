<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Analysis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analysis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_id')->unsigned()->index();
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->integer('block_id')->unsigned()->index();
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->integer('part')->unsigned()->index();
            $table->string('answer');
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
        Schema::dropIfExists('analysis');
    }
}
