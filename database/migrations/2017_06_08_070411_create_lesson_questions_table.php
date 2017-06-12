<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');

            $table->string('video_url');
            $table->string('video_title');
            $table->longText('description');
            $table->longText('featured_image')->nullable();

            $table->integer('lft')->default(0);

            $table->string('outer_url')->nullable();
            $table->string('assessment_id')->nullable();

            $table->integer('lesson_id')->unsigned()->nullable();
            $table->foreign('lesson_id')->references('id')->on('lessons');

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
        Schema::dropIfExists('lesson_questions');
    }
}
