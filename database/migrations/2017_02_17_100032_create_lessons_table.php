<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->longText('description');
            $table->longText('featured_image')->nullable();
            $table->string('video_url');
            $table->string('bonus_video_url');
            $table->integer('lft')->default(0);

            $table->integer('module_id')->unsigned();
            $table->foreign('module_id')->references('id')->on('modules');

            $table->date('lock_date')->nullable();
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
        Schema::dropIfExists('lessons');
    }
}
